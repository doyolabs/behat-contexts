<?php

namespace spec\Doyo\Behat\CodeCoverage;

use Doyo\Behat\CodeCoverage\Extension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Extension::class);
    }

    function its_process_should_configure_skip_code_coverage(
        ContainerBuilder $builder,
        InputInterface $input,
        ParameterBagInterface $parameterBag
    )
    {
        $input->hasParameterOption('--coverage')->willReturn(false);
        $parameterBag->set('behat.code_coverage.skip',true)->shouldBeCalled();

        $builder->getParameterBag()->willReturn($parameterBag);
        $builder->hasDefinition(Argument::any())->willReturn(false);
        $builder->get('cli.input')->willReturn($input);
        $this->process($builder);
    }
}
