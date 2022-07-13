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
use burnthebook\ClickCollect\Records\ShippingMethod as ShippingMethodRecord;

/**
 * Shipping Method model
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
        return static::fetch()->name ?? $this->shippingMethodName;
    }

    public function getIsEnabled() : bool
    {
        return static::fetch()->active ?? $this->shippingMethodEnabled;
    }

    public function getPrice() : int
    {
        return static::fetch()->price ?? $this->shippingMethodPrice;
    }

    public function getCpEditUrl() : string
    {
        return $this->shippingMethodCpEditUrl;
    }

    // Model helper methods

    public static function exists() : bool
    {
        return (bool) ShippingMethodRecord::find()->orderBy(['id' => SORT_ASC])->one();
    }

    /** 
     * Fetch method on model returns an ActiveRecord class, so model can be used as ActiveRecord
     * 
     * @return burnthebook\ClickCollect\Records\ShippingMethod
     */
    public static function fetch() : ShippingMethodRecord
    {
        return ShippingMethodRecord::find()->orderBy(['id' => SORT_ASC])->one();
    }

    public static function fetchById(int $id) : ShippingMethodRecord
    {
        return ShippingMethodRecord::find()->where(['id' => $id])->orderBy(['id' => SORT_ASC])->one();
    }

    public static function create() : ShippingMethodRecord
    {
        return new ShippingMethodRecord;
    }
}