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

use craft\base\Model;
use burnthebook\craftcommerceclickandcollect\ClickAndCollect;

class CollectionTime extends Model
{
    public ?int $id = null;

    public ?string $uid = null;

    public ?string $collectionPointId = null;

    public ?string $day = null;

    public ?\DateTime $openingTime = null;

    public ?\DateTime $closingTime = null;

    public ?\DateTime $dateCreated = null;

    public ?\DateTime $dateUpdated = null;

    private $_collectionPoint = null;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['collectionPointId', 'day', 'openingTime', 'closingTime'], 'required'],
            [['collectionPointId'], 'integer'],
            [['uid'], 'string'],
            [['day', 'openingTime', 'closingTime'], 'safe'],
            [['dateCreated', 'dateUpdated'], 'safe'],
        ];
    }

    /**
     * Retrieves the collection point associated with this instance, if it has not already been retrieved.
     *
     * @return CollectionPoint|null The collection point associated with this instance, or null if it has not been set.
     */
    public function getCollectionPoint()
    {
        if ($this->_collectionPoint === null && $this->collectionPointId !== null) {
            $this->_collectionPoint = ClickAndCollect::$plugin->collectionPoints->getCollectionPointById($this->collectionPointId);
        }

        return $this->_collectionPoint;
    }
}
