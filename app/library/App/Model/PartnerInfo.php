<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use Phalcon\Mvc\Model;

/**
 * PartnerInfo
 * 
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2020-09-29, 21:20:24
 * @method Companies getCompanies
 */
class PartnerInfo extends Model
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
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $info;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $level;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=true)
     */
    protected $approved;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): self
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
    public function setCompanyId(int $company_id): self
    {
        $this->company_id = $company_id;

        return $this;
    }

    /**
     * Method to set the value of field info
     *
     * @param string $info
     * @return $this
     */
    public function setInfo(string $info): self
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Method to set the value of field level
     *
     * @param string $level
     * @return $this
     */
    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Method to set the value of field approved
     *
     * @param integer $approved
     * @return $this
     */
    public function setApproved(int $approved): self
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return (int)$this->id;
    }

    /**
     * Returns the value of field company_id
     *
     * @return integer|null
     */
    public function getCompanyId(): ?int
    {
        return (int)$this->company_id;
    }

    /**
     * Returns the value of field info
     *
     * @return string
     */
    public function getInfo(): ?string
    {
        return $this->info;
    }

    /**
     * Returns the value of field level
     *
     * @return string|null
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * Returns the value of field approved
     *
     * @return integer|null
     */
    public function getApproved(): ?int
    {
        return (int)$this->approved;
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('partner_info');
        $this->belongsTo('company_id', Companies::class, 'id', ['alias' => 'Companies']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'partner_info';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return PartnerInfo[]|PartnerInfo|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return PartnerInfo|\Phalcon\Mvc\Model\ResultInterface
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
            'info' => 'info',
            'level' => 'level',
            'approved' => 'approved'
        ];
    }

}
