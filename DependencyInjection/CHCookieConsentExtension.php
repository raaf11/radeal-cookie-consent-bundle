<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class CHCookieConsentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ch_cookie_consent.categories', $config['categories']);
        $container->setParameter('ch_cookie_consent.use_logger', $config['use_logger']);
        $container->setParameter('ch_cookie_consent.http_only', $config['http_only']);
        $container->setParameter('ch_cookie_consent.form_action', $config['form_action']);
        $container->setParameter('ch_cookie_consent.csrf_protection', $config['csrf_protection']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
