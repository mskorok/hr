<?php
declare(strict_types=1);

namespace App\Resources;

use App\Controllers\UsersController;
use App\Transformers\UsersTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiResource;
use App\Api\Endpoint as ApiEndpoint;
use App\Model\Users;
use App\Constants\AclRoles;

/**
 * Class UsersResource
 * @package App\Resources
 */
class UsersResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Users')
            ->model(Users::class)
            ->expectsJsonData()
            ->transformer(UsersTransformer::class)
            ->handler(UsersController::class)
            ->itemKey('user')
            ->collectionKey('users')
//            ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
//            ->deny(AclRoles::UNAUTHORIZED)
            ->allow(AclRoles::UNAUTHORIZED)

            ->endpoint(
                ApiEndpoint::all()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
                    ->description('Returns all registered users')
            )
            ->endpoint(
                ApiEndpoint::create()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
                    ->postedDataMethod(PostedDataMethods::POST)
            )
            ->endpoint(
                ApiEndpoint::find()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::EMPLOYER, AclRoles::MANAGER])
            )
            ->endpoint(
                ApiEndpoint::update()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
                    ->postedDataMethod(PostedDataMethods::POST)
            )
            ->endpoint(
                ApiEndpoint::remove()
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
            )
            ->endpoint(
                ApiEndpoint::get('/me', 'me')
                    ->allow([AclRoles::AUTHORIZED])
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::post('/authenticate', 'authenticate')
                    ->allow(AclRoles::UNAUTHORIZED)
                    ->deny(AclRoles::AUTHORIZED)
                    ->description(
                        'Authenticates user credentials provided in the
                         authorization header and returns an access token'
                    )
                    ->exampleResponse(
                        ''
                    )
            )
//            ->endpoint(
//                ApiEndpoint::get('/logout', 'logout')
//                    ->allow(AclRoles::UNAUTHORIZED)
//                    //->allow(AclRoles::AUTHORIZED)
//                    ->description('Logout authenticated user')
//            )
            ->endpoint(
                ApiEndpoint::post('/password', 'newPassword')
                    ->allow(AclRoles::AUTHORIZED)
                    ->description('Recovery new pass')
            )->endpoint(
                ApiEndpoint::get('/password', 'newPassword')
                    ->allow(AclRoles::AUTHORIZED)
                    ->description('Recovery new pass')
            )->endpoint(
                ApiEndpoint::post('/recovery', 'recovery')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Recovery mail')
            )->endpoint(
                ApiEndpoint::post('/confirm/email', 'confirm')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by email')
            )->endpoint(
                ApiEndpoint::get('/confirm/email', 'confirm')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by email')
            )->endpoint(
                ApiEndpoint::get('/index', 'indexAction')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::MANAGER])
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by admin')
            )
            ->endpoint(
                ApiEndpoint::get('/search', 'searchAction')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::MANAGER])
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by admin')
            )
            ->endpoint(
                ApiEndpoint::get('/new', 'newAction')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by admin')
            )
            ->endpoint(
                ApiEndpoint::get('/edit', 'editAction')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by admin')
            )
            ->endpoint(
                ApiEndpoint::get('/create', 'createAction')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by admin')
            )
            ->endpoint(
                ApiEndpoint::get('/save', 'saveAction')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN, AclRoles::APPLICANT])
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by admin')
            )
            ->endpoint(
                ApiEndpoint::get('/delete', 'deleteAction')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN])
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Confirm user by admin')
            )
            ->endpoint(
                ApiEndpoint::get('/subscriptions', 'getSubscriptions')
                    ->allow(AclRoles::AUTHORIZED)
                    ->description('get subscriptions')
            )

            ->endpoint(
                ApiEndpoint::get('/company/subscriptions', 'getCompanySubscriptions')
                    ->allow(AclRoles::AUTHORIZED)
                    ->description('')
            )
            ->endpoint(
                ApiEndpoint::get('/subscribe/user/{sid:[0-9]+}', 'subscribeUser')
                    ->allow(AclRoles::AUTHORIZED)
                    ->description('')
            )
            ->endpoint(
                ApiEndpoint::get('/unsubscribe/user/{sid:[0-9]+}', 'unsubscribeUser')
                    ->allow(AclRoles::AUTHORIZED)
                    ->description('')
            )
            ->endpoint(
                ApiEndpoint::get('/subscribe/company/{cid:[0-9]+}/{sid:[0-9]+}', 'subscribeCompany')
                    ->allow(AclRoles::AUTHORIZED)
                    ->description('')
            )
            ->endpoint(
                ApiEndpoint::get('/unsubscribe/company/{cid:[0-9]+}/{sid:[0-9]+}', 'unsubscribeCompany')
                    ->allow(AclRoles::AUTHORIZED)
                    ->description('')
            )
            /*
            ->endpoint(
                ApiEndpoint::get('/has/autoresponder/{id:[0-9]+}', 'hasAutoresponder')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Check autoresponder')
            )
            ->endpoint(
                ApiEndpoint::post('/supplier/code', 'supplierCode')
                    ->allow(AclRoles::UNAUTHORIZED)
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Set supplier code for company')
            )

            ->endpoint(
                ApiEndpoint::get('/search', 'search')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN])
                    //->allow(AclRoles::AUTHORIZED)
                    ->description('Search through fields of related models')
            )
            ->endpoint(
                ApiEndpoint::post('/search', 'search')
                    ->allow([AclRoles::ADMIN, AclRoles::SUPERADMIN])
                    ->allow(AclRoles::UNAUTHORIZED)
                    ->description('Search through fields of related models')
            )
            */
        ;
    }
}
