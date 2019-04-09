<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class UserControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

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

    //List scenarii
    public function testUserListAccess()
    {
        $this->logIn(['ROLE_ADMIN']);
        $crawler = $this->client->request('GET', '/users');
        $this->assertContains("liste des Utiisateurs", $this->client->getResponse()->getContent());
    }

    public function testUserListError403()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/users');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    //Create scenarii
    public function testUserCreatePageAccess()
    {
        $this->logIn(['ROLE_ADMIN']);
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertContains("creation d'utilisateur", $this->client->getResponse()->getContent());
    }

    public function testUserCreatePageError403()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testUserCreatePageSendForm()
    {
        $time = time();
        $this->logIn(['ROLE_ADMIN']);
        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'user_test_' . $time;
        $form['user[password][first]'] = 'password_test';
        $form['user[password][second]'] = 'password_test';
        $form['user[email]'] = 'user_test_' . $time . '@email.fr';
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertContains("utilisateur a bien été ajouté", $this->client->getResponse()->getContent());
    }

    //Edit scenarii
    public function testUserEditPageAccess()
    {
        $this->logIn(['ROLE_ADMIN']);
        $crawler = $this->client->request('GET', '/users/1/edit');
        $this->assertContains("edition d'utilisateur", $this->client->getResponse()->getContent());
    }

    public function testUserEditPageError403()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/users/1/edit');
        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testUserEditPageError404()
    {
        $this->logIn(['ROLE_ADMIN']);
        $crawler = $this->client->request('GET', '/users/99999/edit');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUserEditPageSendForm()
    {
        $time = time();
        $this->logIn(['ROLE_ADMIN']);
        $crawler = $this->client->request('GET', '/users/3/edit');
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'user_modif_' . $time;
        $form['user[password][first]'] = 'password_modif';
        $form['user[password][second]'] = 'password_modif';
        $form['user[email]'] = 'user_modif_' . $time . '@email.fr';
        $form['user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertContains("utilisateur a bien été modifié", $this->client->getResponse()->getContent());
    }
}
