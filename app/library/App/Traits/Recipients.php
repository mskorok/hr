<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 20.04.18
 * Time: 12:49
 */

namespace App\Traits;

use App\Constants\AclRoles;
use App\Model\Users;
use App\Resources\UsersResource;
use App\Transformers\UsersTransformer;
use App\User\Service;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\Model\Query\Builder;
use PhalconRest\QueryParsers\PhqlQueryParser;

/**
 * Trait Recipients
 * @package App\Traits
 */
trait Recipients
{
    /**
     * @throws \PhalconApi\Exception
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getUsers()
    {
        /** @var Service $userService */
        $userService = $this->userService;
        $role = $userService->getRole();
        $users = [];
        if ($role === AclRoles::ADMIN || $role === AclRoles::SUPERADMIN) {
            $users = $this->_getAdminRecipients();
        } elseif ($role === AclRoles::MANAGER || $role === AclRoles::EMPLOYER) {
            $users = $this->_getEmployerRecipients();
        } elseif ($role === AclRoles::APPLICANT) {
            $users = $this->_getApplicantRecipients();
        }
        return $this->createCollectionResponse($users, new UsersTransformer(), 'users');
    }

    /**
     * @return mixed
     */
    protected function _getAdminRecipients()
    {
        $resource = new UsersResource('/users');
        /** @var PhqlQueryParser $parser */
        $parser = $this->phqlQueryParser;
        /** @var Builder $query */
        $query = $parser->fromQuery($this->query, $resource);
        $this->addLimit($query);
        return $query->getQuery()->execute();
    }

    /**
     * @return mixed
     */
    protected function _getEmployers()
    {
        $resource = new UsersResource('/users');
        /** @var PhqlQueryParser $parser */
        $parser = $this->phqlQueryParser;
        /** @var Builder $query */
        $query = $parser->fromQuery($this->query, $resource);
        $query->andWhere('[' . Users::class . '].[role]  = :role:', ['role' => AclRoles::EMPLOYER]);
        $query->orWhere('[' . Users::class . '].[role1]  = :role1:', ['role1' => AclRoles::MANAGER]);
        $this->addLimit($query);
        return $query->getQuery()->execute();
    }


    /**
     * @return mixed
     */
    protected function _getApplicant()
    {
        $resource = new UsersResource('/users');
        /** @var PhqlQueryParser $parser */
        $parser = $this->phqlQueryParser;
        /** @var Builder $query */
        $query = $parser->fromQuery($this->query, $resource);
        $query->andWhere('[' . Users::class . '].[role]  = :role:', ['role' => AclRoles::APPLICANT]);
        $this->addLimit($query);
        return $query->getQuery()->execute();
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _getEmployerRecipients(): array
    {
        $admin = $this->getAdminUser();
        $resource = new UsersResource('/users');
        /** @var PhqlQueryParser $parser */
        $parser = $this->phqlQueryParser;
        $query = $parser->fromQuery($this->query, $resource);
        $query = $this->_getEmployerQuery($query);
        /** @var Simple $employers */
        $employers = $this->modifyEmployerByUser($query);

        $users = [$admin];
        foreach ($employers as $employer) {
            $users[] = $employer;
        }
        return array_unique($users);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _getApplicantRecipients(): array
    {
        $admin = $this->getAdminUser();
        $resource = new UsersResource('/users');
        /** @var PhqlQueryParser $parser */
        $parser = $this->phqlQueryParser;
        $query = $parser->fromQuery($this->query, $resource);
        $this->addLimit($query);
        /** @var Simple $applicants */
        $query = $this->_getApplicantQuery($query);
        $applicants = $this->modifyApplicantByUser($query);

        $users = [$admin];
        foreach ($applicants as $applicant) {
            $users[] = $applicant;
        }
        return array_unique($users);
    }

    /**
     * @param $id
     * @return bool
     */
    protected function isEmployer($id): bool
    {
        $resource = new UsersResource('/users');
        /** @var PhqlQueryParser $parser */
        $parser = $this->phqlQueryParser;
        /** @var Builder $query */
        $query = $parser->fromQuery($this->query, $resource);
        $query->andWhere('[' . Users::class . '].[id]  = :id:', ['id' => (int) $id]);
        $query->andWhere('[' . Users::class . '].[role]  = :role:', ['role' => AclRoles::MANAGER]);
        $this->addLimit($query);
        /** @var Simple $users */
        $users = $query->getQuery()->execute();
        $query1 = $parser->fromQuery($this->query, $resource);
        $query1->andWhere('[' . Users::class . '].[id]  = :id:', ['id' => (int) $id]);
        $query1->andWhere('[' . Users::class . '].[role]  = :role:', ['role' => AclRoles::EMPLOYER]);
        $this->addLimit($query1);
        /** @var Simple $users1 */
        $users1 = $query1->getQuery()->execute();
        return $users->count() > 0 || $users1->count() > 0;
    }


    /**
     * @param $id
     * @return bool
     */
    protected function isApplicant($id): bool
    {
        $resource = new UsersResource('/users');
        /** @var PhqlQueryParser $parser */
        $parser = $this->phqlQueryParser;
        /** @var Builder $query */
        $query = $parser->fromQuery($this->query, $resource);
        $query->andWhere('[' . Users::class . '].[id]  = :id:', ['id' => (int) $id]);
        $query->andWhere('[' . Users::class . '].[role]  = :role:', ['role' => AclRoles::APPLICANT]);
        $this->addLimit($query);
        /** @var Simple $users */
        $users = $query->getQuery()->execute();
        return $users->count() > 0;
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    protected function modifyEmployerByUser(Builder $query)
    {
        return $query->getQuery()->execute();
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    protected function modifyApplicantByUser(Builder $query)
    {
        return $query->getQuery()->execute();
    }
}
