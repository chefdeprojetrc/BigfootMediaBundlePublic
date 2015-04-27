<?php

namespace Bigfoot\Bundle\MediaBundle\Provider\Common;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Bigfoot\Bundle\CoreBundle\Theme\Theme;

/**
 * Abstract media provider
 */
abstract class AbstractMediaProvider
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var Session
     */
    protected $session;

    // public function __construct(EntityManager $entityManager, Theme $theme, Session $session)
    // {
    //     $this->entityManager = $entityManager;
    //     $this->theme = $theme;
    //     $this->session = $session;
    // }

    /**
     * Sets the value of entityManager.
     *
     * @param EntityManager $entityManager the entity manager
     *
     * @return self
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Sets the value of theme.
     *
     * @param Theme $theme the theme
     *
     * @return self
     */
    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Sets the value of session.
     *
     * @param Session $session the session
     *
     * @return self
     */
    public function setSession(Session $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get repository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepository($this->getClassname());
    }

    /**
     * Get theme bundle
     *
     * @return string
     */
    protected function getThemeBundle()
    {
        return $this->theme->getTwigNamespace();
    }

    /**
     * Get configuration
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    public function getConfiguration($key = null, $default = null)
    {
        $configuration = $this->configuration();

        if (empty($key)) {
            return is_array($configuration) ? $configuration : array();
        }

        if (!is_array($configuration)) {
            return $default;
        }

        if (!isset($configuration[$key])) {
            return $default;
        }

        return $configuration[$key];
    }

    /**
     * Get class name
     *
     * @return string
     */
    abstract public function getClassname();

    /**
     * Get form type
     *
     * @return string
     */
    abstract public function getFormType();

    /**
     * Get form template
     *
     * @return string
     */
    abstract public function getFormTemplate();

    /**
     * Get form template
     *
     * @return string
     */
    abstract public function getLineTemplate();

    /**
     * Configuration
     *
     * @return array
     */
    abstract protected function configuration();

    /**
     * Get url
     *
     * @param  Request $request
     * @param  mixed  $media
     *
     * @return string
     */
    abstract public function getUrl(Request $request, $media);

    /**
     * Get media details
     *
     * @param  mixed $media
     *
     * @return array
     */
    abstract public function getMediaDetails($media);

    /**
     * Get total
     *
     * @return integer
     */
    abstract public function getTotal();

    /**
     * Find medias
     *
     * @param  integer $offset
     * @param  integer $limit
     *
     * @return array
     */
    abstract public function findAll($offset = 0, $limit = 20);

    /**
     * Get medias
     *
     * @param  mixed $identifier
     *
     * @return array
     */
    abstract public function find($identifier);

    /**
     * Search
     *
     * @param  mixed  $model
     * @param  integer $offset
     * @param  integer $limit
     *
     * @return array
     */
    abstract public function search($model, $offset = 0, $limit = 20);
}
