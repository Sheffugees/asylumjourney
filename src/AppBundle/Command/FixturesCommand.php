<?php

namespace AppBundle\Command;

use AppBundle\Entity\Provider;
use AppBundle\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:fixtures')
            ->setDescription('Load fixture data used by Dredd');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addServices($output);
        $this->addProviders($output);
        $output->writeln("Fixtures Created");
    }

    private function addServices()
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

        $service = new Service();
        $service->setName("My Service");
        $service->setDescription("This is a service");
        $service->setDataMaintainer("me");
        $service->setEndDate(new \DateTime("2018-01-01T00:00:00+0000"));

        $service->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(1));
        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(1));
        $service->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(2));
        $service->addProvider($manager->getRepository('AppBundle\\Entity\\Provider')->find(1));
        $service->addProvider($manager->getRepository('AppBundle\\Entity\\Provider')->find(2));
        $service->addServiceUser($manager->getRepository('AppBundle\\Entity\\ServiceUser')->find(2));
        $service->addServiceUser($manager->getRepository('AppBundle\\Entity\\ServiceUser')->find(3));
        $service->addIssue($manager->getRepository('AppBundle\\Entity\\Issue')->find(2));

        $manager->persist($service);

        $service2 = new Service();
        $service2->setName("Another Service");
        $service2->setDescription("This is a another service");
        $service2->setDataMaintainer("someone else");
        $service2->setEndDate(new \DateTime("2018-01-01T00:00:00+0000"));

        $service2->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(6));
        $service2->addCategory($manager->getRepository('AppBundle\\Entity\\Category')->find(7));
        $service2->addStage($manager->getRepository('AppBundle\\Entity\\Stage')->find(3));
        $service2->addProvider($manager->getRepository('AppBundle\\Entity\\Provider')->find(2));
        $service2->addServiceUser($manager->getRepository('AppBundle\\Entity\\ServiceUser')->find(1));
        $service2->addIssue($manager->getRepository('AppBundle\\Entity\\Issue')->find(3));

        $manager->persist($service2);
        $manager->flush();
    }

    private function addProviders()
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $manager = $doctrine->getManager();

        $cmd = $manager->getClassMetadata('AppBundle\\Entity\\Provider');
        $connection = $manager->getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM ' . $cmd->getTableName());
            $connection->query('ALTER TABLE ' . $cmd->getTableName() . ' AUTO_INCREMENT = 1');
            // Beware of ALTER TABLE here--it's another DDL statement and will cause
            // an implicit commit.
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }

        $provider = new Provider();
        $provider->setName("A provider");
        $provider->setDescription("This is a Provider");
        $provider->setPhone("0114 2222222");
        $provider->setEmail("jeff@example.com");
        $provider->setWebsite("http://www.provider.example.com");
        $provider->setContactName("Jeff Bdager");
        $provider->setAddress("Badger House");
        $provider->setPostcode("S1 2NS");

        $manager->persist($provider);

        $provider = new Provider();
        $provider->setName("Another provider");
        $provider->setDescription("This is another Provider");
        $provider->setPhone("0114 11111111");
        $provider->setEmail("barry@example.com");
        $provider->setWebsite("http://www.another-provider.example.com");
        $provider->setContactName("Barry Bdager");
        $provider->setAddress("Badger Tower");
        $provider->setPostcode("S11 8QD");

        $manager->persist($provider);
        $manager->flush();
    }
}
