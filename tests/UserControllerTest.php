<?php

use App\User;

/**
 * User Controller Test
 *
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 */
class UserControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testUserCanLoadHomePage()
    {
        $this->get('/')->assertResponseOk();
    }

    public function testUserCannotViewAllRegisteredUsersListIfNotAuthenticated()
    {
        $this->get('/')->assertResponseOk();

        $this->get($this->apiV1UsersUrl);
        $this->seeStatusCode(401)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    public function testUserCannotViewAllRegisteredUsersListIfNotAnAdministrator()
    {
        $this->get('/')->assertResponseOk();

        $this->actingAs(factory(User::class)->create(['is_specialist' => true, 'is_admin' => false]))->get($this->apiV1UsersUrl);
        $this->seeStatusCode(401)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    public function testUserCanViewAllRegisteredUsersListOnlyIfAnAdministrator()
    {
        $this->get('/')->assertResponseOk();

        $this->actingAs(factory(User::class)->create(['is_specialist' => false, 'is_admin' => true]))->get($this->apiV1UsersUrl);
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeJsonStructure([
            'data' => ['data' => [['id', 'last_name', 'first_name', 'created_at']]]
        ])->seeJsonDoesntContains(['errors']);
    }

    public function testUsersListCanBeFilteredToRetrieveOnlySpecialistsWithPagination()
    {
        $this->get('/')->assertResponseOk();

        $this->actingAs(factory(User::class)->create(['is_specialist' => false, 'is_admin' => true]))->get("{$this->apiV1UsersUrl}?specialist=1");
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeJsonStructure([
            'data' => [
                'data' => [['id', 'last_name', 'first_name', 'created_at', 'specialist' => ['license_no', 'licensed_at']]],
                'current_page', 'per_page', 'total'
            ],
        ])->seeJsonDoesntContains(['errors']);
    }
}
