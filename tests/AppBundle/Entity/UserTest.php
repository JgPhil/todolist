<?php

namespace Tests\AppBundle\Entity;

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\User;

class UserTest extends TestCase
{
    public function testNewUserSetGet()
    {
    	$user = new User();
        $user->setUsername("username");
        $user->setPassword("password");
        $user->setEmail("email@email.com");
        $user->setRoles(["ROLE_ROLE"]);

        $this->assertSame("username", $user->getUsername());
        $this->assertSame("password", $user->getPassword());
        $this->assertSame("email@email.com", $user->getEmail());
        $this->assertSame(["ROLE_ROLE"], $user->getRoles());
        $this->assertSame(null, $user->getSalt());
        $this->assertSame(null, $user->getId());

    }
}
