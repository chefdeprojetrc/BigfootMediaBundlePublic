<?php

namespace Bigfoot\Bundle\MediaBundle\Twig;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Symfony\Component\DependencyInjection\Container;

class MediaExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('media', array($this, 'mediaFilter'))
        );
    }

    public function mediaFilter($value)
    {
        $ids = explode(',', $value);

        $em = $this->container->get('doctrine')->getManager();
        $request = $this->container->get('request');

        $query = $em
            ->createQuery('SELECT m FROM Bigfoot\Bundle\MediaBundle\Entity\Media m WHERE m.id IN (:ids)')
            ->setParameter('ids', $ids);
        $media = $query->getOneOrNullResult();

        return sprintf('%s/%s', $request->getBasePath(), $media->getFile());
    }

    public function getName()
    {
        return 'media';
    }
}