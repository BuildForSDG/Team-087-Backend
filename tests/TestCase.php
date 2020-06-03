<?php

use App\Patient;
use App\Specialist;
use App\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected $apiV1 = '/api/v1';

    protected $apiV1UsersUrl;
    protected $apiV1SignInUrl;
    protected $apiV1SignOutUrl;
    protected $userWithAuthorization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=UsersTableSeeder');
        $this->withoutEvents();

        $this->apiV1SignInUrl = $this->apiV1 . '/auth/signin';
        $this->apiV1SignOutUrl = $this->apiV1 . '/auth/signout';

        $this->apiV1UsersUrl = $this->apiV1 . '/users';
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    protected function get_user_with_authorization($condition = [])
    {
        $user = factory(User::class)->create(array_merge(['is_active' => true, 'is_guest' => false], $condition));

        if ($user->is_patient || $user->is_specialist) {
            factory($user->is_patient ? Patient::class : Specialist::class)->create(['user_id' => $user->id]);
        }

        return ['user' => $user];
    }
}
