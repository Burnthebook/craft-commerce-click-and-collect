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

namespace burnthebook\craftcommerceclickandcollect\services;

use craft\base\Component;
use burnthebook\craftcommerceclickandcollect\models\CollectionTime;
use burnthebook\craftcommerceclickandcollect\records\CollectionTimeRecord;

class CollectionTimeService extends Component
{
    /**
     * Retrieves all collection times from the database and returns them as an array of CollectionTime objects.
     *
     * @return array An array of CollectionTime objects.
     */
    public function getAllCollectionTimes()
    {
        \Craft::info('getAllCollectionTimes() method called.', __METHOD__);

        // Fetch all collection times from the database
        $records = CollectionTimeRecord::find()->all();

        $collectionPoints = [];
        foreach ($records as $record) {
            $collectionPoints[] = new CollectionTime($record->toArray());
        }

        return $collectionPoints;
    }

    /**
     * Retrieves a CollectionTime object by its ID.
     *
     * @param int $id The ID of the CollectionTime to retrieve.
     * @return CollectionTime|null The CollectionTime object if found, or null if not found.
     */
    public function getCollectionTimeById(int $id)
    {
        $record = CollectionTimeRecord::findOne($id);

        if ($record) {
            return new CollectionTime($record->toArray());
        }

        return null;
    }

    /**
     * Deletes a collection time record by its ID.
     *
     * @param int $id The ID of the collection time record to be deleted.
     * @return bool Whether the deletion was successful.
     */
    public function deleteCollectionTimeById(int $id)
    {
        $record = CollectionTimeRecord::findOne($id);

        if ($record) {
            return $record->delete();
        }

        return false;
    }

    /**
     * Saves a CollectionTime model.
     * @param CollectionTime $model The CollectionTime model to be saved.
     * @return bool Whether the model was successfully saved.
     * @throws \Exception If the model's ID does not exist.
     */
    public function saveCollectionTime(CollectionTime $model): bool
    {
        $isNew = !$model->id;

        if ($model->validate()) {
            if ($isNew) {
                $record = new CollectionTimeRecord();
            } else {
                $record = CollectionTimeRecord::findOne($model->id);
                if (!$record) {
                    throw new \Exception('No collection time exists with the ID "' . $model->id . '".');
                }
            }

            $record->collectionPointId = $model->collectionPointId;
            $record->day = $model->day;
            $record->openingTime = $model->openingTime;
            $record->closingTime = $model->closingTime;

            return $record->save(false);
        } else {
            // If validation fails, return false
            return false;
        }
    }

    /**
     * Retrieves a collection of CollectionTime objects by the given collection point ID.
     *
     * @param int $collectionPointId The ID of the collection point to retrieve collection times for.
     * @param array $options Optional parameters for the query, such as 'orderBy'.
     * @return CollectionTime[] An array of CollectionTime objects.
     */
    public function getCollectionTimesByCollectionPointId(int $collectionPointId, array $options = [])
    {
        $query = CollectionTimeRecord::find()
            ->where(['collectionPointId' => $collectionPointId]);

        if (isset($options['orderBy'])) {
            $query->orderBy($options['orderBy']);
        }

        $records = $query->all();

        $collectionTimes = [];
        foreach ($records as $record) {
            $attributes = $record->toArray();

            // Convert 'openingTime' to DateTime if necessary
            if (isset($attributes['openingTime'])) {
                $attributes['openingTime'] = new \DateTime($attributes['openingTime']);
            }

            // Convert 'openingTime' to DateTime if necessary
            if (isset($attributes['closingTime'])) {
                $attributes['closingTime'] = new \DateTime($attributes['closingTime']);
            }

            $collectionTimes[] = new CollectionTime($attributes);
        }

        return $collectionTimes;
    }
}
