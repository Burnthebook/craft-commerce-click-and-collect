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

namespace burnthebook\craftcommerceclickandcollect\variables;

use burnthebook\craftcommerceclickandcollect\ClickAndCollect;

class ClickAndCollectVariable
{
    /**
     * Retrieves all collection points from the ClickAndCollect plugin.
     *
     * @return array An array of all collection points.
     */
    public function getCollectionPoints()
    {
        return ClickAndCollect::$plugin->collectionPoints->getAllCollectionPoints();
    }

    /**
     * Retrieves all collection times from the ClickAndCollect plugin.
     *
     * @return array An array of all collection times.
     */
    public function getCollectionTimes()
    {
        return ClickAndCollect::$plugin->collectionTimes->getAllCollectionTimes();
    }

    /**
     * Finds nearby collection points based on given latitude and longitude coordinates.
     *
     * @param float $latitude The latitude coordinate.
     * @param float $longitude The longitude coordinate.
     * @param int $radius The radius in kilometers to search within. Default is 50km.
     * @param int $limit The maximum number of collection points to return. Default is 10.
     * @return array An array of nearby collection points.
     */
    public function findNearby(float $latitude, float $longitude, int $radius = 50, int $limit = 10)
    {
        return ClickAndCollect::$plugin->collectionPoints->findNearby($latitude, $longitude, $radius, $limit);
    }
}
