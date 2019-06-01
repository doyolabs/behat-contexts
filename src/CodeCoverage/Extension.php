<?php


namespace Doyo\Behat\CodeCoverage;

use Doyo\Behat\Cli\CodeCoverageController;
use LeanPHP\Behat\CodeCoverage\Compiler\DriverPass;
use LeanPHP\Behat\CodeCoverage\Compiler\FactoryPass;
use LeanPHP\Behat\CodeCoverage\Compiler\FilterPass;
use LeanPHP\Behat\CodeCoverage\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Extension extends BaseExtension
{
    public function load(ContainerBuilder $container, array $config)
    {
        parent::load($container, $config);

        $container->setParameter('behat.code_coverage.controller.cli.class', CodeCoverageController::class);
    }

    public function process(ContainerBuilder $container)
    {
        $input = $container->get('cli.input');
        if (!$input->hasParameterOption('--coverage')) {
            $container->getParameterBag()->set('behat.code_coverage.skip', true);
        }else{
            $container->getParameterBag()->set('behat.code_coverage.skip', false);
        }

        $passes = $this->getCompilerPasses();

        foreach ($passes as $pass) {
            $pass->process($container);
        }
    }

    /**
     * return an array of compiler passes
     *
     * @return array
     */
    private function getCompilerPasses()
    {
        return array(
            new DriverPass(),
            new FactoryPass(),
            new FilterPass(),
        );
    }
}
