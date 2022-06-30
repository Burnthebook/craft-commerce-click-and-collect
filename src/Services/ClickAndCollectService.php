<?php
namespace burnthebook\ClickCollect\Services;

use Craft;
use DateTime;
use yii\base\Event;
use yii\base\Component;
use craft\commerce\elements\Order;
use craft\commerce\base\ShippingRuleInterface;

class ClickAndCollectService extends Component
{
    /**
     * @var int|null ID
     */
    public ?int $id = null;

    /**
     * @var string|null Name
     */
    public ?string $name = null;

    /**
     * @var string|null Handle
     */
    public ?string $handle = null;

    /**
     * @var bool Enabled
     */
    public bool $enabled = true;

    /**
     * @var DateTime|null
     * @since 3.4
     */
    public ?DateTime $dateCreated = null;

    /**
     * @var DateTime|null
     * @since 3.4
     */
    public ?DateTime $dateUpdated = null;

    /**
     * @var bool Is this the shipping method for the lite edition.
     */
    public bool $isLite = false;


    /**
     * Returns the type of Shipping Method. This might be the name of the plugin or provider.
     * The core shipping methods have type: `Custom`. This is shown in the control panel only.
     */
    public function getType(): string 
    {
        return 'Custom';
    }

    /**
     * Returns the ID of this Shipping Method, if it is managed by Craft Commerce.
     *
     * @return int|null The shipping method ID, or null if it is not managed by Craft Commerce
     */
    public function getId(): ?int
    {
        return null;
    }

    /**
     * Returns the name of this Shipping Method as displayed to the customer and in the control panel.
     */
    public function getName(): string
    {
        // @todo - pull from settings
        return 'Click & Collect';
    }

    /**
     * Returns the unique handle of this Shipping Method.
     */
    public function getHandle(): string
    {
        // @todo - pull from settings
        return 'craft-commerce-click-and-collect';
    }

    /**
     * Returns the control panel URL to manage this method and its rules.
     * An empty string will result in no link.
     */
    public function getCpEditUrl(): string
    {
        // @todo - edit page
        return '';
    }

    /**
     * Returns an array of rules that meet the `ShippingRules` interface.
     *
     * @return ShippingRuleInterface[] The array of ShippingRules
     */
    public function getShippingRules(): array
    {
        // @todo - ????
        return [];
    }

    /**
     * Returns whether this shipping method is enabled for listing and selection by customers.
     */
    public function getIsEnabled(): bool
    {
        // @todo - setting
        return true;
    }

    public function getPriceForOrder(Order $order): float
    {
        // @todo
        return 9999;
    }

    /**
     * The first matching shipping rule for this shipping method
     */
    public function getMatchingShippingRule(Order $order): ?ShippingRuleInterface
    {
        return '';
    }

    /**
     * Is this shipping method available to the order?
     */
    public function matchOrder(Order $order): bool
    {
        // @todo
        return true;
    }
}