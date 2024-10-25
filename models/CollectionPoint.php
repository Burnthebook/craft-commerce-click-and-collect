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

namespace burnthebook\craftcommerceclickandcollect\models;

use DateTime;
use DateInterval;
use Craft;
use craft\base\Model;
use craft\elements\Address;
use burnthebook\craftcommerceclickandcollect\ClickAndCollect;

class CollectionPoint extends Model
{
    public ?int $id = null;
    
    public ?int $craft_address_id = null;

    public ?int $gracePeriodHours = null;

    public ?string $uid = null;

    public ?string $name = null;

    public ?string $address = null;

    public ?string $postcode = null;

    public ?string $latitude = null;

    public ?string $longitude = null;

    public ?\DateTime $dateCreated = null;

    public ?\DateTime $dateUpdated = null;

    private $_collectionTimes = null;

    public ?float $distance = null;
    
    private ?Address $_address = null;

    /**
     * Returns an array of rules for validating the attributes of this model.
     *
     * The rules specify which attributes are required, which attributes should be of a certain type,
     * and any other validation rules that should be applied to the model's attributes.
     *
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            [['name', 'address', 'postcode', 'craft_address_id', 'gracePeriodHours'], 'required'],
            [['craft_address_id', 'gracePeriodHours'], 'integer'],
            [['latitude', 'longitude', 'distance'], 'number'],
            [['name', 'address', 'postcode', 'uid'], 'string'],
            [['dateCreated', 'dateUpdated'], 'safe'],
        ];
    }

    /**
     * Retrieves the collection times for the current collection point.
     *
     * @return array|null The collection times for the current collection point, or null if none are found.
     */
     public function getCollectionTimes()
     {
         if ($this->_collectionTimes === null) {
             $this->_collectionTimes = ClickAndCollect::$plugin->collectionTimes->getCollectionTimesByCollectionPointId($this->id, [
                 'orderBy' => 'openingTime ASC',
             ]);
         }
 
         return $this->_collectionTimes;
     }
 

    /**
     * Returns the first collection time from the collection times array.
     *
     * @return string|null The first collection time or null if the collection times array is empty.
     */
    public function getFirstCollectionTime()
    {
        $collectionPointId = $this->id;
        
        return ClickAndCollect::$plugin->collectionPoints->getNextAvailableCollectionTime($collectionPointId);
    }

    /**
     * Gets the related Address Element.
     *
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        if ($this->_address === null && $this->craft_address_id) {
            $this->_address = Address::find()->id($this->craft_address_id)->one();
        }
        return $this->_address;
    }

}
