<?php

namespace AppBundle\Command;

use AppBundle\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CsvImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:csv-import')
            ->setDescription('Import CSV data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $manager = $doctrine->getManager();

        $cmd = $manager->getClassMetadata('AppBundle\\Entity\\Service');
        $connection = $manager->getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM ' . $cmd->getTableName());
            $connection->query('DELETE FROM service_stage');
            $connection->query('DELETE FROM service_issue');
            $connection->query('DELETE FROM service_provider');
            $connection->query('DELETE FROM service_category');
            $connection->query('DELETE FROM service_service_user');
            $connection->query('ALTER TABLE ' . $cmd->getTableName() . ' AUTO_INCREMENT = 1');
            // Beware of ALTER TABLE here--it's another DDL statement and will cause
            // an implicit commit.
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }

        $file = new \SplFileObject(__DIR__ . '/../../../trello.csv');
        $file->setFlags(\SplFileObject::READ_CSV);

        foreach ($file as $row) {
            $service = new Service($row[1], $row[3], "", new \DateTimeImmutable("+ 10 years"));

            switch ($row[15]) {
                case "Arrival":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(1));
                    break;
                case "Awaiting Decision":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(2));
                    break;
                case "Positive Decision":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(3));
                    break;
                case "Negative Decision under appeal - Section 4":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(4));
                    break;
                case "Negative Decision - Destitute":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(5));
                    break;
                case "Gateway Protection programme (Refugee Council)":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(6));
                    break;

            }

            foreach (explode(',', $row[4]) as $label) {
                switch (trim($label)) {
                    case "Children":
                        $service->addServiceUser($manager->getRepository('AppBundle\\Entity\\ServiceUser')->find(3));
                        break;
                    case "Family":
                        $service->addServiceUser($manager->getRepository('AppBundle\\Entity\\ServiceUser')->find(2));
                        break;
                    case "Vulnerable adults":
                        $service->addServiceUser($manager->getRepository('AppBundle\\Entity\\ServiceUser')->find(4));
                        break;
                    case "Single Male":
                        $service->addServiceUser($manager->getRepository('AppBundle\\Entity\\ServiceUser')->find(5));
                        break;
                    case "Single Female":
                        $service->addServiceUser($manager->getRepository('AppBundle\\Entity\\ServiceUser')->find(6));
                        break;

                    case "Needing further support/Under-resourced (blue)":
                        $service->addIssue($manager->getRepository('AppBundle\\Entity\\Issue')->find(1));
                        break;
                    case "Gap in provision (pink)":
                        $service->addIssue($manager->getRepository('AppBundle\\Entity\\Issue')->find(2));
                        break;
                    case "Needing Immediate Action (purple)":
                        $service->addIssue($manager->getRepository('AppBundle\\Entity\\Issue')->find(3));
                        break;

                    case "Right to Remain/Positive Decision (green)":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(3));
                        break;
                    case "Arrival (red)":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(1));
                        break;
                    case "Awaiting Decision (yellow)":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(2));
                        break;
                    case "Negative Decision Under Appeal  S4  (sky)":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(4));
                        break;
                    case "Negative Decision/Destitute (orange)":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(5));
                        break;

                    case "Education":
                        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(1));
                        break;
                    case "Housing":
                        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(3));
                        break;
                    case "Health & Social Care":
                        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(2));
                        break;
                    case "Finance":
                        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(4));
                        break;
                    case "Social & Community":
                        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(6));
                        break;
                    case "Asylum Process Advice & Legal":
                        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(5));
                        break;
                    case "Resources e.g. clothes and food":
                        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(7));
                        break;

                    default:
                        var_dump($label);


                }
            }


            $manager->persist($service);
        }
        $manager->flush();

        $output->writeln("CSV Imported");
    }
}
