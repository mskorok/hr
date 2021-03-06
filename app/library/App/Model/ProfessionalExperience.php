<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use Phalcon\Mvc\Model;

/**
 * ProfessionalExperience
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2019-01-13, 14:20:15
 * @method Users getUsers
 */
class ProfessionalExperience extends Model
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
    protected $title;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $description;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $organization;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $location;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $site;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $professional_area;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $position;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
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
     * Method to set the value of field title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

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
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Method to set the value of field organization
     *
     * @param string $organization
     * @return $this
     */
    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Method to set the value of field location
     *
     * @param string $location
     * @return $this
     */
    public function setCountry(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Method to set the value of field site
     *
     * @param string $site
     * @return $this
     */
    public function setSite(string $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Method to set the value of field professional_area
     *
     * @param string $professional_area
     * @return $this
     */
    public function setProfessionalArea(string $professional_area): self
    {
        $this->professional_area = $professional_area;

        return $this;
    }

    /**
     * Method to set the value of field position
     *
     * @param string $position
     * @return $this
     */
    public function setPosition(string $position): self
    {
        $this->position = $position;

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
     * Returns the value of field title
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
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
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId(): ?int
    {
        return (int)$this->user_id;
    }

    /**
     * Returns the value of field organization
     *
     * @return string
     */
    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    /**
     * Returns the value of field location
     *
     * @return string
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Returns the value of field site
     *
     * @return string
     */
    public function getSite(): ?string
    {
        return $this->site;
    }

    /**
     * Returns the value of field professional_area
     *
     * @return string
     */
    public function getProfessionalArea(): ?string
    {
        return $this->professional_area;
    }

    /**
     * Returns the value of field position
     *
     * @return string
     */
    public function getPosition(): ?string
    {
        return $this->position;
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
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('professional_experience');
        $this->belongsTo('user_id', Users::class, 'id', ['alias' => 'Users']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'professional_experience';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProfessionalExperience[]|ProfessionalExperience|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ProfessionalExperience|\Phalcon\Mvc\Model\ResultInterface
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
            'description' => 'description',
            'user_id' => 'user_id',
            'organization' => 'organization',
            'location' => 'location',
            'site' => 'site',
            'professional_area' => 'professional_area',
            'position' => 'position',
            'start' => 'start',
            'finish' => 'finish'
        ];
    }
}
