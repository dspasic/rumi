<?php

/*
 * Copyright 2016 trivago GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Trivago\Rumi;

class Timer
{
    public static function execute(callable $cb)
    {
        $time = microtime(true);

        $cbArgs = self::determineCallbackArguments(func_num_args(), func_get_args());
        $cb(...$cbArgs);

        return number_format(microtime(true) - $time, 3, '.', '') . 's';
    }

    /**
     * @return array
     */
    private static function determineCallbackArguments(int $argc, array $args): array
    {
        if (1 < $argc) {
            $cbArgs = $args;
            array_shift($cbArgs);

            return $cbArgs;
        }

        return [];
    }
}
