<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25.09.17
 * Time: 8:52
 */

namespace App\Forms;

use App\Model\Resumes;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;

/**
 * Phalcon\Forms\Form
 *
 * This component allows to build forms using an object-oriented interface
 */
class ResumesForm extends BaseForm
{
    private static $counter = 0;

    /**
     * ResumesForm constructor.
     * @param null $entity
     * @param null $userOptions
     */
    public function __construct($entity = null, $userOptions = null)
    {
        $this->cnt = ++static::$counter;
        parent::__construct($entity, $userOptions);
        $this->formId = 'resume_form';
    }

    /**
     * @param Resumes $model
     * @param array|null $options
     */
    public function initialize(Resumes $model = null, array $options = null): void
    {
        if (isset($options['cnt'])) {
            $this->cnt = $options['cnt'];
        }
        if (isset($options['admin'])) {
            $this->admin = (bool) $options['admin'];
        }

        $this->add(
            new Hidden('id', ['id' => 'id_model_Resumes_counter_' . $this->cnt])
        );

        $position = new Text('position', [
            'class'   => 'form-control',
            'placeholder' => 'Position',
            'id' => 'position_model_Resumes_counter_' . $this->cnt
        ]);
        $position->setLabel('Position');
        $this->add($position);


        $professional_area = new Text('professional_area', [
            'class'   => 'form-control',
            'placeholder' => 'Professional area',
            'id' => 'professional_area_model_Resumes_counter_' . $this->cnt
        ]);
        $professional_area->setLabel('Professional area');
        $this->add($professional_area);

        $location = new Text('location', [
            'class'   => 'form-control',
            'placeholder' => 'Location',
            'id' => 'location_model_Resumes_counter_' . $this->cnt
        ]);
        $location->setLabel('Location');
        $this->add($location);

        $salary = new Numeric('salary', [
            'class'   => 'form-control',
            'placeholder' => 'Salary',
            'id' => 'salary_model_Resumes_counter_' . $this->cnt
        ]);
        $salary->setLabel('Salary');
        $this->add($salary);

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
                'id' => 'type_of_job_model_Resumes_counter_' . $this->cnt
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
                'id' => 'work_place_model_Resumes_counter_' . $this->cnt
            ]
        );
        $work_place->setLabel('Work Place');
        $work_place->setAttribute('class', 'form-control');
        $this->add($work_place);


        $image = new File('fileName');
        $image->setLabel('CV');
        $image->setAttribute('class', '');
        $image->setAttribute('id', 'cv_model_Resumes_counter_' . $this->cnt);
        $this->add($image);




        $key_skills = new Text('key_skills', [
            'class'   => 'form-control',
            'placeholder' => 'Key skills',
            'id' => 'key_skills_model_Resumes_counter_' . $this->cnt
        ]);
        $key_skills->setLabel('Key skills');
        $this->add($key_skills);

        $language = new Text('language', [
            'class'   => 'form-control',
            'placeholder' => 'Language',
            'id' => 'language_model_Resumes_counter_' . $this->cnt
        ]);
        $language->setLabel('Language');
        $this->add($language);

        $about_me = new Text('about_me', [
            'class'   => 'form-control',
            'placeholder' => 'About me',
            'id' => 'about_me_model_Resumes_counter_' . $this->cnt
        ]);
        $about_me->setLabel('About me');
        $this->add($about_me);

        $certification = new Text('certification', [
            'class'   => 'form-control',
            'placeholder' => 'Certification',
            'id' => 'certification_model_Resumes_counter_' . $this->cnt
        ]);
        $certification->setLabel('Certification');
        $this->add($certification);
    }
}
