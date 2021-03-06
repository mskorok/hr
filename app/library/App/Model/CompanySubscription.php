<?php

namespace App\Model;
use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model;

/**
 * CompanySubscription
 * 
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2019-05-09, 10:25:14
 *
 * @method Collection getPayments
 * @method Companies getCompanies
 * @method Subscriptions getSubscriptions
 */
class CompanySubscription extends Model
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
    protected $subscription_id;

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
     * Method to set the value of field subscription_id
     *
     * @param integer $subscription_id
     * @return $this
     */
    public function setSubscriptionId(int $subscription_id): self
    {
        $this->subscription_id = $subscription_id;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return (int)$this->id;
    }

    /**
     * Returns the value of field company_id
     *
     * @return integer
     */
    public function getCompanyId(): ?int
    {
        return (int)$this->company_id;
    }

    /**
     * Returns the value of field subscription_id
     *
     * @return integer
     */
    public function getSubscriptionId(): ?int
    {
        return (int)$this->subscription_id;
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('company_subscription');
        $this->hasMany('id', Payments::class, 'company_subscription', ['alias' => 'Payments']);
        $this->belongsTo('company_id', Companies::class, 'id', ['alias' => 'Companies']);
        $this->belongsTo('subscription_id', Subscriptions::class, 'id', ['alias' => 'Subscriptions']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'company_subscription';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return CompanySubscription[]|CompanySubscription|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return CompanySubscription|\Phalcon\Mvc\Model\ResultInterface
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
            'subscription_id' => 'subscription_id'
        ];
    }

}
