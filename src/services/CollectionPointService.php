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

use Craft;
use DateTime;
use DatePeriod;
use DateInterval;
use DateTimeZone;
use craft\db\Query;
use craft\helpers\Db;
use yii\db\Expression;
use craft\base\Component;
use craft\elements\Address;
use burnthebook\craftcommerceclickandcollect\models\CollectionPoint;
use burnthebook\craftcommerceclickandcollect\records\CollectionPointRecord;

class CollectionPointService extends Component
{
    /**
     * Retrieves all collection points from the database.
     *
     * @return array An array of CollectionPoint objects.
     */
    public function getAllCollectionPoints()
    {
        \Craft::info('getAllCollectionPoints() method called.', __METHOD__);

        // Fetch all collection points from the database
        $records = CollectionPointRecord::find()->all();

        $collectionPoints = [];
        foreach ($records as $record) {
            $collectionPoints[] = new CollectionPoint($record->toArray());
        }

        return $collectionPoints;
    }

    /**
     * Retrieves a collection point by its ID.
     *
     * @param int $id The ID of the collection point to retrieve.
     * @return CollectionPoint|null The collection point with the given ID, or null if no collection point was found.
     */
    public function getCollectionPointById(int $id)
    {
        $record = CollectionPointRecord::findOne($id);

        if ($record) {
            return new CollectionPoint($record->toArray());
        }

        return null;
    }

    /**
     * Deletes a collection point record from the database by its ID.
     *
     * @param int $id The ID of the collection point record to be deleted.
     * @return bool Whether the deletion was successful or not.
     */
    public function deleteCollectionPointById(int $id): bool
    {
        $record = CollectionPointRecord::findOne($id);

        if (!$record) {
            return false;
        }

        $transaction = Craft::$app->db->beginTransaction();
        try {
            // Delete the associated address
            if ($record->craft_address_id) {
                $address = Address::find()->id($record->craft_address_id)->one();
                if ($address) {
                    Craft::$app->getElements()->deleteElement($address);
                }
            }

            // Delete the collection point
            $record->delete();

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Craft::error('Failed to delete collection point: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Saves a collection point model to the database.
     *
     * @param CollectionPoint $model The collection point model to be saved.
     * @return bool Whether the save was successful or not.
     * @throws \Exception If the collection point with the given ID does not exist.
     */
    public function saveCollectionPoint(CollectionPoint $model): bool
    {
        $isNew = !$model->id;

        if ($model->validate()) {
            if ($isNew) {
                $record = new CollectionPointRecord();
            } else {
                $record = CollectionPointRecord::findOne($model->id);
                if (!$record) {
                    throw new \Exception('No collection point exists with the ID "' . $model->id . '".');
                }
            }

            $record->name = $model->name;
            $record->address = $model->address;
            $record->postcode = $model->postcode;
            $record->latitude = $model->latitude;
            $record->longitude = $model->longitude;
            $record->gracePeriodHours = $model->gracePeriodHours;
            $record->craft_address_id = $model->craft_address_id;

            return $record->save(false);
        } else {
            // If validation fails, return false
            return false;
        }
    }

    /**
     * Finds nearby collection points based on a given latitude and longitude.
     *
     * @param float $latitude The latitude of the location to search around.
     * @param float $longitude The longitude of the location to search around.
     * @param int $radius The radius in miles to search within. Default is 50 miles.
     * @param int $limit The maximum number of results to return. Default is 10.
     * @return array An array of CollectionPoint objects sorted by distance from the given location.
     */
    public function findNearby(float $latitude, float $longitude, int $radius = 50, int $limit = 10)
    {
        // Earth radius in miles
        $earthRadius = 3959;

        // Calculate bounding box (latitude and longitude deltas)
        $latDelta = $radius / $earthRadius * (180 / pi());
        $lonDelta = $radius / $earthRadius * (180 / pi()) / cos($latitude * pi() / 180);

        $minLat = $latitude - $latDelta;
        $maxLat = $latitude + $latDelta;
        $minLon = $longitude - $lonDelta;
        $maxLon = $longitude + $lonDelta;

        // Detect the database driver type
        $dbDriver = Craft::$app->getDb()->getDriverName();

        // Haversine formula to calculate the distance
        $distanceFormula = "(:earthRadius * acos(
            cos(radians(:latitude)) * cos(radians([[latitude]])) * cos(radians([[longitude]]) - radians(:longitude)) +
            sin(radians(:latitude)) * sin(radians([[latitude]]))
        ))";

        if ($dbDriver === 'pgsql') {
            // PostgreSQL requires using a subquery for the distance calculation
            $subquery = (new Query())
                ->select([
                    '{{%collection_points}}.*',
                    new Expression("$distanceFormula AS distance")
                ])
                ->from('{{%collection_points}}')
                ->where(['between', 'latitude', $minLat, $maxLat])
                ->andWhere(['between', 'longitude', $minLon, $maxLon])
                ->addParams([
                    ':latitude' => $latitude,
                    ':longitude' => $longitude,
                    ':earthRadius' => $earthRadius,
                ]);

            $query = (new Query())
                ->from(['points' => $subquery])
                ->where(['<=', 'distance', $radius])
                ->orderBy('distance ASC')
                ->limit($limit);
        } else {
            // MySQL query structure
            $query = CollectionPointRecord::find()
                ->select([
                    '{{%collection_points}}.*',
                    new Expression("$distanceFormula AS distance")
                ])
                ->addParams([
                    ':latitude' => $latitude,
                    ':longitude' => $longitude,
                    ':earthRadius' => $earthRadius,
                ])
                ->where(['between', 'latitude', $minLat, $maxLat])
                ->andWhere(['between', 'longitude', $minLon, $maxLon])
                ->having(['<=', 'distance', $radius])
                ->orderBy('distance ASC')
                ->limit($limit);
        }

        $records = $query->all();

        $sql = $query->createCommand()->getRawSql();
        \Craft::info("SQL Query: $sql", __METHOD__);

        $collectionPoints = [];
        foreach ($records as $record) {
            // Check if $record is an array or an object
            $attributes = is_array($record) ? $record : $record->getAttributes();

            // Get the calculated distance from the record (in case of array)
            $attributes['distance'] = $attributes['distance'] ?? $record->getAttribute('distance');

            $collectionPoints[] = new CollectionPoint($attributes);
        }

        return $collectionPoints;
    }

    /**
     * Gets the next available collection time for a given collection point.
     *
     * @param int $collectionPointId The ID of the collection point.
     * @return DateTime|null The next available collection time, or null if none is available.
    */
    public function getNextAvailableCollectionTime($collectionPointId)
    {
        // This function is complex. Enable logging here to debug it.
        $enableLogging = false;
    
        $collectionPoint = $this->getCollectionPointById($collectionPointId);
        $collectionTimes = $collectionPoint->getCollectionTimes();
    
        $currentDateTime = new DateTime();
        $remainingGraceHours = $collectionPoint->gracePeriodHours ?? 8;
    
        // Ensure proper timezone
        $currentDateTime->setTimezone(new \DateTimeZone('Europe/London'));
    
        // Prepare schedule as time-only components
        $schedule = [];
        foreach ($collectionTimes as $time) {
            $schedule[$time->day] = [
                'open' => (new DateTime())->setTime((int)$time->openingTime->format("H"), (int)$time->openingTime->format("i")),
                'close' => (new DateTime())->setTime((int)$time->closingTime->format("H"), (int)$time->closingTime->format("i"))
            ];
        }
    
        $iterationLimit = 30; // Prevent infinite loops
        $iterations = 0;
    
        $enableLogging ? Craft::info("Starting calculation for Collection Point ID: {$collectionPointId}", __METHOD__) : null;
    
        while ($remainingGraceHours > 0) {
            $iterations++;
    
            // Log iteration details
            $enableLogging ? Craft::info("Iteration: {$iterations}, Current DateTime: {$currentDateTime->format('Y-m-d H:i:s')}, Remaining Grace Hours: {$remainingGraceHours}", __METHOD__) : null;
    
            if ($iterations > $iterationLimit) {
                $enableLogging ? Craft::error("Iteration limit exceeded for Collection Point ID: {$collectionPointId}. Current DateTime: {$currentDateTime->format('Y-m-d H:i:s')}, Remaining Grace Hours: {$remainingGraceHours}", __METHOD__) : null;
                return false;
            }
    
            $dayOfWeek = strtolower($currentDateTime->format("D"));
    
            if (isset($schedule[$dayOfWeek])) {
                $openTime = clone $schedule[$dayOfWeek]['open'];
                $closeTime = clone $schedule[$dayOfWeek]['close'];
    
                $openTime->setDate($currentDateTime->format("Y"), $currentDateTime->format("m"), $currentDateTime->format("d"));
                $closeTime->setDate($currentDateTime->format("Y"), $currentDateTime->format("m"), $currentDateTime->format("d"));
    
                $enableLogging ? Craft::info("Checking day: {$dayOfWeek}, Open Time: {$openTime->format('Y-m-d H:i:s')}, Close Time: {$closeTime->format('Y-m-d H:i:s')}", __METHOD__) : null;
    
                // If current time is before opening, move to opening time
                if ($currentDateTime < $openTime) {
                    $enableLogging ? Craft::info("Current time is before opening. Moving to Open Time: {$openTime->format('Y-m-d H:i:s')}", __METHOD__) : null;
                    $currentDateTime = $openTime;
                }
    
                // Calculate available hours for today
                if ($currentDateTime < $closeTime) {
                    $availableHours = ($closeTime->getTimestamp() - $currentDateTime->getTimestamp()) / 3600;
                    $enableLogging ? Craft::info("Available Hours Today: {$availableHours}", __METHOD__) : null;
    
                    if ($remainingGraceHours <= $availableHours) {
                        // Split remaining hours into hours and minutes
                        $wholeHours = floor($remainingGraceHours);
                        $fractionalHours = $remainingGraceHours - $wholeHours;
                        $minutes = round($fractionalHours * 60);
    
                        // Apply hours and minutes separately
                        if ($wholeHours > 0) {
                            $currentDateTime->modify("+$wholeHours hours");
                        }
                        if ($minutes > 0) {
                            $currentDateTime->modify("+$minutes minutes");
                        }
    
                        $enableLogging ? Craft::info("Grace period fits today. Next Available Time: {$currentDateTime->format('Y-m-d H:i:s')}", __METHOD__) : null;
    
                        // Validate the result
                        if ($currentDateTime->format('Y') > 3000) {
                            $enableLogging ? Craft::error("Invalid future date detected: {$currentDateTime->format('Y-m-d H:i:s')}", __METHOD__) : null;
                            throw new \RuntimeException("Invalid future date returned.");
                        }
    
                        return $currentDateTime;
                    } else {
                        // Use up all available hours today
                        $remainingGraceHours -= $availableHours;
                        $enableLogging ? Craft::info("Used up all hours today. Remaining Grace Hours: {$remainingGraceHours}", __METHOD__) : null;
                    }
                } else {
                    $enableLogging ? Craft::info("Current time is past today's close time. Moving to the next day.", __METHOD__) : null;
                }
            } else {
                $enableLogging ? Craft::info("No opening hours for {$dayOfWeek}. Skipping to next day.", __METHOD__) : null;
            }
    
            // Move to the next day
            $currentDateTime->modify('+1 day');
            if (isset($schedule[strtolower($currentDateTime->format("D"))])) {
                $nextOpenTime = $schedule[strtolower($currentDateTime->format("D"))]['open'];
                $currentDateTime->setTime((int)$nextOpenTime->format("H"), (int)$nextOpenTime->format("i"));
                $enableLogging ? Craft::info("Moving to next day's open time: {$currentDateTime->format('Y-m-d H:i:s')}", __METHOD__) : null;
            } else {
                $enableLogging ? Craft::info("No opening hours for {$currentDateTime->format('l')}. Skipping to next day.", __METHOD__) : null;
            }
        }
    
        $enableLogging ? Craft::info("Final Next Available Time: {$currentDateTime->format('Y-m-d H:i:s')}", __METHOD__) : null;
        return $currentDateTime;
    }

    // public function getNextAvailableCollectionTime($collectionPointId)
    // {
    //     // Get collection point.
    //     $collectionPoint = $this->getCollectionPointById($collectionPointId);
    //     $collectionTimes = $collectionPoint->getCollectionTimes();
    //     $timezone = Craft::$app->getTimeZone();

    //     // Get current time.
    //     $dateTime = new DateTime('now', new DateTimeZone($timezone));

    //     // Get grace period for collection point. (Default 18h if not set)
    //     $gracePeriod = $collectionPoint->gracePeriodHours ?? 18;

    //     // Add grace period (hours) to current time.
    //     $desiredTime = $dateTime->add(new DateInterval("PT{$gracePeriod}H"))->format("d-m-Y H:i");
    //     $desiredTimeDt = new DateTime($desiredTime, new DateTimeZone($timezone));

    //     // Get the days
    //     $days = $this->getOpeningHours($collectionTimes);

    //     // Loop through collection times again to do datetime comparsion with opening hours array
    //     foreach ($collectionTimes as $collectionTime) {
    //         // If the collection time day matches the desired times day
    //         if ($collectionTime->day == strtolower($desiredTimeDt->format('D'))) {
    //             $closestTime = $this->getClosestTime($desiredTimeDt, $days[$collectionTime->day]);
    //             return $closestTime;
    //         }
    //     }

    //     return null;
    // }

    /**
     * Returns an array of opening hours for each day of the week, based on the given collection times.
     *
     * @param array $collectionTimes An array of collection times.
     * @return array An array of opening hours for each day of the week, sorted according to the desired order of days.
    */
    public function getOpeningHours(array $collectionTimes)
    {
        $days = [];

        // Build a days array with all possible opening hours
        foreach ($collectionTimes as $collectionTime) {
            // init/reset array
            $times = [];

            // Create a range of times for this day
            $days[$collectionTime->day] = [];

            // Create a date period
            $period = new DatePeriod($collectionTime->openingTime, new DateInterval('PT1H'), $collectionTime->closingTime);

            // loop through period
            foreach ($period as $time) {
                $times[] = $time->format('H:i');
            }
            // add closing time to end of array
            $times[] = $collectionTime->closingTime->format('H:i');

            // Add range of opening hours to days array
            $days[$collectionTime->day] = $times;
        }

        // Define the desired order of days
        $orderedDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $sortedDays = [];

        // Sort $days according to $orderedDays
        foreach ($orderedDays as $day) {
            if (isset($days[$day])) {
                $sortedDays[$day] = $days[$day];
            }
        }

        return $sortedDays;
    }

    /**
     * Returns an array of formatted opening hours for each day in the given collection times.
     *
     * @param array $collectionTimes An array of collection times for each day.
     * @return array An array of formatted opening hours for each day.
    */
    public function getFormattedOpeningHours(array $collectionTimes)
    {
        // Map to convert day abbreviations to full names
        $dayMap = [
            'mon' => 'Mon',
            'tue' => 'Tue',
            'wed' => 'Wed',
            'thu' => 'Thu',
            'fri' => 'Fri',
            'sat' => 'Sat',
            'sun' => 'Sun'
        ];

        $formattedDays = [];

        foreach ($collectionTimes as $collectionTime) {
            // Format the opening and closing times
            $openingTime = $collectionTime->openingTime->format('H:i');
            $closingTime = $collectionTime->closingTime->format('H:i');

            // Map the day abbreviation to the full name
            $day = $dayMap[$collectionTime->day] ?? $collectionTime->day;

            // Create the formatted string for each day
            $formattedDays[] = "{$day} {$openingTime} - {$closingTime}";
        }

        return $formattedDays;
    }

    /**
     * Returns the closest DateTime object from the given array of times to the given DateTime object.
     *
     * @param DateTime $dateTime The original DateTime object to compare against.
     * @param array $times An array of times to compare against the original DateTime object.
     * @return DateTime|null The closest DateTime object from the given array of times, or null if the array is empty.
    */
    protected function getClosestTime(DateTime $dateTime, array $times)
    {
        $closestDateTime = null;
        $smallestDifference = null;

        foreach ($times as $time) {
            // Clone the original DateTime object to preserve the date
            $timeObject = clone $dateTime;

            // Set the time part using the time from the array
            $timeObject->modify($time);

            // Calculate the difference between the original DateTime and the new one
            $difference = $dateTime->diff($timeObject);

            // Convert the difference to seconds
            $secondsDifference = abs($difference->h * 3600 + $difference->i * 60 + $difference->s);

            // Track the smallest difference and closest DateTime
            if ($smallestDifference === null || $secondsDifference < $smallestDifference) {
                $smallestDifference = $secondsDifference;
                $closestDateTime = $timeObject; // Store the closest DateTime object
            }
        }

        return $closestDateTime;
    }
}
