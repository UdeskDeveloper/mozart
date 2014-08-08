<?php

namespace Mozart\Bundle\NucleusBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Command that places twitter bootstrap files into a given directory.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwitterBootstrapInstallCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
                ->setName('twbs:install')
                ->setDefinition(array(
                    new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
                ))
                ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlinks the assets instead of copying it')
                ->addOption('relative', null, InputOption::VALUE_NONE, 'Make relative symlinks')
                ->setDescription('Installs bundles web assets under a public web directory')
                ->setHelp(<<<EOT
The <info>%command.name%</info> command installs Twitter Bootstrap files into a given
directory (e.g. the web directory).

<info>php %command.full_name% web</info>

A "twbs" directory will be created inside the target directory, and "bootstrap"
    and "bootstrap-sass" directories of Twitter Bootstrap will be copied into it.

To create a symlink to each bundle instead of copying its assets, use the
<info>--symlink</info> option:

<info>php %command.full_name% web --symlink</info>

To make symlink relative, add the <info>--relative</info> option:

<info>php %command.full_name% web --symlink --relative</info>

EOT
                )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException When the target directory does not exist or symlink cannot be used
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetArg = rtrim($input->getArgument('target'), '/');

        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        if (!function_exists('symlink') && $input->getOption('symlink')) {
            throw new \InvalidArgumentException('The symlink() function is not available on your system. You need to install the assets without the --symlink option.');
        }

        $filesystem = $this->getContainer()->get('filesystem');

        // Create the bundles directory otherwise symlink will fail.
        $targetDir = $targetArg . '/twbs/';
        $filesystem->mkdir($targetDir, 0777);

        $output->writeln(sprintf("Installing Twitter Bootstrap files using the <comment>%s</comment> option", $input->getOption('symlink') ? 'symlink' : 'hard copy'));

        if (is_dir($originDir = $this->getContainer()->get('kernel')->getRootDir() . '/../vendor/twbs/')) {

            $output->writeln(sprintf('Installing Twitter Bootstrap into <comment>%s</comment>', $targetDir));

            $filesystem->remove($targetDir);

            if ($input->getOption('symlink')) {
                if ($input->getOption('relative')) {
                    $relativeOriginDir = $filesystem->makePathRelative($originDir, realpath($targetDir));
                } else {
                    $relativeOriginDir = $originDir;
                }
                $filesystem->symlink($relativeOriginDir, $targetDir);
            } else {
                $filesystem->mkdir($targetDir, 0777);
                // We use a custom iterator to ignore VCS files
                $filesystem->mirror($originDir, $targetDir, Finder::create()->ignoreDotFiles(false)->in($originDir));
            }
        }
    }

}
