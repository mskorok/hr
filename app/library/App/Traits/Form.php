<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 15:02
 */

namespace App\Traits;

use App\Constants\Services;
use App\Forms\BaseForm;
use App\Model\Images;
use Phalcon\Forms\Element;
use Phalcon\Mvc\Model;
use ReflectionClass;

/**
 * Trait Form
 * @package App\Traits
 */
trait Form
{

    public $html;

    protected $method = 'POST';

    protected $class = '';

    protected $formId = 'user_form';

    protected $additionalElements = [];

    protected $additionalName;

    protected $additionalForms = [];

    protected $name;

    protected $collection = false;

    protected $showImage = false;


    /**
     * @param Element $element
     * @param bool $echo
     * @return null|string
     */
    public function renderDecorated(Element $element, $echo = false)
    {

        // Get any generated messages for the current element
        /** @var \Phalcon\Validation\Message\Group $messages */
        $messages = $this->getMessagesFor(
            $element->getName()
        );
        $html = '';

        if (\count($messages)) {
            // Print each element
            $html .= '<div class="messages">';

            foreach ($messages as $message) {
                $html .= $this->flash->error($message);
            }

            $html .= '</div>';
        }

        $html .= '<p>';

        $html .= '<label for="' . $element->getAttribute('id')
            . '" class="d-b">' . $element->getLabel() . '</label>';

        $html .= $element;

        $html .= '</p>';
        if ($echo) {
            echo $html;
            return null;
        }
        return $html;
    }

    /**
     * @param null $imageField
     * @param bool $show
     * @throws \ReflectionException
     */
    public function renderForm($imageField = null, $show = true)
    {
        $action = $this->getAction() ?: '';
        $html = '<form action="' . $action . '" method="' . $this->getMethod()
            . '" ' . $this->getClass() . '  ' . $this->getMultipart() . ' id="' . $this->formId . '">';

        /** @var Element[] $elements */
        $elements = $this->getElements();
        $html .= '<div class="row m-t-1"><div class="col-xs-12">';

        foreach ($elements as $element) {
            $hiddenClass = $element instanceof Element\Hidden ? 'hidden' : '';
            $html .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $hiddenClass . '">';
            $html .= '<div class="form-group">';
            if (($imageField || $this->getImageRelatedField()) && $element->getName() === $this->getImageField()) {
                if (!$imageField) {
                    $imageField = $this->getImageRelatedField();
                }
                $config = $this->getDI()->get(Services::CONFIG);
                $uploadsDir = $config->hostName;
                $method = 'get' . ucfirst($imageField);
                $entity = $this->getEntity();
                if ($entity instanceof Model && method_exists($entity, $method)) {
                    $imageId = (int) $entity->$method();
                    if ($imageId) {
                        $image = Images::findFirst($imageId);
                        if ($image instanceof Images) {
                            $url = $uploadsDir . $image->getPath() . $image->getFileName();
                            $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                            if (!$this->isShow()) {
                                $html .= $this->renderDecorated($element);
                            }
                        } else {
                            $html .= $this->renderDecorated($element);
                        }
                    } else {
                        $html .= $this->renderDecorated($element);
                    }
                } else {
                    $html .= $this->renderDecorated($element);
                }
            } else {
                $html .= $this->renderDecorated($element);
            }
            $html .= '</div>';
            $html .= '</div>';
            $hiddenClass = null;
            unset($hiddenClass);
        }
        $html .= '</div>';
        $html .= '</div>';

        $name = (new ReflectionClass($this))->getShortName();

        if (\count($this->additionalElements) > 0) {
            $html .= '<div id="additional_elements" class="additional-elements"><br><br>';
            $html .= '<h4>' . $this->getAdditionalName() . '</h4><br>';
            $html .= '<div class="row m-t-1"><div class="col-xs-12">';
            foreach ($this->additionalElements as $element) {
                $hiddenClass = $element instanceof Element\Hidden ? 'hidden' : '';
                $html .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $hiddenClass . '">';
                $html .= '<div class="form-group">';
                $html .= $this->renderDecorated($element);
                $html .= '</div>';
                $html .= '</div>';
                $hiddenClass = null;
                unset($hiddenClass);
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '<div class="red-color pull-right pointer glyphicon glyphicon-remove hidden"></div>';
        $html .= '<hr>';

        if ($show) {
            $html .=
                '<button type="submit" id="submit_'
                . $name . '_button_' . $this->cnt
                . '" class="btn btn-orange" style="margin-bottom: 50px">Submit</button>';
        }
        $html .= '</form>';

        $this->html = $html;
    }


    /**
     * @throws \ReflectionException
     */
    public function renderImageForm()
    {
        $config = $this->getDI()->get(Services::CONFIG);
        $uploadsDir = $config->hostName . '/uploads/';
        $entity = $this->getEntity();
        $name = (new ReflectionClass($this))->getShortName();

        $action = $this->getAction() ?: '';
        $html = '<form action="' . $action . '" method="' . $this->getMethod()
            . '" ' . $this->getClass() . '  ' . $this->getMultipart() . ' id="' . $this->formId . '">';

        $hidden = $this->showImage ? '' : 'hidden';

        /** @var Element[] $elements */
        $elements = $this->getElements();
        $html .= '<div class="col-xs-12">';
        $html .= '<div class="row m-t-1"><div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">';
        if ($entity && property_exists($entity, $this->getImageField())) {
            $method = 'get' . ucfirst($this->getImageField());
            $url = $uploadsDir . $entity->$method();
            $html .= '<div class="form-group"><img style="width: 130px;" src="' . $url . '"></div>';
        }


        foreach ($elements as $element) {
            $html .= '<div class="' . $hidden . '">';
            $html .= '<div class="form-group">';
            $html .= $this->renderDecorated($element);
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="red-color pull-right pointer glyphicon glyphicon-remove ' . $hidden . '"></div>';
        $html .= '<hr>';
        $html .=
            '<button type="submit" id="submit_'
            . $name . '_button_' . $this->cnt
            . '" class="btn btn-orange" style="margin-bottom: 50px">Submit</button>';
        $html .= '</form>';
        $this->html = $html;
    }

    /**
     * @throws \ReflectionException
     */
    public function renderComplexAddForm()
    {
        $action = $this->getAction() ?: '';
        $html = '<form action="' . $action . '" method="' . $this->getMethod()
            . '" ' . $this->getClass() . '  ' . $this->getMultipart() . ' id="' . $this->formId . '">';

        /** @var Element[] $elements */
        $elements = $this->getElements();
        $html .= '<div class="row m-t-1"><div class="col-xs-12">';

        foreach ($elements as $element) {
            $hiddenClass = $element instanceof Element\Hidden ? 'hidden' : '';
            $html .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $hiddenClass . '">';
            $html .= '<div class="form-group">';
            if ($element->getName() === $this->getImageField()) {
                $config = $this->getDI()->get(Services::CONFIG);
                $uploadsDir = $config->hostName;
                $entity = $this->getEntity();
                if ($entity instanceof Model) {
                    if ($this->getImageRelatedField()) {
                        $method = 'get' . ucfirst($this->getImageRelatedField());
                        if (method_exists($entity, $method)) {
                            $imageId = (int) $entity->$method();
                            if ($imageId) {
                                $image = Images::findFirst($imageId);
                                if ($image instanceof Images) {
                                    $url = $uploadsDir . $image->getPath() . $image->getFileName();
                                    $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                    if (!$this->isShow()) {
                                        $html .= $this->renderDecorated($element);
                                    }
                                } else {
                                    $html .= $this->renderDecorated($element);
                                }
                            } else {
                                $html .= $this->renderDecorated($element);
                            }
                        } else {
                            $html .= $this->renderDecorated($element);
                        }
                    } elseif ($this->isImage && $entity instanceof Images && $entity->getId()) {
                        $url = $uploadsDir . $entity->getPath() . $entity->getFileName();
                        $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                        if (!$this->isShow()) {
                            $html .= $this->renderDecorated($element);
                        }
                    } else {
                        $html .= $this->renderDecorated($element);
                    }
                } else {
                    $html .= $this->renderDecorated($element);
                }
            } else {
                $html .= $this->renderDecorated($element);
            }
            $html .= '</div>';
            $html .= '</div>';
            $hiddenClass = null;
            unset($hiddenClass);
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '<hr>';
        $html .=
            '<button type="submit" id="submit_button" class="btn btn-orange" style="margin-bottom: 50px">Submit</button>';

        $html .= '</form>';

        $html .= '<hr class="fwb" />';

        if (\count($this->additionalForms) > 0) {
            $html .= '<div id="additional_forms" class="additional-forms">';
            $html .= '<h4>' . $this->getAdditionalName() . '</h4><br>';
            $html .= '<div class="saved-result"></div>';
            foreach ($this->additionalForms as $form) {
                if (!($form instanceof \Phalcon\Forms\Form)) {
                    continue;
                }
                /** @var  $form BaseForm */
                $name = (new ReflectionClass($form))->getShortName();
                $counter = $form->getCnt();


                $action = $form->getAction() ?: '';
                $html .= '<div id="additional_forms_'
                    . strtolower($form->getAdditionalName()) . '" class="additional-form">';
                $html .= '<h4>' . $form->getAdditionalName() . '</h4><br>';
                $html .= '<div class="saved-result"></div>';
                $html .= '<div>';
                $html .= '<form action="' . $action . '" method="' . $form->getMethod()
                    . '" ' . $form->getClass() . '  ' . $form->getMultipart()
                    . ' id="' . $form->getFormId() . '_counter_' . $counter . '">';

                /** @var Element[] $elements */
                $elements = $form->getElements();
                $html .= '<div class="row m-t-1"><div class="col-xs-12">';

                foreach ($elements as $element) {
                    $hiddenClass = $element instanceof Element\Hidden ? 'hidden' : '';
                    $html .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $hiddenClass . '">';
                    $html .= '<div class="form-group">';
                    if ($element->getName() === $this->getImageField()) {
                        $config = $this->getDI()->get(Services::CONFIG);
                        $uploadsDir = $config->hostName;
                        $entity = $form->getEntity();
                        if ($entity instanceof Model) {
                            if ($form->getImageRelatedField()) {
                                $method = 'get' . ucfirst($form->getImageRelatedField());
                                if (method_exists($entity, $method)) {
                                    $imageId = (int) $entity->$method();
                                    if ($imageId) {
                                        $image = Images::findFirst($imageId);
                                        if ($image instanceof Images) {
                                            $url = $uploadsDir . $image->getPath() . $image->getFileName();
                                            $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                            if (!$this->isShow()) {
                                                $html .= $this->renderDecorated($element);
                                            }
                                        } else {
                                            $html .= $this->renderDecorated($element);
                                        }
                                    } else {
                                        $html .= $this->renderDecorated($element);
                                    }
                                } else {
                                    $html .= $this->renderDecorated($element);
                                }
                            } elseif ($form->isImage && $entity instanceof Images && $entity->getId()) {
                                $url = $uploadsDir . $entity->getPath() . $entity->getFileName();
                                $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                if (!$this->isShow()) {
                                    $html .= $this->renderDecorated($element);
                                }
                            } else {
                                $html .= $this->renderDecorated($element);
                            }
                        } else {
                            $html .= $this->renderDecorated($element);
                        }
                    } else {
                        $html .= $this->renderDecorated($element);
                    }

                    $html .= '</div>';
                    $html .= '</div>';
                    $hiddenClass = null;
                    unset($hiddenClass);
                }

                $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="red-color pull-right pointer glyphicon glyphicon-remove hidden"></div>';
                $html .= '<hr>';

                $html .=
                    '<button type="submit" id="submit_'
                    . $name . '_button_' . $this->cnt
                    . '" class="btn btn-orange" style="margin-bottom: 50px">Submit ' . '</button>';

                $html .= '</form>';
                $html .= '</div>';

                $hidden = $form->isCollection() ? '' : 'hidden jd-hidden-button';

                $html .= '<div id="additional_forms_plus_'
                    . $form->getAdditionalName()
                    . '" class="additional-form-plus glyphicon glyphicon-plus-sign ' . $hidden
                    . '"></div> <span class="jd-slash ' . $hidden . '">/</span> ';
                $html .= '<div id="additional_forms_minus_'
                    . $form->getAdditionalName()
                    . '" class="additional-form-minus glyphicon glyphicon-minus-sign ' . $hidden . '"></div>';


                $html .= '<hr class="fwb" />';
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        $this->html = $html;
    }


    /**
     * @throws \ReflectionException
     */
    public function renderComplexShowForm()
    {
        $action = $this->getAction() ?: '';
        $html = '<form action="' . $action . '" method="' . $this->getMethod()
            . '" ' . $this->getClass() . '  ' . $this->getMultipart() . ' id="' . $this->formId . '">';

        /** @var Element[] $elements */
        $elements = $this->getElements();
        $html .= '<div class="row m-t-1"><div class="col-xs-12">';

        foreach ($elements as $element) {
            $element->setAttribute('disabled', 'disabled');
            $hiddenClass = $element instanceof Element\Hidden ? 'hidden' : '';
            $html .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $hiddenClass . '">';
            $html .= '<div class="form-group">';
            if ($element->getName() === $this->getImageField()) {
                $config = $this->getDI()->get(Services::CONFIG);
                $uploadsDir = $config->hostName;
                $entity = $this->getEntity();
                if ($entity instanceof Model) {
                    if ($this->getImageRelatedField()) {
                        $method = 'get' . ucfirst($this->getImageRelatedField());
                        if (method_exists($entity, $method)) {
                            $imageId = (int) $entity->$method();
                            if ($imageId) {
                                $image = Images::findFirst($imageId);
                                if ($image instanceof Images) {
                                    $url = $uploadsDir . $image->getPath() . $image->getFileName();
                                    $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                    if (!$this->isShow()) {
                                        $html .= $this->renderDecorated($element);
                                    }
                                } else {
                                    $html .= $this->renderDecorated($element);
                                }
                            } else {
                                $html .= $this->renderDecorated($element);
                            }
                        } else {
                            $html .= $this->renderDecorated($element);
                        }
                    } elseif ($this->isImage && $entity instanceof Images && $entity->getId()) {
                        $url = $uploadsDir . $entity->getPath() . $entity->getFileName();
                        $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                        if (!$this->isShow()) {
                            $html .= $this->renderDecorated($element);
                        }
                    } else {
                        $html .= $this->renderDecorated($element);
                    }
                } else {
                    $html .= $this->renderDecorated($element);
                }
            } else {
                $html .= $this->renderDecorated($element);
            }
            $html .= '</div>';
            $html .= '</div>';
            $hiddenClass = null;
            unset($hiddenClass);
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</form>';

        $html .= '<hr class="fwb" />';

        $previousForm = null;
        $currentForm = null;

        if (\count($this->additionalForms) > 0) {
            $html .= '<div id="additional_forms" class="additional-forms">';
            $html .= '<h4>' . $this->getAdditionalName() . '</h4><br>';
            $html .= '<div class="saved-result"></div>';
            foreach ($this->additionalForms as $form) {
                if (!($form instanceof \Phalcon\Forms\Form)) {
                    continue;
                }
                /** @var  $form BaseForm */
                $name = (new ReflectionClass($form))->getShortName();
                $counter = $form->getCnt();

                $newFormType = $previousForm !== $name;
                $startForms = $previousForm;
                $previousForm = $name;

                $action = $form->getAction() ?: '';
                if ($newFormType) {
                    if ($startForms) {
                        $html .= '</div>';
                        $html .= '<hr />';
                    }
                    $html .= '<div id="additional_forms_'
                        . strtolower($form->getAdditionalName()) . '" class="additional-form">';
                    $html .= '<h4>' . $form->getAdditionalName() . '</h4><br>';
                    $html .= '<div class="saved-result"></div>';
                }

                $html .= '<form action="' . $action . '" method="' . $form->getMethod()
                    . '" ' . $form->getClass() . '  ' . $form->getMultipart()
                    . ' id="' . $form->getFormId() . '_counter_' . $counter . '">';

                /** @var Element[] $elements */
                $elements = $form->getElements();
                $html .= '<div class="row m-t-1"><div class="col-xs-12">';

                foreach ($elements as $element) {
                    $element->setAttribute('disabled', 'disabled');
                    $hiddenClass = $element instanceof Element\Hidden ? 'hidden' : '';
                    $html .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $hiddenClass . '">';
                    $html .= '<div class="form-group">';
                    if ($element->getName() === $this->getImageField()) {
                        $config = $this->getDI()->get(Services::CONFIG);
                        $uploadsDir = $config->hostName;
                        $entity = $form->getEntity();
                        if ($entity instanceof Model) {
                            if ($form->getImageRelatedField()) {
                                $method = 'get' . ucfirst($form->getImageRelatedField());
                                if (method_exists($entity, $method)) {
                                    $imageId = (int) $entity->$method();
                                    if ($imageId) {
                                        $image = Images::findFirst($imageId);
                                        if ($image instanceof Images) {
                                            $url = $uploadsDir . $image->getPath() . $image->getFileName();
                                            $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                            $html .= '<br>';
                                            if (!$this->isShow()) {
                                                $html .= $this->renderDecorated($element);
                                            }
                                        } else {
                                            $html .= $this->renderDecorated($element);
                                        }
                                    } else {
                                        $html .= $this->renderDecorated($element);
                                    }
                                } else {
                                    $html .= $this->renderDecorated($element);
                                }
                            } elseif ($form->isImage && $entity instanceof Images && $entity->getId()) {
                                $url = $uploadsDir . $entity->getPath() . $entity->getFileName();
                                $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                $html .= '<br>';
                                if (!$this->isShow()) {
                                    $html .= $this->renderDecorated($element);
                                }
                            } else {
                                $html .= $this->renderDecorated($element);
                            }
                        } else {
                            $html .= $this->renderDecorated($element);
                        }
                    } else {
                        $html .= $this->renderDecorated($element);
                    }

                    $html .= '</div>';
                    $html .= '</div>';
                }

                $html .= '</div>';
                $html .= '</div>';

                $html .= '</form>';
            }
            $html .= '</div>';
            $html .= '<hr class="fwb" />';
            $html .= '</div>';
        }

        $this->html = $html;
    }

    /**
     * @throws \ReflectionException
     */
    public function renderComplexEditForm()
    {
        $action = $this->getAction() ?: '';
        $html = '<div>';
        $html .= '<form action="' . $action . '" method="' . $this->getMethod()
            . '" ' . $this->getClass() . '  ' . $this->getMultipart() . ' id="' . $this->formId . '">';

        /** @var Element[] $elements */
        $elements = $this->getElements();
        $html .= '<div class="row m-t-1"><div class="col-xs-12">';

        foreach ($elements as $element) {
            $hiddenClass = $element instanceof Element\Hidden ? 'hidden' : '';
            $html .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $hiddenClass . '">';
            $html .= '<div class="form-group">';
            if ($element->getName() === $this->getImageField()) {
                $config = $this->getDI()->get(Services::CONFIG);
                $uploadsDir = $config->hostName;
                $entity = $this->getEntity();
                if ($entity instanceof Model) {
                    if ($this->getImageRelatedField()) {
                        $method = 'get' . ucfirst($this->getImageRelatedField());
                        if (method_exists($entity, $method)) {
                            $imageId = (int) $entity->$method();
                            if ($imageId) {
                                $image = Images::findFirst($imageId);
                                if ($image instanceof Images) {
                                    $url = $uploadsDir . $image->getPath() . $image->getFileName();
                                    $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                    if (!$this->isShow()) {
                                        $html .= $this->renderDecorated($element);
                                    }
                                } else {
                                    $html .= $this->renderDecorated($element);
                                }
                            } else {
                                $html .= $this->renderDecorated($element);
                            }
                        } else {
                            $html .= $this->renderDecorated($element);
                        }
                    } elseif ($this->isImage && $entity instanceof Images && $entity->getId()) {
                        $url = $uploadsDir . $entity->getPath() . $entity->getFileName();
                        $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                        if (!$this->isShow()) {
                            $html .= $this->renderDecorated($element);
                        }
                    } else {
                        $html .= $this->renderDecorated($element);
                    }
                } else {
                    $html .= $this->renderDecorated($element);
                }
            } else {
                $html .= $this->renderDecorated($element);
            }

            $html .= '</div>';
            $html .= '</div>';
            $hiddenClass = null;
            unset($hiddenClass);
        }

        $html .= '</div>';
        $html .= '</div>';
        $html .= '<hr>';
        $html .=
            '<button type="submit" id="submit_button" class="btn btn-orange" style="margin-bottom: 50px">Submit</button>';

        $html .= '</form>';
        $html .= '</div>';

        $html .= '<hr class="fwb" />';

        $previousForm = null;
        $currentForm = null;
        $oldForm = null;


        if (\count($this->additionalForms) > 0) {
            $html .= '<div id="additional_forms" class="additional-forms">';
            $html .= '<h4>' . $this->getAdditionalName() . '</h4><br>';
            $html .= '<div class="saved-result"></div>';
            foreach ($this->additionalForms as $form) {
                if (!($form instanceof \Phalcon\Forms\Form)) {
                    continue;
                }
                /** @var  $form BaseForm */
                $name = (new ReflectionClass($form))->getShortName();
                $counter = $form->getCnt();

                $newFormType = $previousForm !== $name;
                $startForms = $previousForm;
                $previousForm = $name;


                $action = $form->getAction() ?: '';

                if ($newFormType) {
                    if ($startForms) {
                        /** @var BaseForm $oldForm */
                        $hidden = $oldForm->isCollection() ? '' : 'hidden jd-hidden-button';

                        $html .= '<div id="additional_forms_plus_'
                            . $oldForm->getAdditionalName()
                            . '" class="additional-form-plus glyphicon glyphicon-plus-sign ' . $hidden
                            . '"></div> <span class="jd-slash ' . $hidden . '">/</span> ';
                        $html .= '<div id="additional_forms_minus_'
                            . $oldForm->getAdditionalName()
                            . '" class="additional-form-minus glyphicon glyphicon-minus-sign ' . $hidden . '"></div>';
                        $html .= '<hr class="fwb" />';
                        $html .= '</div>';
                    }


                    $html .= '<div id="additional_forms_'
                        . strtolower($form->getAdditionalName()) . '" class="additional-form">';
                    $html .= '<h4>' . $form->getAdditionalName() . '</h4><br>';
                    $html .= '<div class="saved-result"></div>';
                }


                $html .= '<div>';
                $html .= '<form action="' . $action . '" method="' . $form->getMethod()
                    . '" ' . $form->getClass() . '  ' . $form->getMultipart()
                    . ' id="' . $form->getFormId() . '_counter_' . $counter . '">';

                /** @var Element[] $elements */
                $elements = $form->getElements();
                $html .= '<div class="row m-t-1"><div class="col-xs-12">';

                foreach ($elements as $element) {
                    $hiddenClass = $element instanceof Element\Hidden ? 'hidden' : '';
                    $html .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 ' . $hiddenClass . '">';
                    $html .= '<div class="form-group">';
                    if ($element->getName() === $this->getImageField()) {
                        $config = $this->getDI()->get(Services::CONFIG);
                        $uploadsDir = $config->hostName;
                        $entity = $form->getEntity();
                        if ($entity instanceof Model) {
                            if ($form->getImageRelatedField()) {
                                $method = 'get' . ucfirst($form->getImageRelatedField());
                                if (method_exists($entity, $method)) {
                                    $imageId = (int) $entity->$method();
                                    if ($imageId) {
                                        $image = Images::findFirst($imageId);
                                        if ($image instanceof Images) {
                                            $url = $uploadsDir . $image->getPath() . $image->getFileName();
                                            $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                            $html .= '<br>';
                                            if (!$this->isShow()) {
                                                $html .= $this->renderDecorated($element);
                                            }
                                        } else {
                                            $html .= $this->renderDecorated($element);
                                        }
                                    } else {
                                        $html .= $this->renderDecorated($element);
                                    }
                                } else {
                                    $html .= $this->renderDecorated($element);
                                }
                            } elseif ($form->isImage && $entity instanceof Images && $entity->getId()) {
                                $url = $uploadsDir . $entity->getPath() . $entity->getFileName();
                                $html .= '<div style="width: 100%;"><img style="width: 130px;" src="' . $url . '"></div>';
                                $html .= '<br>';
                                if (!$this->isShow()) {
                                    $html .= $this->renderDecorated($element);
                                }
                            } else {
                                $html .= $this->renderDecorated($element);
                            }
                        } else {
                            $html .= $this->renderDecorated($element);
                        }
                    } else {
                        $html .= $this->renderDecorated($element);
                    }

                    $html .= '</div>';
                    $html .= '</div>';
                    $hiddenClass = null;
                    unset($hiddenClass);
                }

                $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="red-color pull-right pointer glyphicon glyphicon-remove hidden"></div>';
                $html .= '<hr>';

                $html .=
                    '<button type="submit" id="submit_'
                    . $name . '_button_' . $this->cnt
                    . '" class="btn btn-orange" style="margin-bottom: 50px">Submit ' . '</button>';

                $html .= '</form>';
                $html .= '</div>';


                $oldForm = $form;
            }
            /** @var BaseForm $oldForm */
            $hidden = $oldForm->isCollection() ? '' : 'hidden jd-hidden-button';

            $html .= '<div id="additional_forms_plus_'
                . $oldForm->getAdditionalName()
                . '" class="additional-form-plus glyphicon glyphicon-plus-sign ' . $hidden
                . '"></div> <span class="jd-slash ' . $hidden . '">/</span> ';
            $html .= '<div id="additional_forms_minus_'
                . $oldForm->getAdditionalName()
                . '" class="additional-form-minus glyphicon glyphicon-minus-sign ' . $hidden . '"></div>';


            $html .= '<hr class="fwb" />';
            $html .= '</div>';
            $html .= '</div>';
        }

        $this->html = $html;
    }

    /**
     * @return string
     */
    protected function getMultipart()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * @param mixed $formId
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;
    }

    /**
     * @return array
     */
    public function getAdditionalElements()
    {
        return $this->additionalElements;
    }

    /**
     * @param \Phalcon\Forms\Form $form
     *
     */
    public function setAdditionalElements(\Phalcon\Forms\Form $form)
    {
        $additionalElements = $form->getElements();
        $this->additionalElements = array_merge($this->additionalElements, $additionalElements);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAdditionalName()
    {
        return $this->additionalName;
    }

    /**
     * @param mixed $additionalName
     */
    public function setAdditionalName($additionalName)
    {
        $this->additionalName = $additionalName;
    }

    /**
     * @return array
     */
    public function getAdditionalForms(): array
    {
        return $this->additionalForms;
    }

    /**
     * @param array $additionalForms
     */
    public function setAdditionalForms(array $additionalForms)
    {
        $this->additionalForms = $additionalForms;
    }

    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return $this->collection;
    }

    /**
     * @param bool $collection
     */
    public function setCollection(bool $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return bool
     */
    public function isShowImage(): bool
    {
        return $this->showImage;
    }

    /**
     * @param bool $showImage
     */
    public function setShowImage(bool $showImage)
    {
        $this->showImage = $showImage;
    }
}
