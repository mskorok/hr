<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * EducationalInstitutions
 * 
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2022-03-13, 17:34:11
 * @method Collection getEducationInstitutionLevel
 * @method Countries getCountries
 * @method Collection getEducationLevel
 */
class EducationalInstitutions extends Model
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
    protected $title;

    /**
     *
     * @var string
     */
    protected $specialization;

    /**
     *
     * @var string
     */
    protected $description;

    /**
     *
     * @var integer
     */
    protected $country_id;

    /**
     *
     * @var string
     */
    protected $city;

    /**
     *
     * @var string
     */
    protected $site;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): EducationalInstitutions
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): EducationalInstitutions
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Method to set the value of field specialization
     *
     * @param string $specialization
     * @return $this
     */
    public function setSpecialization(string $specialization): EducationalInstitutions
    {
        $this->specialization = $specialization;

        return $this;
    }

    /**
     * Method to set the value of field description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): EducationalInstitutions
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Method to set the value of field country_id
     *
     * @param integer $country_id
     * @return $this
     */
    public function setCountryId(int $country_id): EducationalInstitutions
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
    public function setCity(string $city): EducationalInstitutions
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Method to set the value of field site
     *
     * @param string $site
     * @return $this
     */
    public function setSite(string $site): EducationalInstitutions
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the value of field title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Returns the value of field specialization
     *
     * @return string|null
     */
    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    /**
     * Returns the value of field description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns the value of field country_id
     *
     * @return integer|null
     */
    public function getCountryId(): ?int
    {
        return $this->country_id;
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
     * Returns the value of field site
     *
     * @return string|null
     */
    public function getSite(): ?string
    {
        return $this->site;
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource("educational_institutions");
        $this->hasMany('id', EducationInstitutionLevel::class, 'institution_id', ['alias' => 'EducationInstitutionLevel']);
        $this->belongsTo('country_id', Countries::class, 'id', ['alias' => 'Countries']);
        $this->hasManyToMany(
            'id',
            EducationInstitutionLevel::class,
            'institution_id',
            'level_id',
            EducationLevel::class,
            'id',
            [
                'alias' => 'EducationLevel'
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
        return 'educational_institutions';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return EducationalInstitutions[]|EducationalInstitutions|ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return EducationalInstitutions|ResultInterface
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
            'title' => 'title',
            'specialization' => 'specialization',
            'description' => 'description',
            'country_id' => 'country_id',
            'city' => 'city',
            'site' => 'site'
        ];
    }

}
