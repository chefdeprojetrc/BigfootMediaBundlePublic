<?php

namespace Bigfoot\Bundle\MediaBundle\Listener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Bigfoot\Bundle\CoreBundle\Event\MenuEvent;

/**
 * Menu Listener
 */
class MenuListener implements EventSubscriberInterface
{
    /**
     * Get subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            MenuEvent::GENERATE_MAIN => 'onGenerateMain',
        );
    }

    /**
     * @param GenericEvent $event
     */
    public function onGenerateMain(GenericEvent $event)
    {
        $menu      = $event->getSubject();
        $mediaMenu = $menu->getChild('media');

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