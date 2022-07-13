<?php

namespace burnthebook\ClickCollect\Records;

use craft\db\ActiveRecord;
use burnthebook\ClickCollect\migrations\Install;

class CollectionPoint extends ActiveRecord
{
    public static function tableName()
    {
        // @todo move const out of install and into plugin class.
        return Install::TABLE_NAMES['collectionpoints'];
    }
}