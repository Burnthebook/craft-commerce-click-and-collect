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

namespace burnthebook\craftcommerceclickandcollect\records;

use craft\db\ActiveRecord;

class CollectionTimeRecord extends ActiveRecord
{
    /**
     * Returns the name of the database table associated with this model.
     *
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%collection_times}}';
    }
}
