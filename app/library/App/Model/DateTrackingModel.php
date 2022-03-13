<?php
declare(strict_types=1);

namespace App\Model;

use Phalcon\Mvc\Model;

/**
 * Phalcon\Mvc\Model
 *
 * Phalcon\Mvc\Model connects business objects and database tables to create
 * a persistable domain model where logic and data are presented in one wrapping.
 * It‘s an implementation of the object-relational mapping (ORM).
 *
 * A model represents the information (data) of the application and the rules to manipulate that data.
 * Models are primarily used for managing the rules of interaction with a corresponding database table.
 * In most cases, each table in your database will correspond to one model in your application.
 * The bulk of your application's business logic will be concentrated in the models.
 *
 * Phalcon\Mvc\Model is the first ORM written in Zephir/C languages for PHP, giving to developers high performance
 * when interacting with databases while is also easy to use.
 *
 * <code>
 * $robot = new Robots();
 *
 * $robot->type = "mechanical";
 * $robot->name = "Astro Boy";
 * $robot->year = 1952;
 *
 * if ($robot->save() === false) {
 *     echo "Umh, We can store robots: ";
 *
 *     $messages = $robot->getMessages();
 *
 *     foreach ($messages as $message) {
 *         echo message;
 *     }
 * } else {
 *     echo "Great, a new robot was saved successfully!";
 * }
 * </code>
 */
class DateTrackingModel extends Model
{
    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $creationDate;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $modifiedDate;

    /**
     * @return array
     */
    public function columnMap():array
    {
        return [
            'creationDate' => 'creationDate',
            'modifiedDate' => 'modifiedDate'
        ];
    }

    /**
     *
     */
    public function beforeCreate(): void
    {
        $this->creationDate = date('Y-m-d H:i:s');
        $this->modifiedDate = $this->creationDate;
    }

    public function beforeUpdate(): void
    {
        $this->modifiedDate = date('Y-m-d H:i:s');
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    /**
     * @param mixed $modifiedDate
     */
    public function setModifiedDate($modifiedDate): void
    {
        $this->modifiedDate = $modifiedDate;
    }
}
