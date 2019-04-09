<?php

namespace Tests\AppBundle\Security;

use PHPUnit\Framework\TestCase;
use AppBundle\Security\TaskVoter;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskVoterTest extends TestCase
{
	private $user1;
	private $tokenU1;
	private $user2;
	private $tokenU2;
	private $admin1;
	private $tokenA1;
	private $voter;
	private $task;

    public function setUp()
    {
    	// Create Each user and it's token
    	$user = new User();
    	$user->setRoles(['ROLE_USER']);
    	$token = new UsernamePasswordToken($user, 'credentials', 'memory');
        $this->user1 = $user;
        $this->tokenU1 = $token;

    	$user = new User();
    	$user->setRoles(['ROLE_USER']);
    	$token = new UsernamePasswordToken($user, 'credentials', 'memory');
        $this->user2 = $user;
        $this->tokenU2 = $token;

    	$user = new User();
    	$user->setRoles(['ROLE_ADMIN']);
    	$token = new UsernamePasswordToken($user, 'credentials', 'memory');
        $this->admin1 = $user;
        $this->tokenA1 = $token;

        // create the voter for testing
    	$voter = new TaskVoter();
    	$this->voter = $voter;

    	// create the task
    	$task = new Task();
    	$this->task = $task;

    }

    // Test instanceOf errors
    public function testTaskVoterTaskIsNotTask()
    {
    	$task = "tache";
    	$this->assertSame(0, $this->voter->vote($this->tokenA1, $task, ['edit']));
    }

    public function testTaskVoterUserIsNotUser()
    {
    	$user = "user";
    	$token = new UsernamePasswordToken($user, 'credentials', 'memory');
    	$this->task->setCreatedBy($this->user1);
    	$this->assertSame(-1, $this->voter->vote($token, $this->task, ['edit']));
    }

    // Test edit possibilities
    public function testTaskVoterCanEditRoleAdmin()
    {
    	$this->task->setCreatedBy(null);
    	$this->assertSame(1, $this->voter->vote($this->tokenA1, $this->task, ['edit']));
    }

    public function testTaskVoterCannotEditRoleAdmin()
    {
    	$this->task->setCreatedBy($this->user1);
    	$this->assertSame(-1, $this->voter->vote($this->tokenA1, $this->task, ['edit']));
    }

    public function testTaskVoterCanEditRoleUser()
    {
    	$this->task->setCreatedBy($this->user1);
    	$this->assertSame(1, $this->voter->vote($this->tokenU1, $this->task, ['edit']));
    }

    public function testTaskVoterCannotEditRoleUser()
    {
    	$this->task->setCreatedBy($this->user2);
    	$this->assertSame(-1, $this->voter->vote($this->tokenU1, $this->task, ['edit']));
    }

    // Test delete possibilities
    public function testTaskVoterCanDeleteRoleAdmin()
    {
    	$this->task->setCreatedBy($this->user1);
    	$this->assertSame(1, $this->voter->vote($this->tokenA1, $this->task, ['delete']));
    }

    public function testTaskVoterCanDeleteRoleUser()
    {
    	$this->task->setCreatedBy($this->user1);
    	$this->assertSame(1, $this->voter->vote($this->tokenU1, $this->task, ['delete']));
    }

    public function testTaskVoterCannotDeleteRoleUser()
    {
    	$this->task->setCreatedBy($this->user2);
    	$this->assertSame(-1, $this->voter->vote($this->tokenU1, $this->task, ['delete']));
    }

    // Test toggle possibilities
    public function testTaskVoterCanToggleRoleAdmin()
    {
    	$this->task->setCreatedBy($this->user1);
    	$this->assertSame(1, $this->voter->vote($this->tokenA1, $this->task, ['toggle']));
    }

    public function testTaskVoterCanToggleRoleUser()
    {
    	$this->task->setCreatedBy($this->user1);
    	$this->assertSame(1, $this->voter->vote($this->tokenU1, $this->task, ['toggle']));
    }

    public function testTaskVoterCannotToggleRoleUser()
    {
    	$this->task->setCreatedBy($this->user2);
    	$this->assertSame(-1, $this->voter->vote($this->tokenU1, $this->task, ['toggle']));
    }



}
