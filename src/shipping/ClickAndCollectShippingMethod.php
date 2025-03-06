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

use craft\commerce\elements\Order;
use Illuminate\Support\Collection;
use craft\commerce\base\ShippingRuleInterface;
use craft\commerce\base\ShippingMethodInterface;
use craft\commerce\base\ShippingMethod as BaseShippingMethod;

class ClickAndCollectShippingMethod implements ShippingMethodInterface
{
    /**
     * @var int|null ID
     */
    public ?int $id = null;

    /**
     * @var int|null Store ID
     */
    // public ?int $storeId = null;

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
     * Returns the type of Shipping Method. This might be the name of the plugin or provider.
     * The core shipping methods have type: `Custom`. This is shown in the control panel only.
     */
    public function getType(): string
    {
        return $this->name;
    }

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
     * Returns the control panel URL to manage this method and its rules.
     * An empty string will result in no link.
     */
    public function getCpEditUrl(): string
    {
        return '';
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

    /**
     * Returns an array of rules that meet the `ShippingRules` interface.
     *
     * @return Collection<ShippingRuleInterface> The array of ShippingRules
     */
    public function getShippingRules(): Collection
    {
        return new Collection([new ClickAndCollectShippingRule()]);
    }


    public function getPriceForOrder(Order $order): float
    {
        return 0;
    }

    /**
     * The first matching shipping rule for this shipping method
     */
    public function getMatchingShippingRule(Order $order): ?ShippingRuleInterface
    {
        return new ClickAndCollectShippingRule();
    }

}
