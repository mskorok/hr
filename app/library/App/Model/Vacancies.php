<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use Phalcon\Assets\Collection;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Vacancies
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2019-01-13, 13:49:29
 * @method Collection getApplicants
 * @method Collection getApplied
 * @method Companies getCompanies
 * @method Collection getDeals
 * @method Collection getFavorites
 * @method Collection getVacancyJobTypes
 * @method Collection getJobTypes
 * @method Collection getCompanyAvatar
 * @method Countries getCountries;
 *
 */
class Vacancies extends DateTrackingModel
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
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=true)
     */
    protected $salary;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $currency;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $company_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $country_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $professional_experience;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $work_place;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $responsibilities;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $main_requirements;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $additional_requirements;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $work_conditions;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $key_skills;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $start;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $finish;

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
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field salary
     *
     * @param double $salary
     * @return $this
     */
    public function setSalary(float $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * Method to set the value of field currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

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
     * Method to set the value of field country_id
     *
     * @param integer $country_id
     * @return $this
     */
    public function setCountryId(int $country_id): self
    {
        $this->country_id = $country_id;

        return $this;
    }

    /**
     * Method to set the value of field city
     *
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Method to set the value of field professional_experience
     *
     * @param string $professional_experience
     * @return $this
     */
    public function setProfessionalExperience(string $professional_experience): self
    {
        $this->professional_experience = $professional_experience;

        return $this;
    }

    /**
     * Method to set the value of field description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Method to set the value of field responsibilities
     *
     * @param string $responsibilities
     * @return $this
     */
    public function setResponsibilities(string $responsibilities): self
    {
        $this->responsibilities = $responsibilities;

        return $this;
    }

    /**
     * Method to set the value of field main_requirements
     *
     * @param string $main_requirements
     * @return $this
     */
    public function setMainRequirements(string $main_requirements): self
    {
        $this->main_requirements = $main_requirements;

        return $this;
    }

    /**
     * Method to set the value of field additional_requirements
     *
     * @param string $additional_requirements
     * @return $this
     */
    public function setAdditionalRequirements(string $additional_requirements): self
    {
        $this->additional_requirements = $additional_requirements;

        return $this;
    }

    /**
     * Method to set the value of field work_conditions
     *
     * @param string $work_conditions
     * @return $this
     */
    public function setWorkConditions(string $work_conditions): self
    {
        $this->work_conditions = $work_conditions;

        return $this;
    }

    /**
     * Method to set the value of field key_skills
     *
     * @param string $key_skills
     * @return $this
     */
    public function setKeySkills(string $key_skills): self
    {
        $this->key_skills = $key_skills;

        return $this;
    }

    /**
     * Method to set the value of field start
     *
     * @param string $start
     * @return $this
     */
    public function setStart(string $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Method to set the value of field finish
     *
     * @param string $finish
     * @return $this
     */
    public function setFinish(string $finish): self
    {
        $this->finish = $finish;

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
     * Returns the value of field name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Returns the value of field salary
     *
     * @return double
     */
    public function getSalary(): ?float
    {
        return (float)$this->salary;
    }

    /**
     * Returns the value of field currency
     *
     * @return string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
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
     * Returns the value of field country_id
     *
     * @return integer|null
     */
    public function getCountryId(): ?int
    {
        return (int)$this->country_id;
    }

    /**
     * Returns the value of field city
     *
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Returns the value of field professional_experience
     *
     * @return string
     */
    public function getProfessionalExperience(): ?string
    {
        return $this->professional_experience;
    }

    /**
     * Returns the value of field description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns the value of field responsibilities
     *
     * @return string
     */
    public function getResponsibilities(): ?string
    {
        return $this->responsibilities;
    }

    /**
     * Returns the value of field main_requirements
     *
     * @return string
     */
    public function getMainRequirements(): ?string
    {
        return $this->main_requirements;
    }

    /**
     * Returns the value of field additional_requirements
     *
     * @return string
     */
    public function getAdditionalRequirements(): ?string
    {
        return $this->additional_requirements;
    }

    /**
     * Returns the value of field work_conditions
     *
     * @return string
     */
    public function getWorkConditions(): ?string
    {
        return $this->work_conditions;
    }

    /**
     * Returns the value of field key_skills
     *
     * @return string
     */
    public function getKeySkills(): ?string
    {
        return $this->key_skills;
    }

    /**
     * Returns the value of field start
     *
     * @return string
     */
    public function getStart(): ?string
    {
        return $this->start;
    }

    /**
     * Returns the value of field finish
     *
     * @return string
     */
    public function getFinish(): ?string
    {
        return $this->finish;
    }

    /**
     * @return string
     */
    public function getWorkPlace(): ?string
    {
        return $this->work_place;
    }

    /**
     * @param string $work_place
     */
    public function setWorkPlace(string $work_place): void
    {
        $this->work_place = $work_place;
    }

    /**
     * @return string
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('vacancies');
        $this->belongsTo('company_id', Companies::class, 'id', ['alias' => 'Companies']);
        $this->hasMany('id', Applied::class, 'vacancy_id', ['alias' => 'Applied']);
        $this->hasMany('id', Deals::class, 'vacancy_id', ['alias' => 'Deals']);
        $this->hasMany('id', Favorites::class, 'vacancy_id', ['alias' => 'Favorites']);
        $this->hasMany('id', VacancyJobTypes::class, 'vacancy_id', ['alias' => 'VacancyJobTypes']);
        $this->belongsTo('country_id', Countries::class, 'id', ['alias' => 'Countries']);
        $this->hasManyToMany(
            'id',
            VacancyJobTypes::class,
            'vacancy_id',
            'type_id',
            JobTypes::class,
            'id',
            [
                'alias' => 'JobTypes'
            ]
        );
        $this->hasManyToMany(
            'company_id',
            Companies::class,
            'id',
            'avatar',
            Images::class,
            'id',
            [
                'alias' => 'CompanyAvatar'
            ]
        );
        $this->hasManyToMany(
            'id',
            Applied::class,
            'vacancy_id',
            'user_id',
            Users::class,
            'id',
            [
                'alias' => 'Applicants'
            ]
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'vacancies';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Vacancies[]|Vacancies|ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Vacancies|ResultInterface
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
        return parent::columnMap() + [
                'id' => 'id',
                'name' => 'name',
                'salary' => 'salary',
                'currency' => 'currency',
                'company_id' => 'company_id',
                'country_id' => 'country_id',
                'city' => 'city',
                'professional_experience' => 'professional_experience',
                'work_place' => 'work_place',
                'description' => 'description',
                'location' => 'location',
                'responsibilities' => 'responsibilities',
                'main_requirements' => 'main_requirements',
                'additional_requirements' => 'additional_requirements',
                'work_conditions' => 'work_conditions',
                'key_skills' => 'key_skills',
                'start' => 'start',
                'finish' => 'finish'
            ];
    }
}
