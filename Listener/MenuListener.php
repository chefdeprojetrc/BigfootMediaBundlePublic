<?php

namespace Bigfoot\Bundle\MediaBundle\Listener;

use Bigfoot\Bundle\CoreBundle\Event\MenuEvent;
use Bigfoot\Bundle\CoreBundle\Theme\Menu\Item;

class MenuListener
{
    public function onMenuGenerate(MenuEvent $event)
    {
        $menu = $event->getMenu();
        if ('sidebar_menu' == $menu->getName()) {
            $media = new Item('sidebar_settings_media', 'Media');
            $media->addChild(new Item('sidebar_settings_media_metadata', 'Metadata management', 'admin_portfolio_metadata'));
            $menu->addOnItem('sidebar_settings', $media);
        }
    }
}