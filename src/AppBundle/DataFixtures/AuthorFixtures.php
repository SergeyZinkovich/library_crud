<?php


namespace AppBundle\DataFixtures;


use AppBundle\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{
    private $names = ['Blaze', 'Shona', 'James'];  // , 'Eileen', 'Jack', 'Julia'
    private $surnames = ['Garrison', 'Chapman', 'Wheeler'];  // , 'Morton', 'Atkins', 'Crawford'

    /**
     * Generates 9 authors (names * surnames)
     *
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $authors = [];
        foreach ($this->names as $name) {
            foreach ($this->surnames as $surname) {
                $authors[] = new Author();
                end($authors)->setName($name . ' ' . $surname);
                $manager->persist((end($authors)));
            }
        }
        $manager->flush();
    }
}