<?php

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
        $this->userWithAuthorization = $this->get_user_with_authorization();
    }

    public function testUserCanLoadHomePage()
    {
        $this->get('/')->assertResponseOk();
    }
}
