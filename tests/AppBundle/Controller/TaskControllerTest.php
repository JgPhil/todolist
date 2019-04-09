<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;

class TaskControllerTest extends WebTestCase 
{
    private $client = null;
    private $entityManager;
    private $testTaskId;

    public function setUp()
    {
        $this->client = static::createClient();


        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

    }

    // fake user enought to access some part of the site
    private function logIn(array $role)
    {
        $session = $this->client->getContainer()->get('session');
        $firewallName = 'main';
        $firewallContext = 'main';
        $token = new UsernamePasswordToken('user', null, $firewallName, $role);

        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    // connect Real User on database with his id number
    private function logInRealUser($userId)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find($userId);

        $session = $this->client->getContainer()->get('session');
        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName);

        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    //List scenarii
    public function testTaskList()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertContains("liste des tâches à faire", $this->client->getResponse()->getContent());
    }

    public function testTaskListDone()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertContains("liste des tâches faites", $this->client->getResponse()->getContent());
    }

    //Create scenarii
    public function testTaskCreatePage()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertContains("creation de tâche", $this->client->getResponse()->getContent());
    }

    public function testTaskCreatePageSendForm()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'title';
        $form['task[content]'] = 'content';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertContains("La tâche a été bien été ajoutée", $this->client->getResponse()->getContent());
    }

    //Edit scenarii
    public function testTaskEditPageOk()
    {
        $time = time();
        $this->logInRealUser(1);
        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'title_modif_' . $time;
        $form['task[content]'] = 'content_modif' . $time;
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertContains("La tâche a bien été modifiée", $this->client->getResponse()->getContent());
        $this->assertContains('title_modif_' . $time, $this->client->getResponse()->getContent());
    }

    //Delete scenarii
    public function testTaskDeletePageError403FakeUser()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/tasks/1/delete');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testTaskDeletePageError404()
    {
        $this->logIn(['ROLE_ADMIN']);
        $crawler = $this->client->request('GET', '/tasks/9999999/delete');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    //Toggle scenarii
    public function testTaskTogglePageError403()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/tasks/1/toggle');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }
}
