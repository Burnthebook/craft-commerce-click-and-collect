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
use yii\web\Response;
use craft\web\Controller;
use craft\elements\Address;
use craft\helpers\UrlHelper;
use burnthebook\craftcommerceclickandcollect\ClickAndCollect;
use burnthebook\craftcommerceclickandcollect\models\CollectionPoint;

/**
 * Collection Points Controller - Used for Control Panel Requests
 *
 * @author Michael Burton <mikey@burnthebook.co.uk>
 * @since 0.0.1
 */
class CollectionPointsController extends Controller
{
    /**
     * Index Template
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        $this->requireAdmin();

        // Debugging
        if (ClickAndCollect::$plugin->collectionPoints === null) {
            throw new \Exception('CollectionPointService is not loaded.');
        }

        // Proceed to call the method
        $collectionPoints = ClickAndCollect::$plugin->collectionPoints->getAllCollectionPoints();

        return $this->renderTemplate('craft-commerce-click-and-collect/cp/collection-points/index', [
            'collectionPoints' => $collectionPoints,
        ]);
    }

    /**
     * Edit Template
     *
     * @param ?int $collectionPointId
     * @return Response
     */
    public function actionEdit(int $collectionPointId = null): Response
    {
        $this->requireAdmin();

        if ($collectionPointId) {
            $collectionPoint = ClickAndCollect::$plugin->collectionPoints->getCollectionPointById($collectionPointId);
        } else {
            $collectionPoint = new CollectionPoint();
        }

        return $this->renderTemplate('craft-commerce-click-and-collect/cp/collection-points/edit', [
            'collectionPoint' => $collectionPoint,
        ]);
    }

    /**
     * Save Request
     *
     * @throws Exception
     * @return Response
     */
    public function actionSave(): Response
    {
        $this->requirePostRequest();
        $this->requireAdmin();

        $request = \Craft::$app->getRequest();
        $collectionPointId = $request->getBodyParam('collectionPointId');

        if ($collectionPointId) {
            $collectionPoint = ClickAndCollect::$plugin->collectionPoints->getCollectionPointById($collectionPointId);
            if (!$collectionPoint) {
                throw new \Exception('Collection Point not found.');
            }
        } else {
            $collectionPoint = new CollectionPoint();
        }

        $collectionPoint->name = $request->getBodyParam('name');
        $collectionPoint->gracePeriodHours = $request->getBodyParam('gracePeriodHours', 0);
        $collectionPoint->postcode = $request->getBodyParam('postcode');
        $collectionPoint->latitude = $request->getBodyParam('latitude');
        $collectionPoint->longitude = $request->getBodyParam('longitude');

        // Handle address data
        $addressData = $request->getBodyParam('address');

        // Fetch or create the Address Element
        if ($collectionPoint->craft_address_id) {
            $address = Address::find()->id($collectionPoint->craft_address_id)->one();
            if (!$address) {
                $address = new Address();
            }
        } else {
            $address = new Address();
        }

        // Set address attributes
        $address->title = $collectionPoint->name;
        $address->setAttributes($addressData, false);

        // Save the address
        if (!Craft::$app->getElements()->saveElement($address)) {
            Craft::$app->getSession()->setError('Couldn&quot;t save address.');

            // Send back to template with errors
            return $this->renderTemplate('craft-commerce-click-and-collect/cp/collection-points/edit', [
                'collectionPoint' => $collectionPoint,
                'address' => $address,
            ]);
        }

        $addressParts = [
            $address->addressLine1,
            $address->addressLine2,
            $address->locality,
            $address->administrativeArea,
            $address->postalCode
        ];

        $nonEmptyParts = array_filter($addressParts, function ($part) {
            return !empty($part);
        });

        $collectionPoint->address = implode(',', $nonEmptyParts);

        // Set the craft_address_id on the collection point
        $collectionPoint->craft_address_id = $address->id;

        if (ClickAndCollect::$plugin->collectionPoints->saveCollectionPoint($collectionPoint)) {
            \Craft::$app->getSession()->setNotice('Collection Point saved.');
            return $this->redirectToPostedUrl();
        } else {
            \Craft::$app->getSession()->setError('Couldn&quot;t save Collection Point.');
            // Send the model back to the template for error display
            return $this->renderTemplate('craft-commerce-click-and-collect/cp/collection-points/edit', [
                'collectionPoint' => $collectionPoint,
            ]);
        }
    }

    /**
     * Delete Request
     *
     * @param int $collectionPointId
     * @throws Exception
     * @return Response
     */
    public function actionDelete(int $collectionPointId): Response
    {
        $this->requireAdmin();

        if (ClickAndCollect::$plugin->collectionPoints->deleteCollectionPointById($collectionPointId)) {
            \Craft::$app->getSession()->setNotice('Collection Point deleted.');
        } else {
            \Craft::$app->getSession()->setError('Couldn&quot;t delete Collection Point.');
        }

        return $this->redirect(UrlHelper::cpUrl('craft-commerce-click-and-collect/collection-points'));
    }
}
