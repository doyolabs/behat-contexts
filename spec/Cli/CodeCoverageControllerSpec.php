<?php

namespace spec\Doyo\Behat\Cli;

use Doyo\Behat\Cli\CodeCoverageController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CodeCoverageControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CodeCoverageController::class);
    }

    function it_should_add_coverage_option(
        Command $command
    )
    {
        $command->addOption('coverage', null, InputOption::VALUE_NONE, Argument::any())
            ->shouldBeCalled();
        $this->configure($command);
    }

    function it_should_add_run_with_coverage_info(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $output->writeln(Argument::any())->shouldBeCalled();

        $this->execute($input, $output);
    }
}
