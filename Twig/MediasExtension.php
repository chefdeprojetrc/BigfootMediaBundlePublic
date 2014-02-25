<?php

namespace Bigfoot\Bundle\MediaBundle\Twig;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Symfony\Component\DependencyInjection\Container;

/**
 * Helper filter facilitating the display of an image from the portfolio.
 * Class MediasExtension
 * @package Bigfoot\Bundle\MediaBundle\Twig
 */
class MediasExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('medias', array($this, 'mediasFilter'))
        );
    }

    /**
     * @param $value
     * @return string
     */
    public function mediasFilter($value)
    {
        $ids = explode(';',$value);
        $em = $this->container->get('doctrine')->getManager();
        $request = $this->container->get('request');
        $result = $em->getRepository('Bigfoot\Bundle\MediaBundle\Entity\Media')->findBy(array('id' => $ids));
        $orderedMedias = array();

        if ($ids) {
            $orderedMedias = array_flip($ids);
            foreach ($result as $media) {
                $orderedMedias[$media->getId()] = sprintf('%s/%s', $request->getBasePath(), $media->getFile());
            }
        }

        return $orderedMedias;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'medias';
    }
}