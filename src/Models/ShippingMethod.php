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

namespace burnthebook\ClickCollect\Models;

use craft\base\Model;

/**
 * Plugin settings object, containing values for configuring the plugin
 *
 * @author    burnthebook
 * @package   ClickCollect
 * @since     0.0.1
 */
class ShippingMethod extends Model 
{
    /**
     * @var string|null The name of the Shipping Method
     */
    public ?string $shippingMethodName = 'Click & Collect';

    /**
     * @var bool Is this shipping method enabled?
     */
    public bool $shippingMethodEnabled = true;

    /**
     * @var int The price of the shipping method (in pence/cents)
     */
    public int $shippingMethodPrice = 199;

    /**
     * @var string|null The Shipping Method CP Edit URL
     */
    public ?string $shippingMethodCpEditUrl = '/admin/craft-commerce-click-and-collect/shipping-method';

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [
                [
                    'shippingMethodName'
                ], 'required'
            ],
            [
                [
                    'shippingMethodEnabled'
                ]
            ],
            [
                [
                    'shippingMethodPrice'
                ]
            ],
            [
                [
                    'shippingMethodCpEditUrl'
                ]
            ],
        ];
    }

    public function getName() : string
    {
        return $this->shippingMethodName;
    }

    public function getIsEnabled() : bool
    {
        return $this->shippingMethodEnabled;
    }

    public function getPrice() : int
    {
        return $this->shippingMethodPrice;
    }

    public function getCpEditUrl() : string
    {
        return $this->shippingMethodCpEditUrl;
    }
}