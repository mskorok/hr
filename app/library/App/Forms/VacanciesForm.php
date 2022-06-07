<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Constants\AclRoles;
use App\Model\Companies;
use App\Model\Users;
use App\Model\Vacancies;
use Exception;
use Phalcon\Filter;
use Phalcon\Forms\Element\Date;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Mvc\Model\Resultset;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class VacanciesForm extends BaseForm
{
    private static $counter = 0;

    protected $user;

    /**
     * VacanciesForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
        $this->formId = 'vacancy_form';
    }

    /**
     * @param Vacancies|null $model
     * @param array|null $options
     * @return void
     * @throws Exception
     */
    public function initialize(Vacancies $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }

        if (isset($options['user'])) {
            $this->user = $options['user'] && $options['user'] instanceof Users? $options['user'] : null;
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Vacancies_counter_' . $this->cnt])
        );

        if ($model instanceof Vacancies) {
            $company = $model->getCompanies();

            if ($company instanceof Companies) {
                $this->add(
                    new Hidden('company_id', ['id' => 'company_id_model_Vacancies_counter_' . $this->cnt, 'value' => $company->getId()])
                );
            } elseif ($company instanceof Resultset) {
                /** @var Companies $item */
                $item = $company->getFirst();

                if ($item instanceof Companies) {
                    $this->add(
                        new Hidden('company_id', ['id' => 'company_id_model_Vacancies_counter_' . $this->cnt, 'value' => $item->getId()])
                    );
                } else {
                    $this->add(
                        new Hidden('company_id', ['id' => 'company_id_model_Vacancies_counter_' . $this->cnt, 'value' => ''])
                    );
                }
            } else {
                $this->add(
                    new Hidden('company_id', ['id' => 'company_id_model_Vacancies_counter_' . $this->cnt, 'value' => ''])
                );
            }
        } elseif ($this->user instanceof Users){
            $role = $this->user->getRole();

            if (\in_array($role, [AclRoles::SUPERADMIN, AclRoles::ADMIN], true)) {
                $companies = Companies::find();
            } else {
                $companies = $this->user->getCompanies();
            }
            $options = [];

            /** @var Companies $company */
            foreach ($companies as $company) {
                $options[] = [
                    $company->getId() => $company->getName()
                ];
            }

            $companiesSelect =  new Select(
            'company_id',
            $options,
            [
                'id' => 'company_id_model_Vacancies_counter_' . $this->cnt
            ]
        );
        $companiesSelect->setLabel('Company');
        $companiesSelect->setAttribute('class', 'form-control');
        $this->add($companiesSelect);

        } else {
            $this->add(
                new Hidden('company_id', ['id' => 'company_id_model_Vacancies_counter_' . $this->cnt, 'value' => ''])
            );
        }

        $name = new Text('name', [
            'class'   => 'form-control',
            'placeholder' => 'Name',
            'id' => 'name_model_Vacancies_counter_' . $this->cnt
        ]);
        $name->setLabel('Name');
        $this->add($name);

        $salary = new Numeric('salary', [
            'class'   => 'form-control',
            'placeholder' => 'Salary',
            'id' => 'salary_model_Vacancies_counter_' . $this->cnt
        ]);
        $salary->setLabel('Salary');
        $this->add($salary);

        $professional_experience = new Text('professional_experience', [
            'class'   => 'form-control',
            'placeholder' => 'Professional experience',
            'id' => 'professional_experience_model_Vacancies_counter_' . $this->cnt
        ]);
        $professional_experience->setLabel('Professional experience');
        $this->add($professional_experience);

        $type_of_job =  new Select(
            'type_of_job',
            [
                'full' => 'Full',
                'part_time' => 'Part-time',
                'project' => 'Project',
                'contract' => 'Contract',
                'volunteer' => 'Volunteer'
            ],
            [
                'id' => 'type_of_job_model_Vacancies_counter_' . $this->cnt
            ]
        );
        $type_of_job->setLabel('Type of Job');
        $type_of_job->setAttribute('class', 'form-control');
        $this->add($type_of_job);

        $work_place =  new Select(
            'work_place',
            [
                'insite' => 'Insite',
                'remote' => 'Remote',
                'remote-partially' => 'Remote partially',
                'no-matter' => 'No matter'
            ],
            [
                'id' => 'work_place_model_Vacancies_counter_' . $this->cnt
            ]
        );
        $work_place->setLabel('Work Place');
        $work_place->setAttribute('class', 'form-control');
        $this->add($work_place);

        $description = new Text('description', [
            'class'   => 'form-control',
            'placeholder' => 'Description',
            'id' => 'description_model_Vacancies_counter_' . $this->cnt
        ]);
        $description->setLabel('Description');
        $this->add($description);

        $responsibilities = new Text('responsibilities', [
            'class'   => 'form-control',
            'placeholder' => 'Responsibilities',
            'id' => 'responsibilities_model_Vacancies_counter_' . $this->cnt
        ]);
        $responsibilities->setLabel('Responsibilities');
        $this->add($responsibilities);

        $main_requirements = new Text('main_requirements', [
            'class'   => 'form-control',
            'placeholder' => 'Main requirements',
            'id' => 'main_requirements_model_Vacancies_counter_' . $this->cnt
        ]);
        $main_requirements->setLabel('Main requirements');
        $this->add($main_requirements);


        $additional_requirements = new Text('additional_requirements', [
            'class'   => 'form-control',
            'placeholder' => 'Additional requirements',
            'id' => 'additional_requirements_model_Vacancies_counter_' . $this->cnt
        ]);
        $additional_requirements->setLabel('Additional requirements');
        $this->add($additional_requirements);

        $work_conditions = new Text('work_conditions', [
            'class'   => 'form-control',
            'placeholder' => 'Work conditions',
            'id' => 'work_conditions_model_Vacancies_counter_' . $this->cnt
        ]);
        $work_conditions->setLabel('Work conditions');
        $this->add($work_conditions);

        $key_skills = new Text('key_skills', [
            'class'   => 'form-control',
            'placeholder' => 'Key skills',
            'id' => 'key_skills_model_Vacancies_counter_' . $this->cnt
        ]);
        $key_skills->setLabel('Key skills');
        $this->add($key_skills);



        $location = new Text('location', [
            'class'   => 'form-control',
            'placeholder' => 'Location',
            'id' => 'location_model_Vacancies_counter_' . $this->cnt
        ]);
        $location->setLabel('Location');
        $this->add($location);


        $start = new Date('start', [
            'class'   => 'form-control',
            'placeholder' => 'Start',
            'id' => 'start_model_Vacancies_counter_' . $this->cnt
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
            'id' => 'finish_model_Vacancies_counter_' . $this->cnt
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
