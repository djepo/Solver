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

namespace JMS\SecurityExtraBundle\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class RunAs
{
    public $roles;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $values['roles'] = $values['value'];
        }
        if (!isset($values['roles'])) {
            throw new \InvalidArgumentException('"roles" must be defined for RunAs annotation.');
        }

        $this->roles = array_map('trim', explode(',', $values['roles']));
    }
}