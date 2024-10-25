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

namespace burnthebook\craftcommerceclickandcollect\assetbundles\clickandcollect;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ClickAndCollectAsset extends AssetBundle
{
    /**
     * Initializes the asset bundle.
     * Sets the source path, dependencies, and JS/CSS files for the click and collect asset bundle.
     */
    public function init()
    {
        $this->sourcePath = "@burnthebook/craftcommerceclickandcollect/assetbundles/clickandcollect/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/click-and-collect.js',
        ];

        $this->css = [
            'css/click-and-collect.css',
        ];

        parent::init();
    }
}
