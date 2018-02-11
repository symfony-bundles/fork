<?php

namespace SymfonyBundles\ForkBundle\Tests\Fork;

use SymfonyBundles\ForkBundle\Fork;
use SymfonyBundles\ForkBundle\Tests\Fixtures;
use SymfonyBundles\ForkBundle\Tests\TestCase;

class ForkTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Fork\ForkInterface::class, new Fork\Fork(new Fork\Process()));
    }

    public function testTasksPoll()
    {
        $task = new Fixtures\Task\DemoTask();
        $fork = new Fork\Fork(new Fork\Process());

        $fork->attach(new Fixtures\Task\DemoTask())->attach($task);

        $this->assertTrue($fork->exists($task));
        $this->assertFalse($fork->exists(new Fixtures\Task\DemoTask()));

        $this->assertFalse($fork->detach($task)->exists($task));

        $fork->run();
    }

    public function testTasksExecuting()
    {
        $process = $this->getMockBuilder(Fork\Process::class)
            ->setMethods(['fork', 'terminate'])
            ->getMock();

        $process->method('fork')->willReturn(true);

        $fork = new Fork\Fork($process);

        $fork
            ->attach($task1 = new Fixtures\Task\DemoTask())
            ->attach($task2 = new Fixtures\Task\DemoTask())
            ->attach($task3 = new Fixtures\Task\DemoTask());

        $fork->run();

        $this->assertTrue($task1->isExecuted());
        $this->assertTrue($task2->isExecuted());
        $this->assertTrue($task3->isExecuted());
    }
}