<?php

use App\Patient;
use App\Review;
use App\Specialist;
use App\User;

/**
 * Review Controller Test Suite
 * 
 */
class ReviewControllerTest extends TestCase
{
    private $apiV1ReviewsUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=PatientsTableSeeder --class=SpecialistsTableSeeder');

        $this->apiV1ReviewsUrl = $this->apiV1UsersUrl . '/:id/reviews';
        $this->userWithAuthorization = $this->get_user_with_authorization(['is_patient' => true]);
    }

    // public function testUserCannotAddReviewIfNotAuthenticated()
    // {
    //     $this->get('/')->assertResponseOk();

    //     $url = str_replace(':id', 0, $this->apiV1ReviewsUrl);
    //     $review = factory(Review::class)->make()->toArray();

    //     $this->post($url, $review, ['Authorization' => 'Bearer ']);
    //     print_r(['url' => $url, 'review' => $review, $this->response->getContent()]);
    //     $this->seeStatusCode(401)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    // }

    /**
     * @return void
     */
    public function testReviewCannotBeAddedForANonSpecialist()
    {
        $this->get('/')->assertResponseOk();

        $url = str_replace(':id', 0, $this->apiV1ReviewsUrl);
        $review = factory(Review::class)->make()->toArray();

        $this->actingAs($this->userWithAuthorization['user'])->post($url, $review, $this->userWithAuthorization['authorization'])->seeStatusCode(400);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * @return void
     */
    public function testReviewCannotBeAddedWithBlankRemark()
    {
        $this->get('/')->assertResponseOk();

        $url = str_replace(':id', Specialist::first()->user_id, $this->apiV1ReviewsUrl);
        $review = factory(Review::class)->make(['remark' => ''])->toArray();

        $this->actingAs($this->userWithAuthorization['user'])->post($url, $review, $this->userWithAuthorization['authorization'])->seeStatusCode(400);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * @return void
     */
    public function testReviewCannotBeAddedWithBlankRating()
    {
        $this->get('/')->assertResponseOk();

        $url = str_replace(':id', Specialist::first()->user_id, $this->apiV1ReviewsUrl);
        $review = factory(Review::class)->make(['rating' => ''])->toArray();

        $this->actingAs($this->userWithAuthorization['user'])->post($url, $review, $this->userWithAuthorization['authorization'])->seeStatusCode(400);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * @return void
     */
    public function testReviewCanOnlyBeAddedByAPatient()
    {
        $this->get('/')->assertResponseOk();

        $specialist_user_id = Specialist::first()->user_id;
        $url = str_replace(':id', $specialist_user_id, $this->apiV1ReviewsUrl);
        $review = factory(Review::class)->make()->toArray();

        $this->actingAs($this->userWithAuthorization['user'])->post($url, $review, $this->userWithAuthorization['authorization']);
        $this->seeStatusCode(201)->seeJson(['status' => true])->seeInDatabase('reviews', [
            'specialist_id' => $specialist_user_id, 'remark' => $review['remark']
        ]);
        $this->seeJsonStructure(['data' => ['id', 'remark', 'created_at'], 'message'])->seeJsonDoesntContains(['errors']);
    }

    /**
     * @return void
     */
    public function testReviewCannotBeGivenIfUserIsNotAPatient()
    {
        $this->get('/')->assertResponseOk();

        $url = str_replace(':id', Specialist::first()->user_id, $this->apiV1ReviewsUrl);
        $review = factory(Review::class)->make()->toArray();

        $nonPatient = factory(User::class)->make(['is_patient' => false]);
        $this->actingAs($nonPatient)->post($url, $review, $this->userWithAuthorization['authorization']);
        $this->seeStatusCode(400)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    public function testReviewCannotBeEditedByADifferentAuthor()
    {
        $this->get('/')->assertResponseOk();

        $specialist_user_id = Specialist::first()->user_id;
        $url = str_replace(':id', $specialist_user_id, $this->apiV1ReviewsUrl);

        $anotherPatient = factory(Patient::class)->create(['user_id' => factory(User::class)->create(['is_patient' => true])->id]);
        $review = factory(Review::class)->create([
            'specialist_id' => $specialist_user_id, 'patient_id' => $anotherPatient->user->id
        ]);

        $this->actingAs($this->userWithAuthorization['user'])->put("{$url}/{$review->id}", $review->toArray());
        $this->seeStatusCode(400)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    public function testReviewCannotBeUpdatedByWithBlankRemark()
    {
        $this->get('/')->assertResponseOk();

        $specialist_user_id = Specialist::first()->user_id;
        $url = str_replace(':id', $specialist_user_id, $this->apiV1ReviewsUrl);

        $review = factory(Review::class)->create(['specialist_id' => $specialist_user_id, 'patient_id' => $this->userWithAuthorization['user']->id]);
        $review->remark = '';

        $this->actingAs($this->userWithAuthorization['user'])->put("{$url}/{$review->id}", $review->toArray());
        $this->seeStatusCode(400)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    public function testReviewCanOnlyBeEditedByItsOriginalAuthor()
    {
        $this->get('/')->assertResponseOk();

        $specialist_user_id = Specialist::first()->user_id;
        $url = str_replace(':id', $specialist_user_id, $this->apiV1ReviewsUrl);

        $review = factory(Review::class)->create(['specialist_id' => $specialist_user_id, 'patient_id' => $this->userWithAuthorization['user']->id]);
        $review->remark = "{$review->remark}-x";

        $this->actingAs($this->userWithAuthorization['user'])->put("{$url}/{$review->id}", $review->toArray());
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeInDatabase('reviews', [
            'specialist_id' => $specialist_user_id, 'remark' => $review['remark']
        ])->seeJsonStructure(['data' => ['id', 'remark', 'updated_at'], 'message'])->seeJsonDoesntContains(['errors']);
    }
}
