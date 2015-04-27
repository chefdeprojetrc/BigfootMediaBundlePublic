<?php

namespace Bigfoot\Bundle\MediaBundle\Twig;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RequestStack;

use Bigfoot\Bundle\MediaBundle\Provider\Common\AbstractMediaProvider;

/**
 * Helper filter facilitating the display of an image from the portfolio.
 * Class MediasExtension
 * @package Bigfoot\Bundle\MediaBundle\Twig
 */
class MediasExtension extends \Twig_Extension
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var AbstractMediaProvider
     */
    private $provider;

    /**
     * Sets the value of requestStack.
     *
     * @param RequestStack $requestStack the request stack
     *
     * @return self
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();

        return $this;
    }

    /**
     * Sets the value of provider.
     *
     * @param AbstractMediaProvider $provider the provider
     *
     * @return self
     */
    public function setProvider(AbstractMediaProvider $provider)
    {
        $this->provider = $provider;

        return $this;
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
    * @return array
    */
    public function getFunctions()
    {
        return array(
            'media_details' => new \Twig_Function_Method($this, 'getMediasWithDetails'),
        );
    }

    /**
     * @param $value
     * @return string
     */
    public function getMediasWithDetails($value)
    {
        $orderedMedias = array();

        if ($value) {
            $ids       = explode(';', $value);
            $results   = $this->provider->find($ids);
            $className = $this->provider->getClassName();

            if ($ids) {
                foreach ($results as $media) {
                    if (!$media instanceof $className) {
                        continue;
                    }

                    $orderedMedias[$media->getId()] = $this->provider->getMediaDetails($this->request, $media);
                }
            }
        }

        return $orderedMedias;
    }

    /**
     * @param $value
     * @return string
     */
    public function mediasFilter($value, $entities = false)
    {
        $orderedMedias = array();

        if ($value) {
            $ids       = explode(';', $value);
            $results   = $this->provider->find($ids);
            $className = $this->provider->getClassName();

            if ($entities) {
                return $results;
            }

            if ($ids) {
                foreach ($results as $media) {
                    if (!$media instanceof $className) {
                        continue;
                    }

                    $orderedMedias[$media->getId()] = $this->provider->getUrl($this->request, $media);
                }
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
