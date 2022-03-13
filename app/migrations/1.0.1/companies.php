<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class CompaniesMigration_101
 */
class CompaniesMigration_101 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws \Phalcon\Db\Exception
     */
    public function morph()
    {
        $this->morphTable('companies', [
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
                        'description',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'name'
                        ]
                    ),
                    new Column(
                        'address',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'description'
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type' => Column::TYPE_CHAR,
                            'default' => 'pending',
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'address'
                        ]
                    ),
                    new Column(
                        'phone',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'status'
                        ]
                    ),
                    new Column(
                        'email',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'phone'
                        ]
                    ),
                    new Column(
                        'city',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'email'
                        ]
                    ),
                    new Column(
                        'country',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'city'
                        ]
                    ),
                    new Column(
                        'avatar',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'country'
                        ]
                    ),
                    new Column(
                        'reg',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'avatar'
                        ]
                    ),
                    new Column(
                        'site',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'reg'
                        ]
                    ),
                    new Column(
                        'requisites',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'site'
                        ]
                    ),
                    new Column(
                        'creationDate',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'default' => 'CURRENT_TIMESTAMP',
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'requisites'
                        ]
                    ),
                    new Column(
                        'modifiedDate',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'creationDate'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('FK_companies_image', ['avatar'], null),
                    new Index('FK_companies_country', ['country'], null)
                ],
                'references' => [
                    new Reference(
                        'FK_companies_country',
                        [
                            'referencedTable' => 'countries',
                            'referencedSchema' => 'hr',
                            'columns' => ['country'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'RESTRICT'
                        ]
                    ),
                    new Reference(
                        'FK_companies_image',
                        [
                            'referencedTable' => 'images',
                            'referencedSchema' => 'hr',
                            'columns' => ['avatar'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'CASCADE'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '3',
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
