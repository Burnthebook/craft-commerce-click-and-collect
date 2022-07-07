<?php
namespace burnthebook\ClickCollect\AssetBundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ClickCollectAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@burnthebook/craft-commerce-click-and-collect/resources/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/craft-commerce-click-and-collect.css',
        ];

        $this->js = [
            'js/craft-commerce-click-and-collect.js',
        ];

        parent::init();
    }
}
