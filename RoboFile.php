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

    private $watch = false;

    public function watch($options = ['coverage'=>false])
    {
        $this->watch = true;

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
                        return 0;
                    }
                    return $this->test($options);
                },
                FilesystemEvent::ALL
            )
            ->run();

        return $this->watch;
    }

    public function test($options=['coverage' => false])
    {
        $this->stopOnFail(false);

        $this->coverage = $options['coverage'];

        if($this->watch){
            $this->taskExec('clear')->run();
        }

        $phpspec = $this->configurePhpSpec();
        $behat = $this->configureBehat();
        $phpunit = $this->configurePhpUnit();

        $tasks = [$phpspec, $phpunit, $behat];
        $failed = false;
        $errorTask = null;
        $messages = [];

        /* @var \Robo\Task\BaseTask $task */
        foreach($tasks as $task){
            /* @var \Robo\Result $test */
            $test = $task->run();
            if($test->getExitCode() !== 0){
                $failed = true;
                $errorTask = $task;
            }
        }

        $builder = $this->collectionBuilder();
        if ($this->coverage) {
            $this->doMergeCoverage($builder);
            $builder->run();
        }

        if(!$failed){
            $this->yell('Tests runs successfully');
            return;
        }

        return \Robo\Result::error($errorTask,'Tests Failed');
    }

    private function doMergeCoverage(\Robo\Collection\CollectionBuilder $builder)
    {
        $builder->taskExec('phpdbg -qrr ./vendor/bin/phpcov merge --ansi --clover build/logs/clover.xml build/cov');
        $builder->taskExec('phpdbg -qrr ./vendor/bin/phpcov merge --ansi --html build/html build/cov');
        $builder->taskExec('phpdbg -qrr ./vendor/bin/phpcov merge --text --ansi build/cov');
    }

    private function configureBehat()
    {
        $behat = $this->taskBehat();
        $behat->noInteraction()
            ->format('progress')
            ->colors();
        if ($this->coverage) {
            $behat->option('coverage');
            return $this->taskExec('phpdbg -qrr '.$behat->getCommand());
        }

        return $behat;
    }

    private function configurePhpSpec()
    {
        $spec = $this->taskPhpspec();
        $spec->noCodeGeneration()
            ->noInteraction()
            ->format('dot');
        if ($this->coverage) {
            $spec->option('coverage');
            return $this->taskExec('phpdbg -qrr '.$spec->getCommand());
        }

        return $spec;
    }

    private function configurePhpUnit()
    {
        $phpunit = $this->taskPhpUnit();

        if ($this->coverage) {
            $phpunit->option('coverage-php', 'build/cov/phpunit.cov');
            return $this
                ->taskExec('phpdbg -qrr '.$phpunit->getCommand());
        }

        return $phpunit;
    }
}
