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

use yii\web\Response;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use burnthebook\craftcommerceclickandcollect\ClickAndCollect;
use burnthebook\craftcommerceclickandcollect\models\CollectionTime;

/**
 * Collection Times Controller - Used for Control Panel Requests
 *
 * @author Michael Burton <mikey@burnthebook.co.uk>
 * @since 0.0.1
 */
class CollectionTimesController extends Controller
{
    /**
     * Index Template
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        $this->requireAdmin();

        $collectionTimes = ClickAndCollect::$plugin->collectionTimes->getAllCollectionTimes();

        return $this->renderTemplate('craft-commerce-click-and-collect/cp/collection-times/index', [
            'collectionTimes' => $collectionTimes,
        ]);
    }

    /**
     * Edit Template
     *
     * @param ?int $collectionTimeId
     * @return Response
     */
    public function actionEdit(int $collectionTimeId = null): Response
    {
        $this->requireAdmin();

        if ($collectionTimeId) {
            $collectionTime = ClickAndCollect::$plugin->collectionTimes->getCollectionTimeById($collectionTimeId);
        } else {
            $collectionTime = new CollectionTime();
        }


        $collectionPoints = ClickAndCollect::$plugin->collectionPoints->getAllCollectionPoints();

        return $this->renderTemplate('craft-commerce-click-and-collect/cp/collection-times/edit', [
            'collectionTime' => $collectionTime,
            'collectionPoints' => $collectionPoints,
        ]);
    }

    /**
     * Save Request
     *
     * @return Response
     */
    public function actionSave(): Response
    {
        $this->requirePostRequest();
        $this->requireAdmin();

        $request = \Craft::$app->getRequest();
        $collectionTimeId = $request->getBodyParam('collectionTimeId');

        if ($collectionTimeId) {
            $collectionTime = ClickAndCollect::$plugin->collectionTimes->getCollectionTimeById($collectionTimeId);
        } else {
            $collectionTime = new CollectionTime();
        }

        $collectionTime->collectionPointId = $request->getBodyParam('collectionPointId');
        $collectionTime->day = $request->getBodyParam('day');
        
        $collectionTime->openingTime = new \DateTime($request->getBodyParam('openingTime'));
        $collectionTime->closingTime = new \DateTime($request->getBodyParam('closingTime'));

        if (ClickAndCollect::$plugin->collectionTimes->saveCollectionTime($collectionTime)) {
            \Craft::$app->getSession()->setNotice('Collection Time saved.');
            return $this->redirectToPostedUrl();
        } else {
            \Craft::$app->getSession()->setError('Couldn&quot;t save Collection Time.');
            $collectionPoints = ClickAndCollect::$plugin->collectionPoints->getAllCollectionPoints();
            return $this->renderTemplate('craft-commerce-click-and-collect/cp/collection-times/edit', [
                'collectionTime' => $collectionTime,
                'collectionPoints' => $collectionPoints,
            ]);
        }
    }

    /**
     * Delete Request
     *
     * @param int $collectionTimeId
     * @return Response
     */
    public function actionDelete(int $collectionTimeId): Response
    {
        $this->requireAdmin();

        if (ClickAndCollect::$plugin->collectionTimes->deleteCollectionTimeById($collectionTimeId)) {
            \Craft::$app->getSession()->setNotice('Collection Time deleted.');
        } else {
            \Craft::$app->getSession()->setError('Couldn&quot;t delete Collection Time.');
        }

        return $this->redirect(UrlHelper::cpUrl('craft-commerce-click-and-collect/collection-times'));
    }
}
