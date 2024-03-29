<?php

/*
 * Copyright 2010 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SecurityExtraBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * This token is automatically generated by the RunAsManager when an invocation
 * is supposed to be run with a different Token.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class RunAsUserToken extends AbstractToken
{
    private $originalToken;
    private $key;
    private $credentials;

    public function __construct($key, $user, $credentials, array $roles, TokenInterface $originalToken)
    {
        parent::__construct($roles);

        $this->originalToken = $originalToken;
        $this->credentials = $credentials;
        $this->key = $key;

        $this->setUser($user);
        $this->setAuthenticated(true);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getOriginalToken()
    {
        return $this->originalToken;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function eraseCredentials()
    {
        parent::eraseCredentials();

        $this->credentials = null;
    }

    public function serialize()
    {
        return serialize(array(
            $this->originalToken,
            $this->key,
            $this->credentials,
            parent::serialize(),
        ));
    }

    public function unserialize($str)
    {
        list($this->originalToken, $this->key, $this->credentials, $parentStr) = unserialize($str);
        parent::unserialize($parentStr);
    }
}