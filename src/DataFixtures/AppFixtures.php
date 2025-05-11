<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\EmployeeFactory;
use App\Factory\ProjectFactory;
use App\Factory\SlotFactory;
use App\Factory\StatusFactory;
use App\Factory\TagFactory;
use App\Factory\TaskFactory;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        // Employee
        EmployeeFactory::createMany(20);
        // Project
        ProjectFactory::createMany(5);
        // Status
        StatusFactory::createMany(4);
        // Tag
        TagFactory::createMany(5);
        // Task
        TaskFactory::createMany(4);
        // Slot
        SlotFactory::createMany(4);

        $manager->flush();
    }

}
