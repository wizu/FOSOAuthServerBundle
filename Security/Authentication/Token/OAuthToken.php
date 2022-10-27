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

namespace FOS\OAuthServerBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * OAuthToken class.
 *
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */

// supporting Symfony 5.3
if (method_exists(AbstractToken::class, 'getCredentials')) {
    class OAuthToken extends AbstractToken
    {
        use OAuthTokenTrait;

        public function getCredentials()
        {
            return $this->token;
        }
    }
} else {
    class OAuthToken extends AbstractToken
    {
        use OAuthTokenTrait;
    }
}

trait OAuthTokenTrait
{
    /**
     * @var string|null
     */
    protected $token;

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}
