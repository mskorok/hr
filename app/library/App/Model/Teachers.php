<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model;


/**
 * Teachers
 * 
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2020-04-26, 13:26:56
 *
 * @method Collection getTeacherSchedule
 * @method Collection getSchedule
 * @method Users getUsers
 */
class Teachers extends Model
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
    protected $user_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $rate;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $skills;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $text;

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
     * Method to set the value of field rate
     *
     * @param integer $rate
     * @return $this
     */
    public function setRate(int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Method to set the value of field skills
     *
     * @param string $skills
     * @return $this
     */
    public function setSkills(string $skills): self
    {
        $this->skills = $skills;

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
     * Method to set the value of field text
     *
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

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
     * Returns the value of field user_id
     *
     * @return integer|null
     */
    public function getUserId(): ?int
    {
        return (int)$this->user_id;
    }

    /**
     * Returns the value of field rate
     *
     * @return integer|null
     */
    public function getRate(): ?int
    {
        return (int)$this->rate;
    }

    /**
     * Returns the value of field skills
     *
     * @return string|null
     */
    public function getSkills(): ?string
    {
        return $this->skills;
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
     * Returns the value of field description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns the value of field text
     *
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('teachers');
        $this->hasMany('id', TeacherSchedule::class, 'teacher_id', ['alias' => 'TeacherSchedule']);
        $this->belongsTo('user_id', Users::class, 'id', ['alias' => 'Users']);
        $this->hasManyToMany(
            'id',
            TeacherSchedule::class,
            'teacher_id',
            'schedule_id',
            Schedule::class,
            'id',
            [
                'alias' => 'Schedule'
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
        return 'teachers';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Teachers[]|Teachers|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Teachers|\Phalcon\Mvc\Model\ResultInterface
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
            'user_id' => 'user_id',
            'rate' => 'rate',
            'skills' => 'skills',
            'title' => 'title',
            'description' => 'description',
            'text' => 'text'
        ];
    }

}
