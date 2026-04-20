<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'Pièces uniques', 'slug' => 'pieces-uniques'],
            ['name' => 'Accessoires', 'slug' => 'accessoires'],
            ['name' => 'Décoration', 'slug' => 'decoration'],
            ['name' => 'Bébés & enfants', 'slug' => 'bebes-enfants'],
            ['name' => 'Mode & vêtements', 'slug' => 'mode-vetements'],
            ['name' => 'Cadeaux', 'slug' => 'cadeaux'],
        ];

        foreach ($categories as $cat) {
            $category = new Category();
            $category->setName($cat['name']);
            $category->setSlug($cat['slug']);
            $manager->persist($category);
        }

        $manager->flush();
    }
}