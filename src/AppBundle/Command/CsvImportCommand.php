<?php

namespace AppBundle\Command;

use AppBundle\Entity\Provider;
use AppBundle\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CsvImportCommand extends ContainerAwareCommand
{
    private $providers;

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
            $connection->query('DELETE FROM provider');
            $connection->query('DELETE FROM service_stage');
            $connection->query('DELETE FROM service_issue');
            $connection->query('DELETE FROM service_provider');
            $connection->query('DELETE FROM service_category');
            $connection->query('DELETE FROM service_service_user');
            $connection->query('ALTER TABLE ' . $cmd->getTableName() . ' AUTO_INCREMENT = 1');
            $connection->query('ALTER TABLE provider AUTO_INCREMENT = 1');
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

            // Skip header row or empty row
            if ($row[0] == "Card ID" || $row[0] == null) continue;

            $matches = [];
            $name = $row[1];
            if (preg_match('/(.*)\((.*)\)/', $row[1], $matches)) {
                $name = trim($matches[1]);
                $providerName = $matches[2];

                if(!isset($this->providers[$providerName])) {
                    $provider = new Provider($providerName);
                    $manager->persist($provider);
                    $this->providers[$providerName] = $provider;
                }
            }

            $service = new Service($name, $row[3], null, null);
            if(isset($providerName)) {
                $service->addProvider($this->providers[$providerName]);
            }

            $output->writeln("---");
            $output->writeln("Processing " . $name);

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
                case "Positive decision - no recourse to public funds":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(5));
                    break;
                case "Negative Decision - with state support (e.g. section 4)":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(5));
                    break;
                case "Negative Decision - Destitute":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(6));
                    break;
                case "Gateway Protection programme (Refugee Council)":
                    $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(7));
                    break;
                default:
                    $output->writeln("Skipping because list is " . $row[15]);
                    continue 2;
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
                    case "Positive no recourse (black)":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(4));
                        break;
                    case "Negative Decision with state support  e.g section 4  (sky)":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(5));
                        break;
                    case "Negative Decision/Destitute (orange)":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(6));
                        break;
                    case "Gateway":
                        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(7));
                        break;

                    case "Education":
                        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(1));
                        break;
                    case "Accommodation":
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
                        $output->writeln('Unhandled label - '. $label);


                }
            }


            $manager->persist($service);
            $output->writeln("OK");
        }
        $manager->flush();

        $output->writeln("CSV Imported");
    }
}
