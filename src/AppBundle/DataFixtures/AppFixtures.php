<?php
namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use AppBundle\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
private $encoder;

	public function __construct(UserPasswordEncoderInterface $encoder)
	{
	    $this->encoder = $encoder;
	}

    public function load(ObjectManager $manager)
    {
        // create 1 Admin
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@fixture.fr');
		$admin->setPassword($this->encoder->encodePassword($admin, 'admin'));
		$admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // create 1 User
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@fixture.fr');
		$user->setPassword($this->encoder->encodePassword($user, 'user'));
		$user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        // create 3 Tasks
        for ($i=1; $i < 4; $i++) { 
            $task = new Task();
            $task->setTitle('Tache_Fixture_' . $i);
            $task->setContent('Tache Fixture Content : ' . $i);
            switch ($i) {
                case 1 :
                    $byUser = $admin;
                    break;
                case 2 :
                    $byUser = $user;
                    break;
                default:
                    $byUser = null;
                    break;
            }
            $task->setCreatedBy($byUser);
            $manager->persist($task);
        }
        $manager->flush();       
    }
}
