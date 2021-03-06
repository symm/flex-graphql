<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->seed(1234);

        for ($i = 0; $i < 10; ++$i) {
            $author = new Author($faker->name());
            $this->setReference('author-'.$i, $author);

            $article = new Article($author, $faker->paragraph(), $faker->paragraph());
            $this->setReference('article-'.$i, $article);

            $manager->persist($author);
            $manager->persist($article);
        }

        $manager->flush();
    }
}
