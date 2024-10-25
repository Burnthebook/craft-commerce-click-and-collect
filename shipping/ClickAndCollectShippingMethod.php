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

namespace burnthebook\craftcommerceclickandcollect\shipping;

use craft\commerce\base\ShippingRuleInterface;
use craft\commerce\base\ShippingMethodInterface;
use craft\commerce\base\ShippingMethod as BaseShippingMethod;
use craft\commerce\elements\Order;

class ClickAndCollectShippingMethod extends BaseShippingMethod implements ShippingMethodInterface
{
    /**
     * @var int|null ID
     */
    public ?int $id = null;

    /**
     * @var string|null Name
     */
    public ?string $name = 'Click & Collect';

    /**
     * @var string|null Handle
     */
    public ?string $handle = 'clickAndCollect';

    /**
     * @var bool Enabled
     */
    public bool $enabled = true;

    /**
     * Returns the ID of the current object.
     *
     * @return int|null The ID of the current object, or null if it does not have an ID.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the name of the object.
     *
     * @return string The name of the object.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the handle of the current object as a string.
     *
     * @return string The handle of the current object.
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * Returns the value of the "isEnabled" property as a boolean.
     *
     * @return bool The value of the "isEnabled" property.
     */
    public function getIsEnabled(): bool
    {
        return true;
    }

    /**
     * Returns a boolean value indicating if the item is available.
     *
     * @return bool True if the item is available, false otherwise.
     */
    public function getIsAvailable(): bool
    {
        return true;
    }

    /**
     * Matches the given Order object with the current order and returns a boolean value.
     *
     * @param Order $order The Order object to be matched.
     * @return bool Returns true if the given Order object matches the current order, false otherwise.
     */
    public function matchOrder(Order $order): bool
    {
        return true;
    }

    /**
     * Retrieves the settings for this application.
     *
     * @return array An array containing the settings for this application.
     */
    public function getSettings(): array
    {
        return [];
    }

    /**
     * Returns an array of shipping rules.
     *
     * @return array An array of shipping rules, currently only containing a ClickAndCollectShippingRule.
     */
    public function getRules(): array
    {
        return [new ClickAndCollectShippingRule()];
    }
}
