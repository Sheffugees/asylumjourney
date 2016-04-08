<?php

namespace AppBundle\Command;

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
        $doctrine = $this->getContainer()->get('doctrine');
        $manager = $doctrine->getManager();

        $cmd = $manager->getClassMetadata('AppBundle\\Entity\\Service');
        $connection = $manager->getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM '.$cmd->getTableName());
            $connection->query('ALTER TABLE '.$cmd->getTableName().' AUTO_INCREMENT = 1');
            // Beware of ALTER TABLE here--it's another DDL statement and will cause
            // an implicit commit.
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }

        $manager->persist(
            new Service(
                "me",
                "Everywhere",
                "This is a service",
                new \DateTimeImmutable("2011-01-01T00:00:00+0000"),
                new \DateTimeImmutable("2011-01-01T00:00:00+0000"),
                "My Service",
                "s1 2ns",
                "Someone"
            )
        );

        $manager->persist(
            new Service(
                "Me",
                "Some places",
                "The description",
                new \DateTimeImmutable("2011-01-01T00:00:00+0000"),
                new \DateTimeImmutable("2011-01-01T00:00:00+0000"),
                "My Service",
                "s11 7rd",
                "Me"
            )
        );
        $manager->flush();
        $output->writeln("Fixtures Created");
    }
}
