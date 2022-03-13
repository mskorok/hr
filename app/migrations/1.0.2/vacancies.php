<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class VacanciesMigration_102
 */
class VacanciesMigration_102 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     * @throws \Phalcon\Db\Exception
     */
    public function morph()
    {
        $this->morphTable('vacancies', [
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
                        'salary',
                        [
                            'type' => Column::TYPE_FLOAT,
                            'size' => 10,
                            'scale' => 2,
                            'after' => 'name'
                        ]
                    ),
                    new Column(
                        'company_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'salary'
                        ]
                    ),
                    new Column(
                        'professional_experience',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'company_id'
                        ]
                    ),
                    new Column(
                        'work_place',
                        [
                            'type' => Column::TYPE_CHAR,
                            'default' => 'insite',
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'professional_experience'
                        ]
                    ),
                    new Column(
                        'description',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'work_place'
                        ]
                    ),
                    new Column(
                        'location',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 255,
                            'after' => 'description'
                        ]
                    ),
                    new Column(
                        'responsibilities',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'location'
                        ]
                    ),
                    new Column(
                        'main_requirements',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'responsibilities'
                        ]
                    ),
                    new Column(
                        'additional_requirements',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'main_requirements'
                        ]
                    ),
                    new Column(
                        'work_conditions',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'additional_requirements'
                        ]
                    ),
                    new Column(
                        'key_skills',
                        [
                            'type' => Column::TYPE_TEXT,
                            'size' => 1,
                            'after' => 'work_conditions'
                        ]
                    ),
                    new Column(
                        'start',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'key_skills'
                        ]
                    ),
                    new Column(
                        'finish',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'size' => 1,
                            'after' => 'start'
                        ]
                    ),
                    new Column(
                        'creationDate',
                        [
                            'type' => Column::TYPE_DATETIME,
                            'default' => "CURRENT_TIMESTAMP",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'finish'
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
                    new Index('FK_vac_comp', ['company_id'], null)
                ],
                'references' => [
                    new Reference(
                        'FK_vac_comp',
                        [
                            'referencedTable' => 'companies',
                            'referencedSchema' => 'hr',
                            'columns' => ['company_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
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
