<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Constants\Services;
use App\Model\ProfessionalExperience;
use App\Model\Users;
use App\User\Service;
use Phalcon\Filter;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class ProfessionExperienceForm extends BaseForm
{
    private static $counter = 0;

    /**
     * ProfessionalExperienceForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
    }

    /**
     * @param ProfessionalExperience|null $model
     * @param array|null $options
     * @throws \PhalconApi\Exception
     */
    public function initialize(ProfessionalExperience $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_ProfessionalExperience_counter_' . $this->cnt])
        );


        if ($model instanceof ProfessionalExperience) {
            $di = $model->getDI();
            /** @var Service $userService */
            $userService = $di->get(Services::USER_SERVICE);
            /** @var Users $user */
            $user = $userService->getDetails();


            if ($user instanceof Users) {
                $this->add(
                    new Hidden('user_id', ['id' => 'user_id_model_ProfessionalExperience_counter_' . $this->cnt, 'value' => $user->getId()])
                );
            }

            $this->add(
                new Hidden('user_id', ['id' => 'company_id_model_ProfessionalExperience_counter_' . $this->cnt, 'value' => ''])
            );
        } else {
            $this->add(
                new Hidden('user_id', ['id' => 'company_id_model_ProfessionalExperience_counter_' . $this->cnt, 'value' => ''])
            );
        }

        $title = new Text('title', [
            'class'   => 'form-control',
            'placeholder' => 'Title',
            'id' => 'title_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $title->setLabel('Title');
        $this->add($title);

        $description = new Text('description', [
            'class'   => 'form-control',
            'placeholder' => 'Description',
            'id' => 'description_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $description->setLabel('Description');
        $this->add($description);


        $organization = new Text('organization', [
            'class'   => 'form-control',
            'placeholder' => 'Organization',
            'id' => 'organization_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $organization->setLabel('Organization');
        $this->add($organization);


        $location = new Text('location', [
            'class'   => 'form-control',
            'placeholder' => 'Location',
            'id' => 'location_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $location->setLabel('Location');
        $this->add($location);


        $site = new Text('site', [
            'class'   => 'form-control',
            'placeholder' => 'Site',
            'id' => 'site_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $site->setLabel('Site');
        $this->add($site);


        $professional_area = new Text('professional_area', [
            'class'   => 'form-control',
            'placeholder' => 'Professional Area',
            'id' => 'professional_area_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $professional_area->setLabel('Professional Area');
        $this->add($professional_area);


        $position = new Text('position', [
            'class'   => 'form-control',
            'placeholder' => 'Position',
            'id' => 'position_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $position->setLabel('Position');
        $this->add($position);

        $start = new Date('start', [
            'class'   => 'form-control',
            'placeholder' => 'Start',
            'id' => 'start_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $start->setLabel('start');
        $start->setDefault((new \DateTime())->format('Y-m-d'));

        $filter = new Filter();
        $filter->add('date', function ($date) {
            return (new \DateTime($date))->format('Y-m-d');
        });

        if ($model && !empty($model->getStart())) {
            $start->setDefault((new \DateTime($model->getStart()))->format('Y-m-d'));

            $dateField = $filter->sanitize($model->getStart(), 'date');
            $model->setStart($dateField);
            $start->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
        }
        $this->add($start);


        $finish = new Date('finish', [
            'class'   => 'form-control',
            'placeholder' => 'Finish',
            'id' => 'finish_model_ProfessionalExperience_counter_' . $this->cnt
        ]);
        $finish->setLabel('Finish');
        $finish->setDefault((new \DateTime())->format('Y-m-d'));

        $filter = new Filter();
        $filter->add('date', function ($date) {
            return (new \DateTime($date))->format('Y-m-d');
        });

        if ($model && !empty($model->getFinish())) {
            $finish->setDefault((new \DateTime($model->getFinish()))->format('Y-m-d'));

            $dateField = $filter->sanitize($model->getFinish(), 'date');
            $model->setFinish($dateField);
            $finish->addValidator(new \Phalcon\Validation\Validator\Date())->addFilter('date');
        }
        $this->add($finish);
    }
}
