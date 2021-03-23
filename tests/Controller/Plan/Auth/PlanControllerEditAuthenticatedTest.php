<?php

namespace Tests\App\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class PlanControllerEditAuthenticatedTest extends WebTestCase
{

    private $crawler;

    private $client;

    private $fixtures;

    public function setUp()
    {
        $this->fixtures = $this->loadFixtures(array(
            'App\DataFixtures\ORM\LoadCompleteDataSet'
        ))->getReferenceRepository();

        $this->loginAs($this->fixtures->getReference('admin-user'), 'main');
        $this->client = $this->makeClient();
        $this->crawler = $this->client->request('GET', '/plan/' . $this->fixtures->getReference('admin-plan')->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEditPlan()
    {
        $link = $this->crawler->filter('.col-12 .pull-right')->link();
        $this->crawler = $this->client->click($link);
        $form = $this->crawler->filter('.btn')->form(array(
            'App_plan[title]' => 'edited title',
            'App_plan[date]' => '2099-06-20',
            'App_plan[description]' => 'new desc',
        ));
        $this->assertContains('00:01', $form->get('App_plan[shifts][0][start]')->getValue());
        $this->assertContains('00:02', $form->get('App_plan[shifts][0][end]')->getValue());

        $values = $form->getPhpValues();

        $values['App_plan']['shifts'][0]['title'] = 'new foo';
        $values['App_plan']['shifts'][0]['orderIndex'] = -5;

        $values['App_plan']['shifts'][1]['title'] = 'new shift';
        $values['App_plan']['shifts'][1]['description'] = 'new new';
        $values['App_plan']['shifts'][1]['start'] = '00:05';
        $values['App_plan']['shifts'][1]['end'] = '00:10';
        $values['App_plan']['shifts'][1]['numberPeople'] = 1;
        $values['App_plan']['shifts'][1]['orderIndex'] = 5;

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->crawler = $this->client->followRedirect();

        $this->assertContains('edited title', $this->crawler->filter('h1')->text());
        $this->assertContains('20.06.2099', $this->crawler->filter('h1')->text());
        $this->assertContains('new desc', $this->crawler->filter('blockquote')->text());
        $this->assertContains('new foo', $this->crawler->filter('.card')->text());
        $this->assertContains('new shift', $this->crawler->filter('.card')->eq(1)->text());
        $this->assertContains('new new', $this->crawler->filter('.card')->eq(1)->text());
        $this->assertContains('00:05', $this->crawler->filter('.card')->eq(1)->text());
        $this->assertContains('00:10', $this->crawler->filter('.card')->eq(1)->text());
        $this->assertContains('/person/new?shift=', $this->crawler->filter('.btn-primary')->attr('href'));
    }

    public function testDeletePlan()
    {
        $link = $this->crawler->filter('.col-12 .pull-right')->link();
        $this->crawler = $this->client->click($link);
        $form = $this->crawler->filter('.btn-danger')->form();
        $this->client->submit($form);
        $this->crawler = $this->client->followRedirect();
        $this->assertNotContains('hmm bli blb blu', $this->crawler->filter('tbody')->text());
    }
}
