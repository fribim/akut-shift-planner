<?php

namespace Tests\App\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test Plan Controller from authenticated perspective
 *
 * Class PlanControllerTest
 * @package Tests\App\Controller
 */
class PlanControllerAuthenticatedTest extends WebTestCase
{

    private $crawler;

    private $client;

    public function setUp()
    {
        $fixtures = $this->loadFixtures(array(
            'App\DataFixtures\ORM\LoadUserData'
        ))->getReferenceRepository();

        $this->loginAs($fixtures->getReference('admin-user'), 'main');
        $this->client = $this->makeClient();
        $this->client->request('GET', '/plan');
        $this->assertEquals(301, $this->client->getResponse()->getStatusCode());
        $this->crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testIndex()
    {
        $this->assertContains('Schichtplan Liste', $this->crawler->filter('.justify-content-end')->text());
        $this->assertContains('Warnung! Keine kommenden Schichtpläne', $this->crawler->filter('.alert-warning')->text());
    }

    public function testCreatePlan()
    {
        $this->crawler = $this->client->request('GET', '/plan/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $this->crawler->filter('.btn')->form(array(
            'App_plan[title]' => 'test plan',
            'App_plan[date]' => '2099-06-20',
            'App_plan[description]' => 'some desc',
        ));

        $values = $form->getPhpValues();

        $values['App_plan']['shifts'][0]['title'] = 'foo';
        $values['App_plan']['shifts'][0]['description'] = 'bar';
        $values['App_plan']['shifts'][0]['start'] = '00:00';
        $values['App_plan']['shifts'][0]['end'] = '00:01';
        $values['App_plan']['shifts'][0]['numberPeople'] = 3;

        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values,
            $form->getPhpFiles());

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals(0, $crawler->filter('.alert')->count());

        //test plan_show page
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('test plan', $crawler->filter('.justify-content-end')->text());
        $this->assertContains('20.06.2099', $crawler->filter('.justify-content-end')->text());
        $this->assertContains('some desc', $crawler->filter('blockquote')->text());
        $this->assertContains('foo', $crawler->filter('.card')->eq(0)->text());
        $this->assertContains('bar', $crawler->filter('.card')->eq(0)->text());
        $this->assertContains('00:00', $crawler->filter('.card')->eq(0)->text());
        $this->assertContains('00:01', $crawler->filter('.card')->eq(0)->text());
        $this->assertContains('edit', $crawler->filter('.main-row .col-12')->eq(1)->filter('a')->attr('href'));
        $this->assertEquals(0, $crawler->filter('#passwordPrompt')->count());
        $this->assertContains('3', $crawler->filter('.progress')->eq(0)->text());

        //go to overview page
        $this->client->request('GET', '/plan');
        $this->crawler = $this->client->followRedirect();
        $this->assertContains('test plan', $this->crawler->text());
    }

    public function testCreatePlanTemplate()
    {
        $this->crawler = $this->client->request('GET', '/plan/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $this->crawler->filter('.btn')->form(array(
            'App_plan[title]' => 'test plan template',
            'App_plan[date]' => '2099-06-20',
            'App_plan[description]' => 'some desc',
            'App_plan[isTemplate]' => true,
            'App_plan[isPublic]' => true
        ));

        $values = $form->getPhpValues();

        $values['App_plan']['shifts'][0]['title'] = 'foo';
        $values['App_plan']['shifts'][0]['description'] = 'bar';
        $values['App_plan']['shifts'][0]['start'] = '00:00';
        $values['App_plan']['shifts'][0]['end'] = '00:01';
        $values['App_plan']['shifts'][0]['numberPeople'] = 3;

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        //test plan_show page
        $this->assertContains('test plan template', $crawler->filter('.justify-content-end')->text());
        $this->assertContains('some desc', $crawler->filter('blockquote')->text());
        $this->assertContains('foo', $crawler->filter('.card')->eq(0)->text());
        $this->assertContains('bar', $crawler->filter('.card')->eq(0)->text());
        $this->assertContains('00:00', $crawler->filter('.card')->eq(0)->text());
        $this->assertContains('00:01', $crawler->filter('.card')->eq(0)->text());
        $this->assertContains('edit', $crawler->filter('.main-row .col-12')->eq(1)->filter('a')->attr('href'));
        $this->assertEquals(0, $crawler->filter('#passwordPrompt')->count());
        $this->assertContains('3', $crawler->filter('.progress')->eq(0)->text());

        //go to overview page
        $this->crawler = $this->client->request('GET', '/plan/templates');
        $this->assertContains('test plan template', $this->crawler->text());
        $this->assertEquals(1, $this->crawler->filter('td > a')->count());
        $this->assertEquals('Bearbeiten', $this->crawler->filter('td > a')->eq(0)->text());
    }

    public function testCreateWithoutShiftPlan()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/plan/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->filter('.btn')->form(array(
            'App_plan[title]' => 'test plan',
            'App_plan[date]' => '2099-06-20',
            'App_plan[description]' => 'some desc'
        ));

        $crawler = $client->submit($form);
        $this->assertEquals(5, $crawler->filter('.alert')->count());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreatePlanErrors()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/plan/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->filter('.btn')->form(array(
            'App_plan[title]' => 't',
            'App_plan[date]' => '03.01.2017',
            'App_plan[description]' => 's'
        ));

        $values = $form->getPhpValues();

        $values['App_plan']['shifts'][0]['description'] = '';
        $values['App_plan']['shifts'][0]['title'] = '';
        $values['App_plan']['shifts'][0]['start'] = '00:00:sdf';
        $values['App_plan']['shifts'][0]['end'] = '00:0:sdf';
        $values['App_plan']['shifts'][0]['numberPeople'] = 0;

        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,
            $form->getPhpFiles());

        $this->assertEquals(8, $crawler->filter('.alert')->count());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
