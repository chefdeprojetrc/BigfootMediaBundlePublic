<?php

namespace Bigfoot\Bundle\UserBundle\DataFixtures\ORM;

use Bigfoot\Bundle\MediaBundle\Entity\Metadata;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadMetadataData
 * @package Bigfoot\Bundle\UserBundle\DataFixtures\ORM
 */
class LoadMetadataData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $title = new Metadata();
        $title->setName('Title');
        $title->setSlug('title');

        $manager->persist($title);

        $width = new Metadata();
        $width->setName('Width');
        $width->setSlug('width');

        $manager->persist($width);

        $height = new Metadata();
        $height->setName('Height');
        $height->setSlug('height');

        $manager->persist($height);

        $size = new Metadata();
        $size->setName('Size');
        $size->setSlug('size');

        $manager->persist($size);

        $repository = $manager->getRepository('Gedmo\\Translatable\\Entity\\Translation');
        $repository->translate($title   , 'name', 'fr', 'Titre');
        $repository->translate($width   , 'name', 'fr', 'Largeur');
        $repository->translate($height  , 'name', 'fr', 'Hauteur');
        $repository->translate($size    , 'name', 'fr', 'Taille');

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 5;
    }
}