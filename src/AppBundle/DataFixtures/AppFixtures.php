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
        $task1 = new Task();
        $task1->setTitle('Tache_Fixture_1');
        $task1->setContent('Tache Fixture Content : 1_' . time());
        $task1->setCreatedBy($admin);
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('Tache_Fixture_2');
        $task2->setContent('Tache Fixture Content : 2_' . time());
        $task2->setCreatedBy($user);
        $manager->persist($task2);

        $task3 = new Task();
        $task3->setTitle('Tache_Fixture_2');
        $task3->setContent('Tache Fixture Content : 3_' . time());
        $task3->setCreatedBy(null);
        $manager->persist($task3);

        $manager->flush();
    }
}
