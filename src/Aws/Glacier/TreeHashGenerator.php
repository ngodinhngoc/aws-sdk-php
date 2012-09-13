<?php
/**
 * Copyright 2010-2012 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Aws\Glacier;

use Aws\Common\Enum\Size;

/**
 * Generates tree hashes of payloads
 */
class TreeHashGenerator
{
    protected $payload;

    public static function linearHash($payload)
    {
        return hash('sha256', $payload);
    }

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function getLinearHash()
    {
        return self::linearHash($this->payload);
    }

    public function getTreeHash()
    {
        $nodes = array_map(function ($data) {
            return self::linearHash($data);
        }, str_split($this->payload, Size::MB));

        while (count($nodes) > 1) {
            $sets = array_chunk($nodes, 2);
            $nodes = array();
            foreach ($sets as $set) {
                $nodes[] = (count($set) === 1) ? $set[0] : self::linearHash(join('', $set));
            }
        }

        return $nodes[0];
    }
}
