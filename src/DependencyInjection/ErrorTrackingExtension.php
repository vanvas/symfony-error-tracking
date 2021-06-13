<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Vim\ErrorTracking\Handler\MonologHandler;

class ErrorTrackingExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container
            ->getDefinition(MonologHandler::class)
            ->setArgument('$ignoredExceptions', $config['ignored_exceptions'])
            ->setArgument('$ignoredCodes', $config['ignored_codes'])
            ->setArgument('$ignoredLevels', $config['ignored_levels'])
            ->setArgument('$ignoredMessages', $config['ignored_messages'])
            ->setArgument('$url', $config['url'])
        ;
    }
}
