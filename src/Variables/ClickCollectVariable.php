<?php
namespace burnthebook\ClickCollect\Variables;

use burnthebook\ClickCollect\ClickCollect;

class ClickCollectVariable
{
    public function __call($name, $arguments)
    {
        return ClickCollect::$plugin->$name($arguments);
    }
}