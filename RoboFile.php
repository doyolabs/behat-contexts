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

use Lurker\Event\FilesystemEvent;
use Robo\Tasks;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends Tasks
{
    private $coverage = false;

    public function watch($options = ['coverage'=>false])
    {
        $paths = [
            'src',
            'tests',
            'spec',
            'features',
        ];

        $this->taskWatch()
            ->monitor(
                $paths,
                function (FilesystemEvent $event) use ($options) {
                    $resource = (string) $event->getResource();
                    if (
                        false !== strpos($resource, 'build')
                        || false !== strpos($resource, 'var')
                    ) {
                        return;
                    }
                    $this->test($options);
                },
                FilesystemEvent::ALL
            )
            ->run();
    }

    public function test($options=['coverage' => false])
    {
        $this->coverage = $options['coverage'];
        $this->taskExec('clear')->run();
        $this->doRunPhpSpec();
        $this->doRunBehat();
        $this->doRunPhpUnit();

        if ($this->coverage) {
            $this->doMergeCoverage();
        }
    }

    private function doMergeCoverage()
    {
        $this->yell('Merging coverage');
        $this
            ->taskExec('phpdbg -qrr ./vendor/bin/phpcov merge --ansi --clover build/clover.xml build/cov')
            ->run();
        $this
            ->taskExec('phpdbg -qrr ./vendor/bin/phpcov merge --ansi --html build/html build/cov')
            ->run();
        $this
            ->taskExec('phpdbg -qrr ./vendor/bin/phpcov merge --text --ansi build/cov')
            ->run();
    }

    private function doRunBehat()
    {
        $behat = $this->taskBehat();
        $behat->noInteraction()
            ->format('progress')
            ->colors();

        $this->yell('Running Behat');
        if ($this->coverage) {
            $behat->option('coverage');
            $this->taskExec('phpdbg -qrr '.$behat->getCommand())
                ->run();
        } else {
            $behat->run();
        }
    }

    private function doRunPhpSpec()
    {
        $spec = $this->taskPhpspec();
        $spec->noCodeGeneration()
            ->noInteraction()
            ->format('dot');

        $this->yell('Running PhpSpec');
        if ($this->coverage) {
            $spec->option('coverage');
            $this->taskExec('phpdbg -qrr '.$spec->getCommand())
                ->run();
        } else {
            $spec->run();
        }
    }

    private function doRunPhpUnit()
    {
        $phpunit = $this->taskPhpUnit();

        if ($this->coverage) {
            $phpunit->option('coverage-php', 'build/cov/phpunit.cov');
            $this
                ->taskExec('phpdbg -qrr '.$phpunit->getCommand())
                ->run();
        } else {
            $phpunit->run();
        }
    }
}
