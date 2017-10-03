<?php

/*
 * This file is part of the Swap Bundle.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Florianv\SwapBundle\DependencyInjection;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * The container extension.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class FlorianvSwapExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $config = $this->processConfiguration(new Configuration(), $config);

        $this->configureCacheService($container, $config['cache']);

        $builderDefinition = $container->getDefinition('florianv_swap.builder');
        foreach ($config['providers'] as $name => $config) {
            $builderDefinition->addMethodCall('add', [$name, $config]);
        }
    }

    /**
     * Configures the cache service.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureCacheService(ContainerBuilder $container, array $config)
    {
        if (empty($type = $config['type'])) {
            return;
        }

        $ttl = $config['ttl'];
        $id = 'florianv_swap.cache';

        if (in_array($type, ['array', 'apcu', 'filesystem'], true)) {
            switch ($type) {
                case 'array':
                    $class = 'Symfony\Component\Cache\Adapter\ArrayAdapter';
                    break;
                case 'apcu':
                    $class = 'Symfony\Component\Cache\Adapter\ApcuAdapter';
                    break;
                case 'filesystem':
                    $class = 'Symfony\Component\Cache\Adapter\FilesystemAdapter';
                    break;
                default:
                    throw new InvalidArgumentException("Unexpected swap cache type '$type'.");
            }

            if (!class_exists($class)) {
                throw new InvalidArgumentException("Cache class $class does not exist.");
            }

            $definition = new Definition($class, ['swap', $ttl]);
            $container->setDefinition($id, $definition);
        } elseif ($container->hasDefinition($type)) {
            $definition = $container->getDefinition($type);
            if (!is_subclass_of($definition->getClass(), CacheItemPoolInterface::class)) {
                throw new InvalidArgumentException("Service '$type' does not implements " . CacheItemPoolInterface::class);
            }

            $id = $type;
        } else {
            throw new InvalidArgumentException("Unexpected swap cache type '$type'.");
        }

        $definition = $container->getDefinition('florianv_swap.builder');
        $definition
            ->replaceArgument(0, ['ttl' => $ttl])
            ->addMethodCall('useCacheItemPool', [new Reference($id)]);
    }
}
