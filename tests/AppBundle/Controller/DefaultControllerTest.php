<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIndexNotLogged()
    {
        $crawler = $this->client->request('GET', '/');
		$this->assertTrue(
		    $this->client->getResponse()->isRedirect('http://localhost/login')
		);

        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/');
        $this->assertContains("Mot de passe :", $this->client->getResponse()->getContent());
    }

    /**
    * @dataProvider demoProvider
    */
    public function testDemo($statusCode, $url)
    {
        $crawler = $this->client->request('GET', $url);
        $statusCodeExcpected = $this->client->getResponse()->getStatusCode();
        $this->assertEquals($statusCode, $statusCodeExcpected);
    }

    public function demoProvider()
    {
        return [
            [200, '/login'],
            [302, '/']
        ];
    }

    public function testIndexLogged()
    {
        $this->logIn(['ROLE_USER']);
        $crawler = $this->client->request('GET', '/');
        $this->assertTrue(
            !$this->client->getResponse()->isRedirect()
        );
        $this->assertContains("Vous pouvez dés maintenant", $this->client->getResponse()->getContent());
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
}
