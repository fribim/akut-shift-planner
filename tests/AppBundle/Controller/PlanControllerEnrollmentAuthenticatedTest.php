<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test Enrollment with auth
 *
 * @package Tests\AppBundle\Controller
 */
class PlanControllerEnrollmentAuthenticatedTest extends WebTestCase
{
    private $crawler;

    private $client;

    public function setUp() {
        $fixtures = $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadPlanWithPeople'
        ))->getReferenceRepository();
        $this->loginAs($fixtures->getReference('admin-user'), 'main');
        $planRef = $fixtures->getReference('admin-plan');
        $this->client = $this->makeClient();
        $this->crawler = $this->client->request('GET', '/plan/' . $planRef->getId());
    }

    public function testSimpleEnrollment()
    {
        $this->assertContains('/person/1/edit', $this->crawler->filter('ol > li > a')->eq(0)->attr('href'));
        $this->assertContains('private name', $this->crawler->filter('ol > li')->eq(0)->text());
        $this->assertContains('mailto:asdf@asfd.de?Subject=Kontakt Schichtplan: admin plan',
            $this->crawler->filter('#person-details li > a')->eq(0)->attr('href'));
        $this->assertContains('09797873', $this->crawler->filter('#person-details li')->eq(1)->text());

        $this->assertContains('09797873', $this->crawler->filter('#person-details:nth-child(2) li')->eq(1)->text());
        $this->assertContains('mailto:asdf@asfd.de?Subject=Kontakt Schichtplan: admin plan',
            $this->crawler->filter('#person-details li > a')->eq(1)->attr('href'));
        $this->assertContains('/person/2/edit', $this->crawler->filter('ol > li > a')->eq(1)->attr('href'));
        $this->assertContains('private name', $this->crawler->filter('ol > li')->eq(1)->text());
    }

    //TODO due teste ob au editierbar si und due se modifye, ou plan mit shift
//    public function testEditEnrollment()
//    {
//        $link = $this->crawler->filter('ol > li > a')->eq(0)->link();
//        $crawler = $this->client->click($link);
//        $form = $crawler->filter('.btn')->form(array(
//            'appbundle_person[name]' => 'p',
//            'appbundle_person[alias]' => 'a',
//            'appbundle_person[email]' => 'testenroll.ch',
//            'appbundle_person[phone]' => '079343134343'
//        ));
//
//        $crawler = $this->client->submit($form);
//        $this->assertEquals(3, $crawler->filter('.alert')->count());
//    }
}
