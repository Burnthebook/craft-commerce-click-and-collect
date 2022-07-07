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

namespace burnthebook\ClickCollect;

use Craft;
use yii\base\Event;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use craft\commerce\services\ShippingMethods;
use burnthebook\ClickCollect\Models\Settings;
use burnthebook\ClickCollect\Models\ShippingMethod;
use burnthebook\ClickCollect\Variables\ClickCollectVariable;
use burnthebook\ClickCollect\Services\ClickAndCollectService;
use craft\commerce\events\RegisterAvailableShippingMethodsEvent;

/**
 * Plugin
 *
 * @author    burnthebook
 * @package   ClickCollect
 * @since     0.0.1
 */
class ClickCollect extends Plugin
{
    /**
     * @var ClickCollect
     */
    public static $plugin;

    /**
     * @var Settings
     */
    public static $settings;

    /**
     * @var bool
     */
    public static $savingSettings = false;

    // Edition constants
    public const EDITION_LITE = 'lite';
    public const EDITION_PRO = 'pro';

    // Plugin constants
    public const PLUGIN_NAME = 'Click & Collect';
    public const PLUGIN_HANDLE = 'craft-commerce-click-and-collect';

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public bool $hasCpSection = true;

    /**
     * @inheritdoc
     */
    public static function editions(): array
    {
        return [
            self::EDITION_LITE,
            self::EDITION_PRO,
        ];
    }


    public function init()
    {
        parent::init();
        self::$plugin = $this;
        self::$settings = self::$plugin->getSettings();

        // $this->setComponents([
        //     'shippingMethod' => ClickAndCollectService::class
        // ]);

        $this->name = self::$settings->pluginName;
        $this->registerShippingMethod();
        $this->_registerVariables();

        // We're loaded
        Craft::info(
            Craft::t(
                self::PLUGIN_HANDLE,
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function getPluginHandle()
    {
        return self::PLUGIN_HANDLE;
    }

    public function getPluginName()
    {
        return Craft::t(self::PLUGIN_HANDLE, $this->getSettings()->pluginName);
    }

    public function registerShippingMethod() {
        Event::on(
            ShippingMethods::class,
            ShippingMethods::EVENT_REGISTER_AVAILABLE_SHIPPING_METHODS,
            function(RegisterAvailableShippingMethodsEvent $event) {
                $event->shippingMethods[] = new ClickAndCollectService();
            }
        );
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): null|Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): null|string
    {
        return Craft::$app->getView()->renderTemplate(
            self::PLUGIN_HANDLE . '/settings',
            [
                'settings' => $this->getSettings() 
            ]
        );
    }

    public function getCpNavItem() : ?array
    {
        $item = parent::getCpNavItem();
        // $item['badgeCount'] = 5;
        $item['subnav'] = [
            'dashboard' => [
                'label' => 'Dashboard', 
                'url' => self::PLUGIN_HANDLE . '/dashboard'
            ],
            'shipping-method' => [
                'label' => 'Shipping Method', 
                'url' => self::PLUGIN_HANDLE . '/shipping-method'
            ],
            'collection-points' => [
                'label' => 'Collection Points', 
                'url' => self::PLUGIN_HANDLE . '/collection-points'
            ],
            'settings' => [
                'label' => 'Settings', 
                'url' => 'settings/plugins/' . self::PLUGIN_HANDLE
            ],
        ];
        return $item;
    }

    private function _registerVariables()
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('clickcollect', ClickCollectVariable::class);
        });
    }

    public function getShippingMethod()
    {
        $shippingModel = new ShippingMethod;
        return $shippingModel;
    }
}