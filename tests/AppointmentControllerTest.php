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

        $this->actingAs($this->userWithAuthorization['user'])->post($url, $appointment)->seeStatusCode(422);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    public function testAppointmentsForTheCurrentUserCanBeFetchedByThem()
    {
        $this->get('/')->assertResponseOk();

        $specialist = User::where(['is_specialist' => true])->firstOrNew();
        $appointment = factory(Appointment::class)->create(['specialist_id' => $specialist->id, 'patient_id' => $this->userWithAuthorization['user']->id]);

        $this->actingAs($this->userWithAuthorization['user'])->get(str_replace(':id/', '', $this->apiV1AppointmentsUrl));
        $this->seeJson(['status' => true])->seeInDatabase('appointments', [
            'id' => $appointment->id, 'purpose' => $appointment->purpose
        ])->seeJsonStructure(['data' => [['id', 'purpose', 'created_at']]])->seeJsonDoesntContains(['errors']);
    }

    public function testAppointmentsForASpecialistCanBeFetchedByAnotherUser()
    {
        $this->get('/')->assertResponseOk();

        $patient = User::where(['is_patient' => true])->firstOrNew();
        $specialist = User::where(['is_specialist' => true])->firstOrNew();
        $appointment = factory(Appointment::class)->create(['specialist_id' => $specialist->id, 'patient_id' => $patient->id]);

        $this->actingAs($this->userWithAuthorization['user'])->get(str_replace(':id', $specialist->id, $this->apiV1AppointmentsUrl));
        $this->seeJson(['status' => true])->seeInDatabase('appointments', [
            'id' => $appointment->id, 'purpose' => $appointment->purpose
        ])->seeJsonStructure(['data' => [['id', 'purpose', 'created_at']]])->seeJsonDoesntContains(['errors']);
    }

    public function testAppointmentsForAPatientCannotBeFetchedByAnotherPatient()
    {
        $this->get('/')->assertResponseOk();

        $patient = User::where(['is_patient' => true])->first();
        $specialist = User::where(['is_specialist' => true])->firstOrNew();
        factory(Appointment::class)->create(['specialist_id' => $specialist->id, 'patient_id' => $patient->id]);

        $this->actingAs($this->userWithAuthorization['user'])->get(str_replace(':id', $patient->id, $this->apiV1AppointmentsUrl));
        $this->seeJson(['status' => true])->seeJsonStructure(['data' => []])->seeJsonDoesntContains(['errors']);
    }
}
