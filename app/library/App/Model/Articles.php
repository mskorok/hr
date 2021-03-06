<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Articles
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2019-01-13, 14:56:16
 * @method Collection getArticleImages
 * @method ArticleSource getArticleSource
 * @method Collection getArticleTag
 * @method Collection getImages
 * @method Collection getComments
 * @method Collection getTags
 * @method Images getImage
 * @method Languages getLanguage
 * @method SourceCategory getSourceCategory
 * @method Subcategory getSubcategory
 */
class Articles extends DateTrackingModel
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
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $text;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $link;

    /**
     *
     * @var int
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $avatar;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $category_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $language_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    protected $show;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $html;

    /**
     *
     * @var integer
     *
     * @Column(type="integer", nullable=true)
     */
    protected $source_category_id;

    /**
     *
     * @var integer
     */
    protected $article_source_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    protected $parsed;

    /**
     *
     * @var integer
     */
    protected $mapped;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    protected $translated;

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
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * Method to set the value of field avatar
     *
     * @param integer $avatar
     * @return $this
     */
    public function setAvatar(int $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Method to set the value of field category_id
     *
     * @param integer $category_id
     * @return $this
     */
    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * Method to set the value of field language_id
     *
     * @param integer $language_id
     * @return $this
     */
    public function setLanguageId(int $language_id): self
    {
        $this->language_id = $language_id;

        return $this;
    }

    /**
     * @param int $show
     */
    public function setShow(int $show): void
    {
        $this->show = $show;
    }


    /**
     * Method to set the value of field html
     *
     * @param string $html
     * @return $this
     */
    public function setHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Method to set the value of field sources
     *
     * @param integer $sourceCategoryId
     * @return $this
     */
    public function setSourceCategoryId(int $sourceCategoryId): self
    {
        $this->source_category_id = $sourceCategoryId;

        return $this;
    }

    /**
     * Method to set the value of field article_source_id
     *
     * @param integer $article_source_id
     * @return $this
     */
    public function setArticleSourceId(int $article_source_id): self
    {
        $this->article_source_id = $article_source_id;

        return $this;
    }

    /**
     * Method to set the value of field parsed
     *
     * @param integer $parsed
     * @return $this
     */
    public function setParsed(int $parsed): self
    {
        $this->parsed = $parsed;

        return $this;
    }

    /**
     * Method to set the value of field mapped
     *
     * @param integer $mapped
     * @return $this
     */
    public function setMapped(int $mapped): self
    {
        $this->mapped = $mapped;

        return $this;
    }

    /**
     * Method to set the value of field translated
     *
     * @param integer $translated
     * @return $this
     */
    public function setTranslated(int $translated): self
    {
        $this->translated = $translated;

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
     * Returns the value of field text
     *
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Returns the value of field avatar
     *
     * @return int
     */
    public function getAvatar(): ?int
    {
        return (int)$this->avatar;
    }

    /**
     * Returns the value of field category_id
     *
     * @return integer|null
     */
    public function getCategoryId(): ?int
    {
        return (int)$this->category_id;
    }

    /**
     * Returns the value of field language_id
     *
     * @return integer|null
     */
    public function getLanguageId(): ?int
    {
        return (int)$this->language_id;
    }

    /**
     * @return int|null
     */
    public function getShow(): ?int
    {
        return (int)$this->show;
    }

    /**
     * Returns the value of field html
     *
     * @return string|null
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * Returns the value of field sources
     *
     * @return integer |null
     */
    public function getSourceCategoryId(): ?int
    {
        return $this->source_category_id;
    }

    /**
     * Returns the value of field article_source_id
     *
     * @return integer|null
     */
    public function getArticleSourceId(): ?int
    {
        return $this->article_source_id;
    }

    /**
     * Returns the value of field parsed
     *
     * @return integer|null
     */
    public function getParsed(): ?int
    {
        return (int) $this->parsed;
    }

    /**
     * Returns the value of field mapped
     *
     * @return integer|null
     */
    public function getMapped(): ?int
    {
        return (int)$this->mapped;
    }

    /**
     * Returns the value of field translated
     *
     * @return integer|null
     */
    public function getTranslated(): ?int
    {
        return (int) $this->translated;
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('articles');
        $this->hasMany('id', ArticleImages::class, 'article_id', ['alias' => 'ArticleImages']);
        $this->hasMany('id', ArticleTag::class, 'article_id', ['alias' => 'ArticleTag']);
        $this->belongsTo('article_source_id', ArticleSource::class, 'id', ['alias' => 'ArticleSource']);
        $this->hasMany('id', Comments::class, 'article_id', ['alias' => 'Comments']);
        $this->belongsTo('avatar', Images::class, 'id', ['alias' => 'Image']);
        $this->belongsTo('language_id', Languages::class, 'id', ['alias' => 'Language']);
        $this->belongsTo('category_id', Subcategory::class, 'id', ['alias' => 'Subcategory']);
        $this->belongsTo('source_category_id', SourceCategory::class, 'id', ['alias' => 'SourceCategory']);
        $this->hasManyToMany(
            'id',
            ArticleImages::class,
            'article_id',
            'image_id',
            Images::class,
            'id',
            [
                'alias' => 'Images'
            ]
        );
        $this->hasManyToMany(
            'id',
            ArticleTag::class,
            'article_id',
            'tag_id',
            Tag::class,
            'id',
            [
                'alias' => 'Tags'
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
        return 'articles';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Articles[]|Articles|ResultsetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Articles|ResultInterface
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
                'avatar' => 'avatar',
                'html' => 'html',
                'source_category_id' => 'source_category_id',
                'article_source_id' => 'article_source_id',
                'parsed' => 'parsed',
                'mapped' => 'mapped',
                'translated' => 'translated',
                'category_id' => 'category_id',
                'language_id' => 'language_id',
                'show' => 'show'
            ];
    }
}
