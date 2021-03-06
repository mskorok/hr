<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Logs
 * 
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2022-06-06, 17:43:44
 */
class Logs extends Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $log;

    /**
     *
     * @var string
     */
    protected $created;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id): Logs
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field log
     *
     * @param string $log
     * @return $this
     */
    public function setLog($log): Logs
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Method to set the value of field created
     *
     * @param string $created
     * @return $this
     */
    public function setCreated($created): Logs
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the value of field log
     *
     * @return string
     */
    public function getLog(): string
    {
        return $this->log;
    }

    /**
     * Returns the value of field created
     *
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource("logs");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'logs';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Logs[]|Logs|ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Logs|ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap(): array
    {
        return [
            'id' => 'id',
            'log' => 'log',
            'created' => 'created'
        ];
    }

}
