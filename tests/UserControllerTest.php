<?php

use App\Specialist;
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
        $this->seeStatusCode(403)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
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
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeJsonStructure(['data' => ['data' => [
            ['id', 'last_name', 'first_name', 'created_at', 'specialist' => ['license_no', 'licensed_at']]
        ], 'current_page', 'per_page', 'total']])->seeJsonDoesntContains(['errors']);
    }

    public function testUsersCanViewTheirPersonalProfile()
    {
        $this->get('/')->assertResponseOk();

        $user = factory(User::class)->create(['is_patient' => false, 'is_specialist' => true]);
        $this->actingAs($user)->get("{$this->apiV1UsersUrl}/me");
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeJsonStructure(['data' => ['id', 'last_name', 'created_at']])->seeJsonDoesntContains(['errors']);
    }

    public function testUserCannotViewAPatientProfileIfNotASpecialist()
    {
        $this->get('/')->assertResponseOk();

        $patient = factory(User::class)->create(['is_patient' => true, 'is_specialist' => false]);
        $anotherPatient = factory(User::class)->create(['is_patient' => true, 'is_specialist' => false]);

        $this->actingAs($patient)->get("{$this->apiV1UsersUrl}/{$anotherPatient->id}");
        $this->seeStatusCode(400)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    public function testUserCanViewASpecialistProfileWhetherOrNotASpecialist()
    {
        $this->get('/')->assertResponseOk();

        $patient = factory(User::class)->create(['is_patient' => true, 'is_specialist' => false]);
        $specialist = factory(User::class)->create(['is_patient' => false, 'is_specialist' => true]);

        $this->actingAs($patient)->get("{$this->apiV1UsersUrl}/{$specialist->id}");
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeJsonStructure(['data' => ['id', 'last_name', 'created_at']])->seeJsonDoesntContains(['errors']);
    }

    public function testUserCannotViewAdministrativeUserIfNotAFellowAdministrator()
    {
        $this->get('/')->assertResponseOk();

        $patient = factory(User::class)->create(['is_patient' => true, 'is_specialist' => false]);
        $administrator = factory(User::class)->create(['is_patient' => false, 'is_specialist' => false, 'is_admin' => true]);

        $this->actingAs($patient)->get("{$this->apiV1UsersUrl}/{$administrator->id}");
        $this->seeStatusCode(400)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    public function testUserCanFetchSpecialistsRecommendationsForConsultationInPages()
    {
        $this->get('/')->assertResponseOk();

        $patient = factory(User::class)->create(['is_patient' => true, 'is_specialist' => false]);
        factory(User::class)->create([
            'is_patient' => false, 'is_specialist' => true, 'is_active' => true
        ])->specialist()->save(factory(Specialist::class)->make());

        $this->actingAs($patient)->get("{$this->apiV1UsersUrl}/recommendations");
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeJsonStructure(['data' => ['data' => [
            ['id', 'last_name', 'created_at', 'specialist' => ['license_no', 'licensed_at']]
        ], 'current_page', 'per_page', 'total']])->seeJsonDoesntContains(['errors']);
    }
}
