<?php

namespace Hslavich\SimplesamlphpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Hslavich\SimplesamlphpBundle\Command
 *
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
class SyncMetadataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('simplesamlphp:sync-metadata')
            ->setDescription('Sync the metadata for the simplesaml connection')
            ->setHelp('Sync the metadata for the simplesaml connection')
            ->addArgument('metadata-url', InputArgument::REQUIRED, 'Url for the metadata XML')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $simpleSamlDir = sprintf(
            '%s/../vendor/simplesamlphp/simplesamlphp',
            $this->getContainer()->getParameter('kernel.root_dir')
        );

        $this->setUp($output, $simpleSamlDir);

        $metadataUrl = $input->getArgument('metadata-url');
        $command = sprintf('%s/modules/metarefresh/bin/metarefresh.php -s %s', $simpleSamlDir, $metadataUrl);

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $output->writeln(sprintf('<error>Could not parse the XML metadata from %s</error>', $metadataUrl));
            exit(1);
        }

        // write the parsed metadata to a php file in de simplesaml directory
        $parsedMetadata = $process->getOutput();
        $configFileContent = sprintf('<?php %s', $parsedMetadata);
        $configFileLocation = sprintf('%s/metadata/saml20-idp-remote.php', $simpleSamlDir);

        $fs = new Filesystem();
        $fs->dumpFile($configFileLocation, $configFileContent);
        exit(0);
    }

    /**
     * Set up the application
     *
     * @param OutputInterface $output
     * @param string $simpleSamlDir
     * @return bool
     */
    private function setUp(OutputInterface $output, $simpleSamlDir)
    {
        // copy the simplesaml config, just in case
        $this->getApplication()->find('simplesamlphp:config')->run(new ArrayInput([]), $output);

        $fs = new Filesystem();

        // create the enable file, to be able to use the metadata parser
        $enableFile = sprintf('%s/modules/metarefresh/enable', $simpleSamlDir);
        if (!$fs->exists($enableFile)) {
            $fs->touch($enableFile);
        }

        return true;
    }
}