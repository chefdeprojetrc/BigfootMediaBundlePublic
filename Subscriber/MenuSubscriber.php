<?php

namespace Bigfoot\Bundle\MediaBundle\Subscriber;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Bigfoot\Bundle\CoreBundle\Event\MenuEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Menu Subscriber
 */
class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * @param SecurityContextInterface $security
     */
    public function __construct(SecurityContextInterface $security)
    {
        $this->security = $security;
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            MenuEvent::GENERATE_MAIN => array('onGenerateMain', 7)
        );
    }

    /**
     * @param GenericEvent $event
     */
    public function onGenerateMain(GenericEvent $event)
    {
        $menu = $event->getSubject();
        $root = $menu->getRoot();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $mediaMenu = $root->addChild(
                'media',
                array(
                    'label'          => 'Media',
                    'url'            => '#',
                    'linkAttributes' => array(
                        'class' => 'dropdown-toggle',
                        'icon'  => 'picture',
                    )
                )
            );

            $mediaMenu->setChildrenAttributes(
                array(
                    'class' => 'submenu',
                )
            );

            $mediaMenu->addChild(
                'metadata',
                array(
                    'label'  => 'Metadata',
                    'route'  => 'admin_portfolio_metadata',
                    'extras' => array(
                        'routes' => array(
                            'admin_portfolio_metadata_new',
                            'admin_portfolio_metadata_edit'
                        )
                    ),
                    'linkAttributes' => array(
                        'icon' => 'list',
                    )
                )
            );
        }
    }
}