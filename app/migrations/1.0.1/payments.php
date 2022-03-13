<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class PaymentsMigration_101
 */
class PaymentsMigration_101 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws \Phalcon\Db\Exception
     */
    public function morph()
    {
        $this->morphTable('payments', [
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
                        'title',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'description',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'title'
                        ]
                    ),
                    new Column(
                        'amount',
                        [
                            'type' => Column::TYPE_FLOAT,
                            'notNull' => true,
                            'size' => 10,
                            'scale' => 2,
                            'after' => 'description'
                        ]
                    ),
                    new Column(
                        'bank',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'amount'
                        ]
                    ),
                    new Column(
                        'date',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'bank'
                        ]
                    ),
                    new Column(
                        'user_subscription',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'date'
                        ]
                    ),
                    new Column(
                        'company_subscription',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'user_subscription'
                        ]
                    ),
                    new Column(
                        'partner_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'size' => 11,
                            'after' => 'company_subscription'
                        ]
                    ),
                    new Column(
                        'currency',
                        [
                            'type' => Column::TYPE_CHAR,
                            'default' => "euro",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'partner_id'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY'),
                    new Index('FK_pay_us', ['user_subscription'], null),
                    new Index('FK_payments_partner', ['partner_id'], null),
                    new Index('FK_payments_company_subscription', ['company_subscription'], null)
                ],
                'references' => [
                    new Reference(
                        'FK_pay_us',
                        [
                            'referencedTable' => 'user_subscription',
                            'referencedSchema' => 'hr',
                            'columns' => ['user_subscription'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'SET NULL'
                        ]
                    ),
                    new Reference(
                        'FK_payments_company_subscription',
                        [
                            'referencedTable' => 'company_subscription',
                            'referencedSchema' => 'hr',
                            'columns' => ['company_subscription'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'RESTRICT'
                        ]
                    ),
                    new Reference(
                        'FK_payments_partner',
                        [
                            'referencedTable' => 'partners',
                            'referencedSchema' => 'hr',
                            'columns' => ['partner_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'SET NULL'
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
