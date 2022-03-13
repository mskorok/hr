<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use Phalcon\Mvc\Model;

/**
 * CompanyManager
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2019-01-13, 14:38:49
 * @method Companies getCompanies
 * @method Users getUsers
 */
class CompanyManager extends Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $company_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user_id;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field company_id
     *
     * @param integer $company_id
     * @return $this
     */
    public function setCompanyId($company_id): self
    {
        $this->company_id = $company_id;

        return $this;
    }

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return (int) $this->id;
    }

    /**
     * Returns the value of field company_id
     *
     * @return integer
     */
    public function getCompanyId(): ?int
    {
        return (int) $this->company_id;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId(): ?int
    {
        return (int) $this->user_id;
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('company_manager');
        $this->belongsTo('company_id', Companies::class, 'id', ['alias' => 'Companies']);
        $this->belongsTo('user_id', Users::class, 'id', ['alias' => 'Users']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'company_manager';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return CompanyManager[]|CompanyManager|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return CompanyManager|\Phalcon\Mvc\Model\ResultInterface
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
            'company_id' => 'company_id',
            'user_id' => 'user_id'
        ];
    }
}
