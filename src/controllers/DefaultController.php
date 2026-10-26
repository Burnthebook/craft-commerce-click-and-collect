<?php

/**
 * Click and Collect plugin for Craft CMS 5.x
 *
 * Add drop in Click and Collect functionality to Craft Commerce.
 *
 * @link        https://burnthebook.co.uk
 * @author      Michael Burton <mikey@burnthebook.co.uk>
 * @since       0.0.1
 * @copyright   Copyright (c) 2022 burnthebook
 */

namespace burnthebook\craftcommerceclickandcollect\controllers;

use craft\web\Controller;
use craft\helpers\UrlHelper;
use burnthebook\craftcommerceclickandcollect\ClickAndCollect;
use burnthebook\craftcommerceclickandcollect\models\CollectionPoint;
use yii\web\Response;

/**
 * Default Controller - Used for Control Panel Requests
 *
 * @author Michael Burton <mikey@burnthebook.co.uk>
 * @since 0.0.1
 */
class DefaultController extends Controller
{
    /**
     * Index Template
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        if (\Craft::$app->getUser()->checkPermission('craft-commerce-click-and-collect:manageCollectionPoints')) {
            return $this->redirect(UrlHelper::cpUrl('craft-commerce-click-and-collect/collection-points'));
        }

        $this->requirePermission('craft-commerce-click-and-collect:manageCollectionTimes');

        return $this->redirect(UrlHelper::cpUrl('craft-commerce-click-and-collect/collection-times'));
    }
}
