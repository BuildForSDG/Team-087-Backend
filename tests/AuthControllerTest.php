<?php

use App\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * Auth Controller Test
 * @covers App\Http\AuthController
 * 
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 * @uses App\User
 */
class AuthControllerTest extends TestCase
{
    private $apiV1RegisterUrl;
    private $apiV1VerifyUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiV1RegisterUrl = $this->apiV1 . '/auth/register';
        $this->apiV1VerifyUrl = $this->apiV1 . '/auth/verify';
    }

    /**
     * Should Pass If Fresh Email is Provided
     *
     * @return void
     */
    public function testUserCanBeCreatedWithFreshAndUnusedEmail()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = array_merge(factory(User::class)->make()->toArray(), ['password' => 'passw0rd', 'password_confirmation' => 'passw0rd']);

        $this->json('POST', $this->apiV1RegisterUrl, $user)->seeStatusCode(201)->seeInDatabase('users', ['email' => $user['email']]);
        $this->seeJson(['status' => true])->seeJsonStructure(['data' => ['id', 'email', 'created_at'], 'message'])->seeJsonDoesntContains(['errors']);
    }

    /**
     * Should Fail If Existing Email is Provided
     *
     * @return void
     */
    public function testUserCannotBeCreatedWithExistingEmail()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = array_merge(User::first()->toArray(), ['password' => 'passw0rd', 'password_confirmation' => 'passw0rd']);

        $this->json('POST', $this->apiV1RegisterUrl, $user)->assertResponseStatus(400);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors' => ['email'], 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * Should fail if invalid parameters are submitted
     *
     * @return void
     */
    public function testUserCannotBeCreatedWithInvalidParameters()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = ['last_name' => 'Meyer', 'first_name' => 'Jack', 'email' => 'mentalapp@', 'gender' => 'males'];
        $this->post($this->apiV1RegisterUrl, $user)->assertResponseStatus(400);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * Should fail verification for incorrect verification-code
     *
     * @return void
     */
    public function testUserCannotBeVerifiedWithIncorrectCode()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = User::where(['is_active' => false])->first();
        $this->get("{$this->apiV1VerifyUrl}?code=x-{$user->profile_code}&email={$user->email}")->assertResponseStatus(400);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * Should fail verification for incorrect email-address
     *
     * @return void
     */
    public function testUserCannotBeVerifiedWithIncorrectEmail()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = User::where(['is_active' => false])->first();
        $this->get("{$this->apiV1VerifyUrl}?code={$user->profile_code}&email=x-{$user->email}")->assertResponseStatus(404);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * Should pass verification for correct verification-code and email-address
     *
     * @return void
     */
    public function testUserCanBeVerifiedWithCorrectCodeAndEmail()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = factory(User::class)->create();
        $this->get("{$this->apiV1VerifyUrl}?code={$user->profile_code}&email={$user->email}")->assertResponseStatus(200);
        $this->seeJson(['status' => true])->seeJsonStructure(['data', 'message'])->seeJsonDoesntContains(['errors']);
    }

    /**
     * Should fail verification for already verified account
     *
     * @return void
     */
    public function testUserCannotBeVerifiedTwiceForAlreadyVerifiedAccount()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = factory(User::class)->create(['is_active' => true, 'is_guest' => false]);
        $this->get("{$this->apiV1VerifyUrl}?code={$user->profile_code}&email={$user->email}")->assertResponseStatus(400);
        $this->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])->seeJsonDoesntContains(['data']);
    }

    /**
     * Should not be granted access with invalid signin-credentials
     *
     * @return void
     */
    public function testUserCannotSignInWithInvalidEmailAddressAndPassword()
    {
        $this->get('/')->assertResponseStatus(200);

        $this->post($this->apiV1SignInUrl, ['email' => 'randomuser@test.com', 'password' => 'passw0rd']);
        $this->seeStatusCode(400)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])
            ->seeJsonDoesntContains(['data']);
    }

    /**
     * Should only be granted access with valid/verified signin-credentials
     *
     * @return void
     */
    public function testUserCanOnlyBeAuthenticatedWithVerifiedEmailAddressAndPassword()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = factory(User::class)->create(['is_active' => true, 'is_guest' => false]);
        $this->post($this->apiV1SignInUrl, ['email' => $user->email, 'password' => 'markspencer']);

        $this->seeStatusCode(200)->seeJson(['status' => true])
            ->seeJsonStructure(['data' => ['id', 'email'], 'access_token', 'token_type', 'expires_in'])->seeJsonDoesntContains(['errors']);
    }

    /**
     * Should not be granted access with UNVERIFIED but valid signin-credentials
     *
     * @return void
     */
    public function testUserCannotSigninWithUnverifiedButValidEmailAddressAndPassword()
    {
        $this->get('/')->assertResponseStatus(200);

        $this->post($this->apiV1SignInUrl, ['email' => factory(User::class)->create()->email, 'password' => 'markspencer']);
        $this->seeStatusCode(401)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message'])
            ->seeJsonDoesntContains(['data']);
    }

    /**
     * Should not be able to sign-out after being authenticated
     *
     * @return void
     */
    public function testUserCanSignoutAfterBeingAuthenticated()
    {
        $this->get('/')->assertResponseStatus(200);

        $user = factory(User::class)->create(['is_active' => true, 'is_guest' => false]);
        $this->post($this->apiV1SignInUrl, ['email' => $user->email, 'password' => 'markspencer']);
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeJsonStructure(['access_token']);

        $response = json_decode($this->response->getContent());
        $this->actingAs($user)->post($this->apiV1SignOutUrl, [], ['Authorization' => "Bearer {$response->access_token}"]);
        $this->seeStatusCode(200)->seeJson(['status' => true])->seeJsonStructure(['message']);
    }

    /**
     * Should not be able to sign-out if not authenticated
     *
     * @return void
     */
    public function testUserCannotSignoutIfNotAuthenticated()
    {
        $this->get('/')->assertResponseStatus(200);

        $this->post($this->apiV1SignOutUrl);
        $this->seeStatusCode(401)->seeJson(['status' => false])->seeJsonStructure(['errors', 'message']);
    }
}
