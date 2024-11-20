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

namespace burnthebook\craftcommerceclickandcollect\controllers;

use Craft;
use DateTime;
use DatePeriod;
use DateInterval;
use DateTimeZone;
use yii\web\Response;
use craft\web\Controller;
use craft\elements\Address;
use burnthebook\craftcommerceclickandcollect\ClickAndCollect;

/**
 * Collection Controller - Used for AJAX Requests from Frontend.
 *
 * @author Michael Burton <mikey@burnthebook.co.uk>
 * @since 0.0.1
 */
class CollectionController extends Controller
{
    protected array|int|bool $allowAnonymous = ['find-collection-points', 'get-next-available-collection-time'];

    public function actionGetNextAvailableCollectionTime(): Response
    {
        $collectionPointId =  \Craft::$app->request->getRequiredParam('collectionPointId');
        $nextAvailableCollectionTime = ClickAndCollect::$plugin->collectionPoints->getNextAvailableCollectionTime($collectionPointId);
        return $this->asJson(compact('nextAvailableCollectionTime'));
    }

    /**
     * Finds nearby collection points based on a given postcode.
     *
     * @return Response The JSON response with the collection points data.
     * @throws \Exception if the postcode is not provided or if no coordinates are found.
     */
    public function actionFindCollectionPoints(): Response
    {
        $this->enableCsrfValidation = false; // Disable CSRF if necessary

        Craft::info('Guest request received for actionFindCollectionPoints', __METHOD__); // Debugging

        // $this->requireAcceptsJson();
        $postcode = \Craft::$app->request->getRequiredParam('postcode');

        // Geocode the postcode
        $coordinates = $this->getGeolocationByPostcode($postcode);

        if (!$coordinates) {
            // No coordinates found.
            throw new \Exception('Please check the postcode you entered is correct and try again. #59');
        }

        if (!array_key_exists('latitude', $coordinates)) {
            // No latitude provided.
            throw new \Exception('Please check the postcode you entered is correct and try again. #64');
        }

        if (!array_key_exists('longitude', $coordinates)) {
            // No longitude provided.
            throw new \Exception('Please check the postcode you entered is correct and try again. #69');
        }

        $latitude = $coordinates['latitude'];
        $longitude = $coordinates['longitude'];

        // Find nearby collection points
        $collectionPoints = ClickAndCollect::$plugin->collectionPoints->findNearby($latitude, $longitude, 500);

        // Prepare data for JSON response
        $data = array_map(function ($point) {

            // Load Address from Craft
            $address = Address::find()->id($point->craft_address_id)->one();

            // Get opening hours
            $openingTimes = ClickAndCollect::$plugin->collectionPoints->getFormattedOpeningHours($point->getCollectionTimes());

            // Ensure the address is loaded
            if ($address) {
                // Convert the address element to an array
                $addressData = [
                    'addressLine1' => $address->addressLine1,
                    'addressLine2' => $address->addressLine2,
                    'locality' => $address->locality,
                    'administrativeArea' => $address->administrativeArea,
                    'postalCode' => $address->postalCode,
                    'countryCode' => $address->countryCode,
                ];
            } else {
                $addressData = null;
            }

            $nextAvailableCollectionTime = ClickAndCollect::$plugin->collectionPoints->getNextAvailableCollectionTime($point->id);

            return [
                'id' => $point->id,
                'name' => $point->name,
                'address' => $point->address,
                'postcode' => $point->postcode,
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
                'distance' => $point->distance,
                'craft_address_id' => $point->craft_address_id,
                'craft_address' => $addressData,
                'firstCollectionTime' => $nextAvailableCollectionTime ? $nextAvailableCollectionTime->format('c') : null,
                'openingHours' => $openingTimes
            ];
        }, $collectionPoints);

        return $this->asJson($data);
    }

    /**
     * Retrieves the address data for a given postcode and returns them as a JSON response.
     *
     * @return Response The JSON response containing the postcode, latitude, and longitude.
     * @throws Exception If no coordinates are found or if the latitude or longitude are not provided.
     */
    public function actionGetAddressData(): Response
    {
        // $this->requireAcceptsJson();

        $postcode = \Craft::$app->request->getRequiredParam('postcode');

        $coordinates = $this->getGeolocationByPostcode($postcode);


        if (!$coordinates) {
            // No coordinates found.
            throw new \Exception('Please check the postcode you entered is correct and try again. #140');
        }

        if (!array_key_exists('latitude', $coordinates)) {
            // No latitude provided.
            throw new \Exception('Please check the postcode you entered is correct and try again. #145');
        }

        if (!array_key_exists('longitude', $coordinates)) {
            // No longitude provided.
            throw new \Exception('Please check the postcode you entered is correct and try again. #150');
        }

        return $this->asJson([
            'postcode' => $coordinates['postcode'],
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude']
        ]);
    }

    /**
     * Retrieves geolocation data for a given postcode.
     *
     * @param string $postcode The postcode to retrieve geolocation data for.
     * @return array|null The geolocation data for the given postcode, or null if there was an error.
     */
    private function getGeolocationByPostcode($postcode): ?array
    {
        $client = Craft::createGuzzleClient();

        try {
            $response = $client->get("https://api.postcodes.io/postcodes/{$postcode}");

            if ($response->getStatusCode() === 200) {
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if ($data['status'] === 200) {
                    return $data['result'];
                } else {
                    Craft::error('API returned an error: ' . $data['error'], __METHOD__);
                }
            } else {
                Craft::error('Unexpected HTTP status code: ' . $response->getStatusCode(), __METHOD__);
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Craft::error('HTTP request failed: ' . $e->getMessage(), __METHOD__);
        } catch (\Exception $e) {
            Craft::error('An unexpected error occurred: ' . $e->getMessage(), __METHOD__);
        }

        return null;
    }

}
