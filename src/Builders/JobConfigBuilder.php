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

namespace Trivago\Rumi\Builders;

use Trivago\Rumi\Models\JobConfig;
use Trivago\Rumi\Models\JobConfigCollection;

class JobConfigBuilder
{
    /**
     * @var ComposeParser
     */
    private $composeHandler;

    /**
     * @param ComposeParser $compose_handler
     */
    public function __construct(ComposeParser $compose_handler)
    {
        $this->composeHandler = $compose_handler;
    }

    /**
     * @param $stageConfig
     *
     * @return JobConfigCollection
     */
    public function build($stageConfig)
    {
        $jobConfigCollection = new JobConfigCollection();
        if (empty($stageConfig)) {
            return $jobConfigCollection;
        }

        foreach ($stageConfig as $jobName => $jobConfig) {
            $jobConfigCollection->add(
                new JobConfig(
                    $jobName,
                    $this->composeHandler->parseComposePart($jobConfig['docker'] ?? null),
                    $jobConfig['ci_image'] ?? null,
                    $jobConfig['entrypoint'] ?? null,
                    $jobConfig['commands'] ?? null,
                    $jobConfig['timeout'] ?? JobConfig::DEFAULT_TIMEOUT
                )
            );
        }

        return $jobConfigCollection;
    }
}
