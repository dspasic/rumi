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

namespace Trivago\Rumi\Commands;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Trivago\Rumi\Process\GitProcessesExecution;

/**
 * @covers \Trivago\Rumi\Commands\CheckoutCommand
 */
class CheckoutCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GitProcessesExecution
     */
    private $gitProcessesExecution;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var CheckoutCommand
     */
    private $SUT;

    /**
     * @var BufferedOutput
     */
    private $output;

    public function setUp()
    {
        $this->output = new BufferedOutput();
        $this->gitProcessesExecution = $this->prophesize(GitProcessesExecution::class);
        $this->input = $this->prophesize(InputInterface::class);

        $this->SUT = new CheckoutCommand(
            $this->gitProcessesExecution->reveal()
        );
    }

    public function testGivenGitCloneBranchIsExecuted_WhenProcessIsSuccessful_ThenFullCheckoutIsDone()
    {
        $this->gitProcessesExecution->executeGitCloneBranch('abc', $this->output);

        $this->SUT->run(
            new ArrayInput(
                [
                    'repository' => 'abc',
                    'commit' => 'sha123',
                ]
            ),
            $this->output
        );

        $this->assertContains('Checkout done', $this->output->fetch());
    }

    public function testGivenGitCloneBranchIsExecuted_WhenProcessFailed_ThenErrorIsDisplayed()
    {
        $this->gitProcessesExecution->executeGitCloneBranch('asd', $this->output)->willThrow(new \Exception('Error'));

        $this->SUT->run(
            new ArrayInput(
                [
                    'repository' => 'abc',
                    'commit' => 'sha123',
                ]
            ),
            $this->output
        );

        $this->assertContains('error', $this->output->fetch());
    }

    public function testGivenGitCheckoutCommitProcessIsExecuted_WhenProcessIsSuccessful_ThenFullCheckoutIsDone()
    {
        $this->gitProcessesExecution->executeGitCheckoutCommitProcess('sha123', $this->output);

        $this->SUT->run(
            new ArrayInput(
                [
                    'repository' => 'abc',
                    'commit' => 'sha123',
                ]
            ),
            $this->output
        );

        $this->assertContains('Checkout done', $this->output->fetch());
    }

    public function testGivenGitCheckoutCommitProcessIsExecuted_WhenProcessFailed_ThenErrorIsDisplayed()
    {
        $this->gitProcessesExecution->executeGitCheckoutCommitProcess($this->input, $this->output)->willThrow(new \Exception('Error'));

        $this->SUT->run(
            new ArrayInput(
                [
                    'repository' => 'abc',
                    'commit' => 'sha123',
                ]
            ),
            $this->output
        );

        $this->assertContains('error', $this->output->fetch());
    }

    public function testGivenGitMergeBranchProcessIsExecuted_WhenProcessIsSuccessful_ThenFullCheckoutIsDone()
    {
        $this->gitProcessesExecution->executeGitMergeBranchProcess($this->input, $this->output, 'config');

        $this->SUT->run(
            new ArrayInput(
                [
                    'repository' => 'abc',
                    'commit' => 'sha123',
                ]
            ),
            $this->output
        );

        $this->assertContains('Checkout done', $this->output->fetch());
    }

    public function testGivenGitMergeBranchProcessIsExecuted_WhenProcessIsSuccessful_ThenReturnCodeIsSuccess()
    {
        $this->gitProcessesExecution->executeGitMergeBranchProcess($this->input, $this->output, 'config');

        $returnCode = $this->SUT->run(
            new ArrayInput(
                [
                    'repository' => 'abc',
                    'commit' => 'sha123',
                ]
            ),
            $this->output
        );

        $this->assertEquals(ReturnCodes::SUCCESS, $returnCode);
    }

    public function testGivenGitMergeBranchProcessIsExecuted_WhenProcessFailed_ThenErrorIsDisplayed()
    {
        $this->gitProcessesExecution->executeGitMergeBranchProcess($this->input, $this->output, 'config')->willThrow(new \Exception('Error'));

        $this->SUT->run(
            new ArrayInput(
                [
                    'repository' => 'abc',
                    'commit' => 'sha123',
                ]
            ),
            $this->output
        );

        $this->assertContains('error', $this->output->fetch());
    }

    public function testGivenExceptionWithReturnCode_WhenCommandIsExecuted_ThenReturnCodeIsPassed(){
        // given
        $code = 123;
        $this->gitProcessesExecution->executeGitCloneBranch('abc', $this->output)->willThrow(new \Exception('Error', $code));

        // when
        $returnCode = $this->SUT->run(
            new ArrayInput(
                [
                    'repository' => 'abc',
                    'commit' => 'sha123',
                ]
            ),
            $this->output
        );

        // then
        $this->assertEquals($code, $returnCode);
    }
}
