<?php

use App\Appointment;
use App\Specialist;
use App\User;

/**
 * Appointment Controller Test Suite
 * 
 */
class AppointmentControllerTest extends TestCase
{
    private $apiV1AppointmentsUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=PatientsTableSeeder --class=SpecialistsTableSeeder --class=AppointmentsTableSeeder');

        $this->apiV1AppointmentsUrl = $this->apiV1UsersUrl . '/:id/appointments';
        $this->userWithAuthorization = $this->get_user_with_authorization(['is_patient' => true]);
    }

    public function testUserCannotBookOrRegisterAppointmentIfNotAuthenticated()
    {
        $this->get('/')->assertResponseOk();

        $this->post(str_replace(':id', 0, $this->apiV1AppointmentsUrl), []);
        $this->seeStatusCode(401)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * @return void
     */
    public function testAppointmentCannotBeBookedByAPatientWithBlankPurpose()
    {
        $this->get('/')->assertResponseOk();

        $specialist = factory(User::class)->create(['is_specialist' => true])->specialist()->save(factory(Specialist::class)->make());
        $url = str_replace(':id', Specialist::first()->user_id, $this->apiV1AppointmentsUrl);
        $appointment = factory(Appointment::class)->make([
            'specialist_id' => $specialist->id, 'patient_id' => $this->userWithAuthorization['user']->id, 'purpose' => ''
        ])->toArray();

        $this->actingAs($this->userWithAuthorization['user'])->post($url, $appointment)->seeStatusCode(400);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }
}
