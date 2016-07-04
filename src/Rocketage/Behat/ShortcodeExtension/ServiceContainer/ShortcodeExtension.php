<?php

namespace Rocketage\Behat\ShortcodeExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension as TestworkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ShortcodeExtension implements TestworkExtension
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'shortcode';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('shortcodes')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadShortcodeProcessorFactory(
            $container,
            $this->shortcodeDefinitions($this->defaultShortcodes(), $config['shortcodes'])
        );

        $this->loadShortcodeProcessor($container);

        $this->loadStepListener($container);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @param ContainerBuilder $container
     * @param array $shortcodeDefinitions
     */
    private function loadShortcodeProcessorFactory(ContainerBuilder $container, array $shortcodeDefinitions)
    {
        $container->setDefinition(
            'shortcode.processor.factory',
            new Definition(
                'Rocketage\Behat\ShortcodeExtension\ServiceContainer\ShortcodeFactory',
                [
                    new Definition('Maiorano\Shortcodes\Manager\ShortcodeManager'),
                    $shortcodeDefinitions
                ]
            )
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadShortcodeProcessor(ContainerBuilder $container)
    {
        $shortcodeProcessor = new Definition('Maiorano\Shortcodes\Manager\ShortcodeManager');
        $shortcodeProcessor->setFactory([new Reference('shortcode.processor.factory'), 'getProcessor']);
        $container->setDefinition('shortcode.processor', $shortcodeProcessor);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadStepListener(ContainerBuilder $container)
    {
        $definition = new Definition(
            'Rocketage\Behat\ShortcodeExtension\Listener\Step',
            [
                new Reference('shortcode.processor')
            ]
        );

        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));

        $container->setDefinition('shortcode.listener.step', $definition);
    }

    /**
     * @param array $defaultClassnames
     * @param array $userDefinedClassnames
     * @return array
     */
    private function shortcodeDefinitions(array $defaultClassnames, array $userDefinedClassnames = [])
    {
        return array_map(
            function($class) { return new Definition($class); },
            array_merge($userDefinedClassnames, $defaultClassnames)
        );
    }

    /**
     * @return array
     */
    private function defaultShortcodes()
    {
        return ['Rocketage\Behat\ShortcodeExtension\Shortcode\Date'];
    }
}
