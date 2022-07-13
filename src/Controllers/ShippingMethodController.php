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

namespace burnthebook\ClickCollect\Controllers;

use Craft;
use craft\web\Response;
use craft\web\Controller;
use burnthebook\ClickCollect\ClickCollect;
use burnthebook\ClickCollect\Models\ShippingMethod;

/**
 * Shipping Method Controller, used for processing the form requests to save the settings
 *
 * @author    burnthebook
 * @package   ClickCollect
 * @since     0.0.1
 */
class ShippingMethodController extends Controller 
{
    public function actionSaveSettings() 
    {
        $this->requireCpRequest();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        if (ShippingMethod::exists()) {
            /** 
             * Fetch method on model returns an ActiveRecord instance
             * 
             * @see burnthebook\ClickCollect\Models\ShippingMethod::fetch
             * @return burnthebook\ClickCollect\Records\ShippingMethod
             */
            $shippingMethod = ShippingMethod::fetch();
        } else {
            /** 
             * Create method on model returns an ActiveRecord instance
             * 
             * @see burnthebook\ClickCollect\Models\ShippingMethod::create
             * @return burnthebook\ClickCollect\Records\ShippingMethod
             */
            $shippingMethod = ShippingMethod::create();
        }

        $shippingMethod->active = $request->getParam('shippingMethodEnabled');
        $shippingMethod->name = $request->getParam('shippingMethodName');
        $shippingMethod->price = $request->getParam('shippingMethodPrice');
        $shippingMethod->save();
        
        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));
        return $this->redirectToPostedUrl();
    }
}