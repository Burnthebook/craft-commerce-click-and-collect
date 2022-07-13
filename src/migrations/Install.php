<?php
namespace burnthebook\ClickCollect\migrations;

use Craft;
use craft\db\Query;
use craft\db\Table;
use craft\db\Migration;
use craft\helpers\Json;
use craft\elements\Entry;
use craft\services\Plugins;
use craft\helpers\ArrayHelper;
use craft\helpers\StringHelper;
use craft\helpers\MigrationHelper;
use burnthebook\ClickCollect\ClickCollect;

class Install extends Migration
{

    public const TABLE_NAMES = [
        'shippingmethods' => '{{%clickcollect_shippingmethods}}',
        'collectionpoints' => '{{%clickcollect_collectionpoints}}',
        'collectiontimes' => '{{%clickcollect_collectiontimes}}',
    ];
    

    // Public Methods
    // =========================================================================

    public function safeUp()
    {
        $this->createTables();
    }


    public function safeDown()
    {
        $this->dropTables();
    }

    // Protected Methods
    // =========================================================================

    /**
     * Check if the database tables have been created, if they have not, create them.
     * 
     * @return bool
     */
    protected function createTables() : bool
    {
        $tablesCreated = [];

        if (!Craft::$app->db->schema->getTableSchema(self::TABLE_NAMES['shippingmethods'])) {
            $tablesCreated['shippingmethods'] = $this->createShippingMethodTable();
        }

        if (!Craft::$app->db->schema->getTableSchema(self::TABLE_NAMES['collectionpoints'])) {
            $tablesCreated['collectionpoints'] = $this->createCollectionPointsTable();
        }

        if (!Craft::$app->db->schema->getTableSchema(self::TABLE_NAMES['collectiontimes'])) {
            $tablesCreated['collectiontimes'] = $this->createCollectionTimesTable();
        }

        return $tablesCreated['shippingmethods'] && $tablesCreated['collectionpoints'] && $tablesCreated['collectiontimes'];
    }

    /**
     * Drop tables if they exist
     * 
     * @return void
     */
    protected function dropTables() : void
    {
        if ($this->db->tableExists(self::TABLE_NAMES['shippingmethods'])) {
            $this->dropTable(self::TABLE_NAMES['shippingmethods']);
        }
        if ($this->db->tableExists(self::TABLE_NAMES['collectionpoints'])) {
            $this->dropTable(self::TABLE_NAMES['collectionpoints']);
        }
        if ($this->db->tableExists(self::TABLE_NAMES['collectiontimes'])) {
            $this->dropTable(self::TABLE_NAMES['collectiontimes']);
        }
    }

    protected function createShippingMethodTable()
    {
        $this->createTable(
            self::TABLE_NAMES['shippingmethods'],
            [
                'id' => $this->primaryKey(),
                'uid' => $this->uid(),                
                'name' => $this->string(),
                'active' => $this->tinyInteger(),
                'price' => $this->integer(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull()
            ]
        );

        $this->createIndex('shippingMethodDateCreated_idx', self::TABLE_NAMES['shippingmethods'], 'dateCreated');

        return true;
    }

    protected function createCollectionPointsTable()
    {
        $this->createTable(
            self::TABLE_NAMES['collectionpoints'],
            [
                'id' => $this->primaryKey(),
                'uid' => $this->uid(),
                'name' => $this->string(),
                'countryCode' => $this->string()->notNull(),
                'administrativeArea' => $this->string(),
                'locality' => $this->string(),
                'dependentLocality' => $this->string(),
                'postalCode' => $this->string(),
                'sortingCode' => $this->string(),
                'addressLine1' => $this->string(),
                'addressLine2' => $this->string(),
                'latitude' => $this->string(),
                'longitude' => $this->string(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull()
            ]
        );

        $this->createIndex('collectionPointsDateCreated_idx', self::TABLE_NAMES['collectionpoints'], 'dateCreated');

        return true;
    }

    protected function createCollectionTimesTable()
    {
        $this->createTable(
            self::TABLE_NAMES['collectiontimes'],
            [
                'id' => $this->primaryKey(),
                'uid' => $this->uid(),
                'collectionPointId' => $this->integer(),
                'time' => $this->string()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull()
            ]
        );

        $this->createIndex('collectionTimesDateCreated_idx', self::TABLE_NAMES['collectiontimes'], 'dateCreated');
        $this->createIndex('collectionTimesCollectionPointId_idx', self::TABLE_NAMES['collectiontimes'], 'collectionPointId');

        return true;
    }
}
