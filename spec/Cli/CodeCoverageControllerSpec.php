<?php

/*
 * This file is part of the DoyoLabs Behat Common project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    public function it_is_initializable()
    {
        $this->shouldHaveType(CodeCoverageController::class);
    }

    public function it_should_add_coverage_option(
        Command $command
    ) {
        $command->addOption('coverage', null, InputOption::VALUE_NONE, Argument::any())
            ->shouldBeCalled();
        $this->configure($command);
    }

    public function it_should_add_run_with_coverage_info(
        InputInterface $input,
        OutputInterface $output
    ) {

        $input->getOption('coverage')
            ->shouldBeCalled()
            ->willReturn(true);
        $output->writeln(Argument::any())->shouldBeCalled();

        $this->execute($input, $output);
    }
}
