<?php
declare(strict_types=1);


use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ProductsMigration
 */
class SettingsMigration_100 extends Migration
{

    /**
     * @throws \Phalcon\Db\Exception
     */
    public function up()
    {
        $this->morphTable(
            'settings',
            [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'size'          => 11,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'first'         => true
                        ]
                    ),
                    new Column(
                        'name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 255,
                            'notNull' => true,
                            'after'   => 'id'
                        ]
                    ),

                    new Column(
                        'stringData',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 255,
                            'notNull' => false,
                            'after'   => 'name'
                        ]
                    ),
                    new Column(
                        'integerData',
                        [
                            'type'     => Column::TYPE_INTEGER,
                            'size'     => 11,
                            'unsigned' => true,
                            'notNull'  => false,
                            'after'    => 'stringData'
                        ]
                    ),

                    new Column(
                        'boolData',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'size'    => 1,
                            'unsigned' => true,
                            'notNull' => false,
                            'after'   => 'integerData'
                        ]
                    ),
                ],
                'indexes' => [
                    new Index(
                        'PRIMARY',
                        ['id']
                    )
                ],
                'options' => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'ENGINE'          => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_unicode_ci'
                ]
            ]
        );
    }
}
