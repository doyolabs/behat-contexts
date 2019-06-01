<?php


namespace Doyo\Behat;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Doyo\Behat\Initializer\ExpressionAwareInitializer;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class Extension implements ExtensionInterface
{
    public function getConfigKey()
    {
        return 'doyo';
    }

    public function process(ContainerBuilder $container)
    {
        if($container->has('symfony2_extension.kernel')){
            $this->loadSymfony($container);
        }
        return;
    }

    private function loadSymfony(ContainerBuilder $container)
    {
        /* @var \Symfony\Component\HttpKernel\KernelInterface $kernel */
        $kernel = $container->get('symfony2_extension.kernel');
        $kernelContainer = $kernel->getContainer();

        if($kernelContainer->has('translator')){
            $translator = $kernelContainer->get('translator');
            $container->getDefinition('doyo.expression.provider')->addMethodCall('setTranslator',[$translator]);
        }
        if($kernelContainer->has('router')){
            $router = $kernelContainer->get('router');
            $container->getDefinition('doyo.expression.provider')->addMethodCall('setRouter',[$router]);
        }
    }

    public function initialize(ExtensionManager $extensionManager)
    {
        // TODO: Implement initialize() method.
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('translator')->defaultValue('translator')->end()
            ->end()
        ;
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $service = $container->get('symfony2_extension.kernel');
        $this->loadExpressionLanguage($container);
        $this->loadContextInitializer($container);
    }

    private function loadExpressionLanguage(ContainerBuilder $container)
    {
        $container->setDefinition('doyo.expression.provider', new Definition(ExpressionLanguageProvider::class));

        $expression = new Definition(ExpressionLanguage::class);
        $expression->addMethodCall('registerProvider',[new Reference('doyo.expression.provider')]);
        $container->setDefinition('doyo.expression.language', $expression);
    }

    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition(ExpressionAwareInitializer::class, array(
            new Reference('doyo.expression.language'),
        ));
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('doyo.initializer.expression', $definition);
    }
}
