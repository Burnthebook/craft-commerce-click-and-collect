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

use craft\commerce\models\ShippingRule as BaseShippingRule;
use craft\commerce\base\ShippingRuleInterface;
use craft\commerce\elements\Order;

class ClickAndCollectShippingRule extends BaseShippingRule implements ShippingRuleInterface
{
    /**
     * Matches the given Order object against a set of criteria and returns a boolean value.
     *
     * @param Order $order The Order object to be matched.
     * @return bool Returns true if the Order object matches the criteria, false otherwise.
     */
    public function matchOrder(Order $order): bool
    {
        return true;
    }

    /**
     * Returns a boolean value indicating whether the feature is enabled or not.
     *
     * @return bool True if the feature is enabled, false otherwise.
     */
    public function getIsEnabled(): bool
    {
        return true;
    }

    /**
     * Returns the options for this object.
     *
     * @return array The attributes for this object.
     */
    public function getOptions(): array
    {
        return $this->getAttributes();
    }

    /**
     * Returns the percentage rate for the given shipping category.
     *
     * @param string $shippingCategory The shipping category to get the percentage rate for.
     * @return float The percentage rate for the given shipping category.
     */
    public function getPercentageRate(?int $shippingCategoryId = null): float
    {
        return 0;
    }

    /**
     * Returns the per item shipping rate for a given shipping category.
     *
     * @param string $shippingCategory The category of the item being shipped.
     * @return float The per item shipping rate.
     */
    public function getPerItemRate(?int $shippingCategoryId = null): float
    {
        return 0;
    }

    /**
     * Calculates the weight rate for a given shipping category.
     *
     * @param string $shippingCategory The category of the shipment.
     * @return float The weight rate for the given shipping category.
     */
    public function getWeightRate(?int $shippingCategoryId = null): float
    {
        return 0;
    }

    /**
     * Returns the base rate as a float value.
     *
     * @return float The base rate.
     */
    public function getBaseRate(): float
    {
        return 0;
    }

    /**
     * Returns the maximum rate for a given item.
     *
     * @return float The maximum rate.
     */
    public function getMaxRate(): float
    {
        return 0;
    }

    /**
     * Returns the minimum rate as a float value.
     *
     * @return float The minimum rate.
     */
    public function getMinRate(): float
    {
        return 0;
    }

    /**
     * Returns the description for the Click & Collect Shipping Rule.
     *
     * @return string The description for the Click & Collect Shipping Rule.
     */
    public function getDescription(): string
    {
        return 'Click & Collect Shipping Rule';
    }
}
