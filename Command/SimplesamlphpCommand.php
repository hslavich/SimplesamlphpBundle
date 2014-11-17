<?php

namespace Hslavich\SimplesamlphpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SimplesamlphpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('simplesamlphp:config')
            ->setDescription('Copy configuration files to simplesamlphp directory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();

        $configPath = $rootDir.'/config/simplesamlphp';
        $targetPath = $rootDir.'/../vendor/simplesamlphp/simplesamlphp';

        $output->writeLn("Copying simplesamlphp config files");
        $filesystem->mirror($configPath, $targetPath);
    }
}
