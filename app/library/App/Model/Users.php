<?php
declare(strict_types=1);

namespace App\Model;

use App\Constants\Services;
use League\Fractal\Resource\Collection;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

/**
 * Users
 *
 * @package App\Model
 * @autogenerated by Phalcon Developer Tools
 * @date 2019-01-13, 13:47:18
 * @method Collection getApplied
 * @method Collection getComments
 * @method Collection getCompanyManager
 * @method Collection getEducation
 * @method ExpertInfo getExpertInfo
 * @method Favorites getFavorites
 * @method Simple getFavoriteVacancies
 * @method Collection getFavoriteResume
 * @method Collection getFavoriteResumes
 * @method Simple getInvitations
 * @method Collection getInvited
 * @method Collection getProfessionalExperiences
 * @method Collection getRecipients
 * @method Collection getResumes
 * @method Collection getSenders
 * @method Collection getUserSubscription
 * @method Countries getCountries
 * @method Teachers getTeachers
 * @method Images getImages
 * @method Simple getCompanies
 * @method Collection getPayments
 * @method Simple getSubscriptions
 * @method Simple getAppliedVacancies
 */
class Users extends DateTrackingModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $surname;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $username;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $birthday;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $gender;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $about_me;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $github;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $linkedIn;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $fb;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $hh;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $email;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    protected $emailConfirmed;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $skype;

    /**
     *
     * @var string
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $country;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     *
     * @var int
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $avatar;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $token;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $language;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $role;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $lastLoginDate;

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
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field surname
     *
     * @param string $surname
     * @return $this
     */
    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Method to set the value of field username
     *
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Method to set the value of field password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Method to set the value of field birthday
     *
     * @param string $birthday
     * @return $this
     */
    public function setBirthday(string $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Method to set the value of field gender
     *
     * @param string $gender
     * @return $this
     */
    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Method to set the value of field github
     *
     * @param string $github
     * @return $this
     */
    public function setGithub(string $github): self
    {
        $this->github = $github;

        return $this;
    }

    /**
     * Method to set the value of field linkedIn
     *
     * @param string $linkedIn
     * @return $this
     */
    public function setLinkedIn(string $linkedIn): self
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }

    /**
     * Method to set the value of field fb
     *
     * @param string $fb
     * @return $this
     */
    public function setFb(string $fb): self
    {
        $this->fb = $fb;

        return $this;
    }

    /**
     * Method to set the value of field hh
     *
     * @param string $hh
     * @return $this
     */
    public function setHh(string $hh): self
    {
        $this->hh = $hh;

        return $this;
    }

    /**
     * Method to set the value of field phone
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field emailConfirmed
     *
     * @param integer $emailConfirmed
     * @return $this
     */
    public function setEmailConfirmed(int $emailConfirmed): self
    {
        $this->emailConfirmed = $emailConfirmed;

        return $this;
    }

    /**
     * Method to set the value of field skype
     *
     * @param string $skype
     * @return $this
     */
    public function setSkype(string $skype): self
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Method to set the value of field country
     *
     * @param string $country
     * @return $this
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Method to set the value of field city
     *
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
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
     * Method to set the value of field address
     *
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Method to set the value of field token
     *
     * @param string $token
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Method to set the value of field language
     *
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Method to set the value of field status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Method to set the value of field role
     *
     * @param string $role
     * @return $this
     */
    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Method to set the value of field lastLoginDate
     *
     * @param string $lastLoginDate
     * @return $this
     */
    public function setLastLoginDate(string $lastLoginDate): self
    {
        $this->lastLoginDate = $lastLoginDate;

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
     * Returns the value of field name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Returns the value of field surname
     *
     * @return string
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Returns the value of field username
     *
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Returns the value of field password
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Returns the value of field birthday
     *
     * @return string
     */
    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    /**
     * Returns the value of field gender
     *
     * @return string
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * Returns the value of field github
     *
     * @return string
     */
    public function getGithub(): ?string
    {
        return $this->github;
    }

    /**
     * Returns the value of field linkedIn
     *
     * @return string
     */
    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    /**
     * Returns the value of field fb
     *
     * @return string
     */
    public function getFb(): ?string
    {
        return $this->fb;
    }

    /**
     * Returns the value of field hh
     *
     * @return string
     */
    public function getHh(): ?string
    {
        return $this->hh;
    }

    /**
     * Returns the value of field phone
     *
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Returns the value of field emailConfirmed
     *
     * @return integer
     */
    public function getEmailConfirmed(): ?int
    {
        return (int)$this->emailConfirmed;
    }

    /**
     * Returns the value of field skype
     *
     * @return string
     */
    public function getSkype(): ?string
    {
        return $this->skype;
    }

    /**
     * Returns the value of field country
     *
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Returns the value of field city
     *
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
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
     * Returns the value of field address
     *
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Returns the value of field token
     *
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Returns the value of field language
     *
     * @return string
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Returns the value of field status
     *
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Returns the value of field role
     *
     * @return string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Returns the value of field lastLoginDate
     *
     * @return string
     */
    public function getLastLoginDate(): ?string
    {
        return $this->lastLoginDate;
    }

    /**
     * @return string
     */
    public function getAboutMe(): ?string
    {
        return $this->about_me;
    }

    /**
     * @param string $about_me
     */
    public function setAboutMe(string $about_me): void
    {
        $this->about_me = $about_me;
    }

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation(): bool
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model' => $this,
                    'message' => 'Please enter a correct email address'
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize(): void
    {
        $this->setSchema($this->getDI()->get(Services::CONFIG)->database->dbname);
        $this->setSource('users');
        $this->hasMany('id', Applied::class, 'user_id', ['alias' => 'Applied']);
        $this->hasMany('id', Comments::class, 'user_id', ['alias' => 'Comments']);
        $this->hasMany('id', CompanyManager::class, 'user_id', ['alias' => 'CompanyManager']);
        $this->hasMany('id', Education::class, 'user_id', ['alias' => 'Education']);
        $this->hasOne('id', ExpertInfo::class, 'user_id', ['alias' => 'ExpertInfo']);
        $this->hasMany('id', Favorites::class, 'user_id', ['alias' => 'Favorites']);
        $this->hasMany('id', FavoriteResume::class, 'user_id', ['alias' => 'FavoriteResume']);
        $this->hasMany('id', Invited::class, 'user_id', ['alias' => 'Invited']);
        $this->hasMany('id', Messages::class, 'recipient', ['alias' => 'Recipients']);
        $this->hasMany('id', Messages::class, 'sender', ['alias' => 'Senders']);
        $this->hasMany('id', Payments::class, 'user_id', ['alias' => 'Payments']);
        $this->hasMany('id', ProfessionalExperience::class, 'user_id', ['alias' => 'ProfessionalExperiences']);
        $this->hasMany('id', Resumes::class, 'user_id', ['alias' => 'Resumes']);
        $this->hasOne('id', Teachers::class, 'user_id', ['alias' => 'Teachers']);
        $this->hasMany('id', UserSubscription::class, 'user_id', ['alias' => 'UserSubscription']);
        $this->belongsTo('country', Countries::class, 'id', ['alias' => 'Countries']);
        $this->belongsTo('avatar', Images::class, 'id', ['alias' => 'Images']);
        $this->hasManyToMany(
            'id',
            Favorites::class,
            'user_id',
            'vacancy_id',
            Vacancies::class,
            'id',
            [
                'alias' => 'FavoriteVacancies'
            ]
        );
        $this->hasManyToMany(
            'id',
            FavoriteResume::class,
            'user_id',
            'resume_id',
            Resumes::class,
            'id',
            [
                'alias' => 'FavoriteResumes'
            ]
        );
        $this->hasManyToMany(
            'id',
            CompanyManager::class,
            'user_id',
            'company_id',
            Companies::class,
            'id',
            [
                'alias' => 'Companies'
            ]
        );
        $this->hasManyToMany(
            'id',
            UserSubscription::class,
            'user_id',
            'subscription_id',
            Subscriptions::class,
            'id',
            [
                'alias' => 'Subscriptions'
            ]
        );
        $this->hasManyToMany(
            'id',
            Invited::class,
            'user_id',
            'company_id',
            Companies::class,
            'id',
            [
                'alias' => 'Invitations'
            ]
        );
        $this->hasManyToMany(
            'id',
            Applied::class,
            'user_id',
            'vacancy_id',
            Vacancies::class,
            'id',
            [
                'alias' => 'AppliedVacancies'
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
        return 'users';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users[]|Users|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users|\Phalcon\Mvc\Model\ResultInterface
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
                'name' => 'name',
                'surname' => 'surname',
                'username' => 'username',
                'password' => 'password',
                'birthday' => 'birthday',
                'gender' => 'gender',
                'github' => 'github',
                'linkedIn' => 'linkedIn',
                'fb' => 'fb',
                'hh' => 'hh',
                'phone' => 'phone',
                'email' => 'email',
                'emailConfirmed' => 'emailConfirmed',
                'skype' => 'skype',
                'country' => 'country',
                'city' => 'city',
                'avatar' => 'avatar',
                'address' => 'address',
                'token' => 'token',
                'language' => 'language',
                'status' => 'status',
                'role' => 'role',
                'lastLoginDate' => 'lastLoginDate',
                'about_me' => 'about_me'
            ];
    }
}
