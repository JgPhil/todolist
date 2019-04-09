<?php

namespace Tests\AppBundle\Entity;

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;

class TaskTest extends TestCase
{
    private $user;
    private $date;

    public function setUp()
    {
        $this->user = new User();
        $this->date = new \Datetime();
    }

    public function testNewTaskSetGet()
    {
        $task = new Task();

        $task->setCreatedAt($this->date);
        $task->setTitle("title");
        $task->setContent("content");
        $task->setCreatedBy($this->user);

        $this->assertSame($this->date, $task->getCreatedAt());
        $this->assertSame("title", $task->getTitle());
        $this->assertSame("content", $task->getContent());
        $this->assertSame($this->user, $task->getCreatedBy());
        $this->assertSame(false, $task->isDone());
        $this->assertSame(null, $task->getId());

    }

    public function testNewTaskToggle()
    {
    	$task = new Task();
    	$task->toggle(true);
    	$this->assertSame(true, $task->isDone());
    }
}
