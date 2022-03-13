<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use Phalcon\Forms\Form;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class BaseForm extends Form
{
    use \App\Traits\Form;

    protected $admin = false;

    protected $cnt;

    protected $isImage = false;

    protected $imageField = 'fileName';

    protected $imageRelatedField = false;

    protected $show = true;

    protected $user;

    public static $relative = [];

    /**
     * @return null|string
     */
    public function getCsrf()
    {
        if (isset($this->security)) {
            return $this->security->getToken();
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getCnt()
    {
        return $this->cnt;
    }

    /**
     * @param mixed $cnt
     */
    public function setCnt($cnt)
    {
        $this->cnt = $cnt;
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->isImage;
    }

    /**
     * @param bool $isImage
     */
    public function setIsImage(bool $isImage)
    {
        $this->isImage = $isImage;
    }

    /**
     * @return string
     */
    public function getImageField(): string
    {
        return $this->imageField;
    }

    /**
     * @param string $imageField
     */
    public function setImageField(string $imageField)
    {
        $this->imageField = $imageField;
    }

    /**
     * @return bool |string
     */
    public function getImageRelatedField()
    {
        return $this->imageRelatedField;
    }

    /**
     * @param bool|string $imageRelatedField
     */
    public function setImageRelatedField($imageRelatedField)
    {
        $this->imageRelatedField = $imageRelatedField;
    }

    /**
     * @return bool
     */
    public function isShow(): bool
    {
        return $this->show;
    }

    /**
     * @param bool $show
     */
    public function setShow(bool $show)
    {
        $this->show = $show;
    }

    /**
     * @param bool $admin
     */
    public function setAdmin(bool $admin)
    {
        $this->admin = $admin;
    }
}
