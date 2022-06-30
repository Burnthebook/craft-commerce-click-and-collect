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
use craft\commerce\services\ShippingMethods;
use burnthebook\ClickCollect\Models\Settings;
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
    public static function editions(): array
    {
        return [
            self::EDITION_LITE,
            self::EDITION_PRO,
        ];
    }

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public bool $hasCpSection = true;

    public function init()
    {
        parent::init();
        self::$plugin = $this;
        self::$settings = self::$plugin->getSettings();
        $this->name = self::$settings->pluginName;
        $this->registerShippingMethod();

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

    public function registerShippingMethod() {
        Event::on(
            ShippingMethods::class,
            ShippingMethods::EVENT_REGISTER_AVAILABLE_SHIPPING_METHODS,
            function(RegisterAvailableShippingMethodsEvent $event) {
                $event->shippingMethods[] = new MyShippingMethod();
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
    public function getSettings(): ?Settings
    {
        $settingsModel = parent::getSettings();
        if ($settingsModel !== null && !self::$savingSettings) {
            $attributes = $settingsModel->attributes();
            if ($attributes !== null) {
                foreach ($attributes as $attribute) {
                    if (is_string($settingsModel->$attribute)) {
                        $settingsModel->$attribute = html_entity_decode(
                            $settingsModel->$attribute,
                            ENT_NOQUOTES,
                            'UTF-8'
                        );
                    }
                }
            }
            self::$savingSettings = false;
        }
        
        return $settingsModel;
    }

    protected function settingsHtml(): null|string
    {
        return \Craft::$app->getView()->renderTemplate(
            self::PLUGIN_HANDLE . '/settings',
            [
                'settings' => $this->getSettings() 
            ]
        );
    }
}