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

namespace Test\Doyo\Behat;

use LeanPHP\PhpSpec\CodeCoverage\Listener\CodeCoverageListener;
use PhpSpec\Extension;
use PhpSpec\ServiceContainer;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report;
use SebastianBergmann\CodeCoverage\Version;
use Symfony\Component\Console\Input\InputOption;

class CodeCoverageExtension implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(ServiceContainer $container, array $params = [])
    {
        foreach ($container->getByTag('console.commands') as $command) {
            $command->addOption('coverage', null, InputOption::VALUE_NONE, 'Run with collecting coverage');
        }

        $container->define('code_coverage.filter', function () {
            return new Filter();
        });

        $container->define('code_coverage', function ($container) {
            return new CodeCoverage(null, $container->get('code_coverage.filter'));
        });

        $container->define('code_coverage.options', function ($container) use ($params) {
            $options = !empty($params) ? $params : $container->getParam('code_coverage');

            if (!isset($options['format'])) {
                $options['format'] = ['html'];
            } elseif (!\is_array($options['format'])) {
                $options['format'] = (array) $options['format'];
            }

            if (isset($options['output'])) {
                if (!\is_array($options['output']) && 1 === \count($options['format'])) {
                    $format = $options['format'][0];
                    $options['output'] = [$format => $options['output']];
                }
            }

            if (!isset($options['show_uncovered_files'])) {
                $options['show_uncovered_files'] = true;
            }
            if (!isset($options['lower_upper_bound'])) {
                $options['lower_upper_bound'] = 35;
            }
            if (!isset($options['high_lower_bound'])) {
                $options['high_lower_bound'] = 70;
            }

            return $options;
        });

        $container->define('code_coverage.reports', function ($container) {
            $options = $container->get('code_coverage.options');

            $reports = [];
            foreach ($options['format'] as $format) {
                switch ($format) {
                    case 'clover':
                        $reports['clover'] = new Report\Clover();
                        break;
                    case 'php':
                        $reports['php'] = new Report\PHP();
                        break;
                    case 'text':
                        $reports['text'] = new Report\Text(
                            $options['lower_upper_bound'],
                            $options['high_lower_bound'],
                            $options['show_uncovered_files'],
                            /* $showOnlySummary */ false
                        );
                        break;
                    case 'xml':
                        $reports['xml'] = new Report\Xml\Facade(Version::id());
                        break;
                    case 'crap4j':
                        $reports['crap4j'] = new Report\Crap4j();
                        break;
                    case 'html':
                        $reports['html'] = new Report\Html\Facade();
                        break;
                }
            }

            $container->setParam('code_coverage', $options);

            return $reports;
        });

        $input = $container->get('console.input');
        if ($input->hasParameterOption('--coverage')) {
            $this->loadListener($container);
        }
    }

    public function loadListener(ServiceContainer $container)
    {
        $container->define('event_dispatcher.listeners.code_coverage', function ($container) {
            $skipCoverage = false;

            $listener = new CodeCoverageListener(
                $container->get('console.io'),
                $container->get('code_coverage'),
                $container->get('code_coverage.reports'),
                $skipCoverage
            );
            $listener->setOptions($container->getParam('code_coverage', []));

            return $listener;
        }, ['event_dispatcher.listeners']);
    }
}
