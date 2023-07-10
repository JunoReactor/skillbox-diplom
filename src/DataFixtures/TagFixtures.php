<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Persistence\ObjectManager;

/**
 * Load data fixtures with the passed EntityManager
 *
 * @param ObjectManager $manager
 */
class TagFixtures extends BaseFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Tag::class, 50, function (Tag $tag) {
            $tag
                ->setName($this->faker->realText(20))
                ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 day'))
            ;

            if ($this->faker->boolean) {
                $tag->setDeletedAt($this->faker->dateTimeThisMonth);
            }
        });

        $manager->flush();
    }
}
