<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class PartnersMigration_102
 */
class PartnersMigration_102 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('partners', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 11,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'company',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'name'
                        ]
                    ),
                    new Column(
                        'approved',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'size' => 4,
                            'after' => 'company'
                        ]
                    ),
                    new Column(
                        'level',
                        [
                            'type' => Column::TYPE_CHAR,
                            'default' => "middle",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'approved'
                        ]
                    ),
                    new Column(
                        'description',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'level'
                        ]
                    ),
                    new Column(
                        'city',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'description'
                        ]
                    ),
                    new Column(
                        'country_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'city'
                        ]
                    ),
                    new Column(
                        'address',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'country_id'
                        ]
                    ),
                    new Column(
                        'phone',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 244,
                            'after' => 'address'
                        ]
                    ),
                    new Column(
                        'email',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'phone'
                        ]
                    ),
                    new Column(
                        'requisites',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'email'
                        ]
                    ),
                    new Column(
                        'created',
                        [
                            'type' => Column::TYPE_TIMESTAMP,
                            'default' => "CURRENT_TIMESTAMP",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'requisites'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('FK_partners_country', ['country_id'], null)
                ],
                'references' => [
                    new Reference(
                        'FK_partners_country',
                        [
                            'referencedTable' => 'countries',
                            'referencedSchema' => 'hr',
                            'columns' => ['country_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'RESTRICT'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '2',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_unicode_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
