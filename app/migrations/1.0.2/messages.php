<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class MessagesMigration_102
 */
class MessagesMigration_102 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('messages', [
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
                        'parent',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'sender',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'parent'
                        ]
                    ),
                    new Column(
                        'recipient',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'sender'
                        ]
                    ),
                    new Column(
                        'title',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'recipient'
                        ]
                    ),
                    new Column(
                        'content',
                        [
                            'type' => Column::TYPE_TEXT,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'title'
                        ]
                    ),
                    new Column(
                        'category',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 9,
                            'after' => 'content'
                        ]
                    ),
                    new Column(
                        'sendMethod',
                        [
                            'type' => Column::TYPE_CHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'category'
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type' => Column::TYPE_CHAR,
                            'default' => "sent",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'sendMethod'
                        ]
                    ),
                    new Column(
                        'supportStatus',
                        [
                            'type' => Column::TYPE_CHAR,
                            'default' => "not support",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'status'
                        ]
                    ),
                    new Column(
                        'sentDate',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'supportStatus'
                        ]
                    ),
                    new Column(
                        'readDate',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'sentDate'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('FK_Message_sender', ['sender'], null),
                    new Index('FK_Message_recipient', ['recipient'], null),
                    new Index('FK_Message_category', ['category'], null),
                    new Index('FK_Message_parent', ['parent'], null)
                ],
                'references' => [
                    new Reference(
                        'FK_Message_category',
                        [
                            'referencedTable' => 'messenger_category',
                            'referencedSchema' => 'hr',
                            'columns' => ['category'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'CASCADE'
                        ]
                    ),
                    new Reference(
                        'FK_Message_parent',
                        [
                            'referencedTable' => 'messages',
                            'referencedSchema' => 'hr',
                            'columns' => ['parent'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'RESTRICT'
                        ]
                    ),
                    new Reference(
                        'FK_Message_recipient',
                        [
                            'referencedTable' => 'users',
                            'referencedSchema' => 'hr',
                            'columns' => ['recipient'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'CASCADE'
                        ]
                    ),
                    new Reference(
                        'FK_Message_sender',
                        [
                            'referencedTable' => 'users',
                            'referencedSchema' => 'hr',
                            'columns' => ['sender'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'CASCADE',
                            'onDelete' => 'CASCADE'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '1',
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
