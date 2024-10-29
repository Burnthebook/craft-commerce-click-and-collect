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

namespace burnthebook\craftcommerceclickandcollect\migrations;

use craft\db\Migration;
use craft\db\Table;

class Install extends Migration
{
    /**
     * Creates the necessary database tables and adds a foreign key.
     *
     * This method is used to create the collection_points and collection_times tables in the database, as well as adding a foreign key to link the two tables.
     *
     * @return void
     */
    public function safeUp()
    {
        // Create collection_points table
        $this->createTable('{{%collection_points}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'craft_address_id' => $this->integer()->notNull(),
            'gracePeriodHours' => $this->integer()->notNull()->defaultValue(0),
            'address' => $this->text()->notNull(),
            'postcode' => $this->string()->notNull(),
            'latitude' => $this->decimal(10, 8),
            'longitude' => $this->decimal(11, 8),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        // Create collection_times table
        $this->createTable('{{%collection_times}}', [
            'id' => $this->primaryKey(),
            'collectionPointId' => $this->integer()->notNull(),
            'day' => $this->text()->notNull(),
            'openingTime' => $this->dateTime()->notNull(),
            'closingTime' => $this->dateTime()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        // Add foreign key to addresses table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%collection_points}}', 'craft_address_id'),
            '{{%collection_points}}',
            'craft_address_id',
            Table::ADDRESSES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Add foreign key
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%collection_times}}', 'collectionPointId'),
            '{{%collection_times}}',
            'collectionPointId',
            '{{%collection_points}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Drops the tables for the Collection Times and Collection Points models, if they exist.
     *
     * @return void
     */
    public function safeDown()
    {
        // Drop foreign key
        $this->dropForeignKey(
            $this->db->getForeignKeyName('{{%collection_points}}', 'craft_address_id'),
            '{{%collection_points}}'
        );

        // Drop tables
        $this->dropTableIfExists('{{%collection_times}}');
        $this->dropTableIfExists('{{%collection_points}}');
    }
}
