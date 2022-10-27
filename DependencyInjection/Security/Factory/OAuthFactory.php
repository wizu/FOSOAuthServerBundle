<?php

declare(strict_types=1);

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\OAuthServerBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * OAuthFactory class.
 *
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */

// support for symfony 5.3 (see below)
class BaseOAuthFactory implements AuthenticatorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createAuthenticator(ContainerBuilder $container, string $id, array $config, string $userProviderId): array|string
    {
        $providerId = 'fos_oauth_server.security.authentication.authenticator.'.$id;
        $container
            ->setDefinition($providerId, new ChildDefinition('fos_oauth_server.security.authentication.authenticator'))
            ->replaceArgument(0, new Reference('fos_oauth_server.server'))
            ->replaceArgument(1, new Reference('security.user_checker.'.$id))
            ->replaceArgument(2, new Reference($userProviderId))
        ;

        return $providerId;
    }

    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint): array
    {
        $providerId = 'security.authentication.provider.fos_oauth_server.'.$id;
        $container
            ->setDefinition($providerId, new ChildDefinition('fos_oauth_server.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
            ->replaceArgument(2, new Reference('security.user_checker.'.$id))
        ;

        $listenerId = 'security.authentication.listener.fos_oauth_server.'.$id;
        $container->setDefinition($listenerId, new ChildDefinition('fos_oauth_server.security.authentication.listener'));

        return [$providerId, $listenerId, 'fos_oauth_server.security.entry_point'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): string
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return 'fos_oauth';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}

// supporting Symfony 5.3 (must come after definition of BaseOAuthFactory)
if (interface_exists('\Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface')) {
    class OAuthFactory extends BaseOAuthFactory implements \Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface
    {
    }
} else {
    class OAuthFactory extends BaseOAuthFactory
    {
    }
}
