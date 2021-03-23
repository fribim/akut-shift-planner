<?php

namespace App\DataFixtures\ORM;

use App\Entity\Person;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Plan;
use App\Entity\Shift;
use Doctrine\Common\DataFixtures\AbstractFixture;
use libphonenumber\PhoneNumber;

class LoadPlanWithPeople extends AbstractFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@admin.ch');
        $admin->setPlainPassword('12345678');
        $admin->setEnabled(1);
        $this->setReference('admin-user', $admin);

        $adminPlan = new Plan();
        $adminPlan->setDescription('hmm bli blb blu');
        $adminPlan->setDate(new \DateTime('1970-01-01 00:01:00'));
        $adminPlan->setTitle('admin plan');
        $adminPlan->setUser($admin);
        $this->setReference('admin-plan', $adminPlan);


        $adminShift = new Shift();
        $adminShift->setDescription('meiu asdjffs');
        $adminShift->setTitle('admin shift');
        $adminShift->setStart(new \DateTime('1970-01-01 00:01:00'));
        $adminShift->setEnd(new \DateTime('1970-01-01 00:02:00'));
        $adminShift->setNumberPeople(2);
        $adminPlan->addShift($adminShift);
        $this->setReference('admin-shift', $adminShift);

        $phone = new PhoneNumber();
        $phone->setRawInput('+41979787323');
        $personOne = new Person();
        $personOne->setAlias('alias');
        $personOne->setName('private name');
        $personOne->setPhone($phone);
        $personOne->setEmail('asdf@asfd.de');
        $personOne->setShift($adminShift);
        $adminShift->addPerson($personOne);

        $personTwo = new Person();
        $personTwo->setAlias('alias');
        $personTwo->setName('private name');
        $personTwo->setPhone($phone);
        $personTwo->setEmail('asdf@asfd.de');
        $personTwo->setShift($adminShift);
        $adminShift->addPerson($personTwo);


        //save all the things!
        $manager->persist($admin);
        $manager->persist($adminShift);
        $manager->persist($personOne);
        $manager->persist($personTwo);
        $manager->persist($adminPlan);

        $manager->flush();
    }
}
