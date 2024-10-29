<?php

/**
 * Click and Collect plugin for Craft CMS 4.x
 *
 * Add drop in Click and Collect functionality to Craft Commerce.
 *
 * @link        https://burnthebook.co.uk
 * @author      Michael Burton <mikey@burnthebook.co.uk>
 * @since       0.0.1
 * @copyright   Copyright (c) 2022 burnthebook
 */

namespace burnthebook\craftcommerceclickandcollect;

use Craft;
use craft\web\View;
use yii\base\Event;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\commerce\elements\Order;
use craft\events\RegisterUrlRulesEvent;
use craft\commerce\services\OrderHistory;
use craft\web\twig\variables\CraftVariable;
use craft\commerce\services\ShippingMethods;
use craft\events\RegisterTemplateRootsEvent;
use craft\commerce\events\OrderCompleteEvent;
use craft\commerce\services\RegisterShippingMethods;
use craft\commerce\events\RegisterAvailableShippingMethodsEvent;
use burnthebook\craftcommerceclickandcollect\shipping\ClickAndCollectShippingMethod;

class ClickAndCollect extends Plugin
{
    public static $plugin;

    /**
    * Initializes the plugin.
    */
    public function init()
    {
        parent::init();

        // Register services
        $this->setComponents([
            'collectionPoints' => services\CollectionPointService::class,
            'collectionTimes' => services\CollectionTimeService::class,
            'orderService' => services\OrderService::class,
        ]);

        self::$plugin = $this;
        self::$plugin->orderService->createFields();

        // Register the shipping method
        Event::on(
            ShippingMethods::class,
            ShippingMethods::EVENT_REGISTER_AVAILABLE_SHIPPING_METHODS,
            function (RegisterAvailableShippingMethodsEvent $event) {
                $shippingMethod = new ClickAndCollectShippingMethod();
                \Craft::info('Adding shipping method: ' . get_class($shippingMethod), __METHOD__);
                \Craft::info('Before adding, shippingMethods: ' . print_r($event->shippingMethods, true), __METHOD__);
                $event->shippingMethods[] = $shippingMethod;
                \Craft::info('After adding, shippingMethods: ' . print_r($event->shippingMethods, true), __METHOD__);
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['craft-commerce-click-and-collect'] = 'craft-commerce-click-and-collect/default/index';

                $event->rules['craft-commerce-click-and-collect/collection-points'] = 'craft-commerce-click-and-collect/collection-points/index';
                $event->rules['craft-commerce-click-and-collect/collection-points/new'] = 'craft-commerce-click-and-collect/collection-points/edit';
                $event->rules['craft-commerce-click-and-collect/collection-points/edit/<collectionPointId:\d+>'] = 'craft-commerce-click-and-collect/collection-points/edit';
                $event->rules['craft-commerce-click-and-collect/collection-points/delete/<collectionPointId:\d+>'] = 'craft-commerce-click-and-collect/collection-points/delete';

                $event->rules['craft-commerce-click-and-collect/collection-times'] = 'craft-commerce-click-and-collect/collection-times/index';
                $event->rules['craft-commerce-click-and-collect/collection-times/new'] = 'craft-commerce-click-and-collect/collection-times/edit';
                $event->rules['craft-commerce-click-and-collect/collection-times/edit/<collectionTimeId:\d+>'] = 'craft-commerce-click-and-collect/collection-times/edit';
                $event->rules['craft-commerce-click-and-collect/collection-times/delete/<collectionTimeId:\d+>'] = 'craft-commerce-click-and-collect/collection-times/delete';
            }
        );

        // Event::on(
        //     Order::class,
        //     Order::EVENT_BEFORE_COMPLETE_ORDER,
        //     function (Event $event) {
        //         $order = $event->order;

        //         if ($event->sender->firstSave) {
        //             // Check if it's a Click & Collect order
        //             if ($order->getShippingMethod()->handle === 'clickAndCollect') {
        //                 // Perform actions specific to Click & Collect orders
        //             }
        //         }
        //     }
        // );

        // Register your variable to the `craft` variable in Twig
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('clickAndCollect', \burnthebook\craftcommerceclickandcollect\variables\ClickAndCollectVariable::class);
            }
        );

        // Register the site template root
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event) {
                $event->roots['craft-commerce-click-and-collect'] = __DIR__ . '/templates';
            }
        );
    }

    /**
     * Retrieves the control panel navigation item for the Click & Collect plugin.
     *
     * @return array|null The navigation item for the Click & Collect plugin.
     */
    public function getCpNavItem(): ?array
    {
        $item = parent::getCpNavItem();
        $item['label'] = 'Click & Collect';
        $item['url'] = 'craft-commerce-click-and-collect';
        $item['subnav'] = [
            'collection-points' => ['label' => 'Collection Points', 'url' => 'craft-commerce-click-and-collect/collection-points'],
            'collection-times' => ['label' => 'Opening Times', 'url' => 'craft-commerce-click-and-collect/collection-times'],
        ];

        return $item;
    }

    /**
     * Defines the permissions for managing collection points and times for the Craft Commerce Click and Collect plugin.
     *
     * @return array The permissions with their corresponding labels.
     */
    public function definePermissions()
    {
        return [
            'craft-commerce-click-and-collect:manageCollectionPoints' => [
                'label' => Craft::t('app', 'Manage Collection Points'),
            ],
            'craft-commerce-click-and-collect:manageCollectionTimes' => [
                'label' => Craft::t('app', 'Manage Opening Times'),
            ],
        ];
    }
}
