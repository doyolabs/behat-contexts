<?php


namespace Doyo\Behat\Cli;


use Behat\Testwork\Cli\Controller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CodeCoverageController implements Controller
{
    /**
     * {@inheritdoc}
     */
    public function configure(Command $command)
    {
        $command->addOption('coverage', null, InputOption::VALUE_NONE, 'Run with code coverage');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running with code coverage</info>');
    }
}
