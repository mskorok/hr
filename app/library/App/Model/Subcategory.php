<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Subcategory
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2021-09-12, 17:04:49
 *
 * @method Categories getCategories
 * @method Images getImages
 * @method Collection getArticles
 */
class Subcategory extends DateTrackingModel
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
    protected $description;

    /**
     *
     * @var string
     */
    protected $text;

    /**
     *
     * @var string
     */
    protected $link;

    /**
     *
     * @var integer
     */
    protected $category_id;

    /**
     *
     * @var integer
     */
    protected $image_id;

    /**
     *
     * @var string
     */
    protected $creationDate;

    /**
     *
     * @var string
     */
    protected $modifiedDate;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): Subcategory
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
    public function setTitle(string $title): Subcategory
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
    public function setDescription(string $description): Subcategory
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
    public function setText(string $text): Subcategory
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Method to set the value of field link
     *
     * @param string $link
     * @return $this
     */
    public function setLink(string $link): Subcategory
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Method to set the value of field category_id
     *
     * @param integer $category_id
     * @return $this
     */
    public function setCategoryId(int $category_id): Subcategory
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * Method to set the value of field image_id
     *
     * @param integer $image_id
     * @return $this
     */
    public function setImageId(int $image_id): Subcategory
    {
        $this->image_id = $image_id;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer | null
     */
    public function getId(): ?int
    {
        return (int)$this->id;
    }

    /**
     * Returns the value of field title
     *
     * @return string | null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Returns the value of field description
     *
     * @return string | null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Returns the value of field text
     *
     * @return string | null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Returns the value of field link
     *
     * @return string | null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Returns the value of field category_id
     *
     * @return integer | null
     */
    public function getCategoryId(): ?int
    {
        return (int)$this->category_id;
    }

    /**
     * Returns the value of field image_id
     *
     * @return integer|null
     */
    public function getImageId(): ?int
    {
        return (int)$this->image_id;
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource("subcategory");
        $this->hasMany('id', Articles::class, 'category_id', ['alias' => 'Articles']);
        $this->belongsTo('category_id', Categories::class, 'id', ['alias' => 'Categories']);
        $this->belongsTo('image_id', Images::class, 'id', ['alias' => 'Images']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'subcategory';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Subcategory[]|Subcategory|ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Subcategory|ResultInterface
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
                'title' => 'title',
                'description' => 'description',
                'text' => 'text',
                'link' => 'link',
                'category_id' => 'category_id',
                'image_id' => 'image_id',
            ];
    }

}
