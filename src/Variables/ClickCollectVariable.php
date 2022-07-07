<?php
namespace burnthebook\ClickCollect\Variables;

use burnthebook\ClickCollect\ClickCollect;

class ClickCollectVariable
{
    public function getPluginName()
    {
        return ClickCollect::$plugin->getPluginName();
    }

    public function getPluginHandle()
    {
        return ClickCollect::$plugin->getPluginHandle();
    }
}