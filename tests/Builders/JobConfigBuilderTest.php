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

use PHPUnit\Framework\TestCase;
use Trivago\Rumi\Models\JobConfig;

/**
 * @covers \Trivago\Rumi\Builders\JobConfigBuilder
 */
class JobConfigBuilderTest extends TestCase
{
    /**
     * @var JobConfigBuilder
     */
    private $SUT;

    /**
     * @var ComposeParser
     */
    private $composeParser;

    protected function setUp()
    {
        $this->composeParser = $this->prophesize(ComposeParser::class);

        $this->SUT = new JobConfigBuilder(
            $this->composeParser->reveal()
        );
    }

    public function testGivenEmptyJobsGiven_WhenBuildExecuted_ThenOutputIsEmptyArray()
    {
        // given
        $config = [];

        // when
        $jobs = $this->SUT->build($config);

        // then
        $this->assertEmpty($jobs->getIterator()->count());
        $this->assertTrue(is_array($jobs->getIterator()->getArrayCopy()));
    }

    public function testGivenOneJobDefined_WhenBuildExecuted_ThenOutputIsCorrectJobObject()
    {
        // given
        $config = [
            'Do something fun' => [
            ],
        ];

        // when
        $jobs = $this->SUT->build($config);

        // then
        $this->assertEquals(1, $jobs->getIterator()->count());
        /** @var JobConfig $job */
        $job = $jobs->getIterator()->offsetGet(0);

        $this->assertInstanceOf(JobConfig::class, $job);
        $this->assertSame('Do something fun', $job->getName());
    }

    public function testGivenJobWithCiImageSpecified_WhenBuildExecuted_ThenJobConfigContainsCiImage()
    {
        // given
        $config = [
            'Do something fun' => [
                'ci_image' => '__container__',
            ],
        ];

        // when
        $jobs = $this->SUT->build($config);

        // then
        $this->assertEquals(1, $jobs->getIterator()->count());
        /** @var JobConfig $job */
        $job = $jobs->getIterator()->offsetGet(0);

        $this->assertSame('__container__', $job->getCiContainer());
    }

    public function testGivenJobWithEntypointSpecified_WhenBuildExecuted_ThenJobConfigContainsEntrypoint()
    {
        // given
        $config = [
            'Do something fun' => [
                'entrypoint' => '__entrypoint__',
            ],
        ];

        // when
        $jobs = $this->SUT->build($config);

        // then
        $this->assertEquals(1, $jobs->getIterator()->count());
        /** @var JobConfig $job */
        $job = $jobs->getIterator()->offsetGet(0);

        $this->assertSame('__entrypoint__', $job->getEntryPoint());
    }

    public function testGivenJobWithCommandSpecified_WhenBuildExecuted_ThenJobConfigContainsCommand()
    {
        // given
        $commands = ['__commands__'];
        $config = [
            'Do something fun' => [
                'commands' => $commands,
            ],
        ];

        // when
        $jobs = $this->SUT->build($config);

        // then
        $this->assertEquals(1, $jobs->getIterator()->count());
        /** @var JobConfig $job */
        $job = $jobs->getIterator()->offsetGet(0);

        $this->assertSame($commands, $job->getCommands());
    }

    public function testGivenEmptyJobsDefinition_WhenBuilderExecuted_ThenItDoesNothing()
    {
        // given

        // when
        $jobs = $this->SUT->build(null);

        // then
        $this->assertEmpty($jobs->getIterator()->getArrayCopy());
    }
}
