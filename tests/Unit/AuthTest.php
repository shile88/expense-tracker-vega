<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\UserController;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

class AuthTest extends TestCase
{
    public function testLoginSuccessful(): void
    {
        $mockUserLoginRequest = Mockery::mock(UserLoginRequest::class, function (MockInterface $mock) {
            $mock->shouldReceive('validated')->once()->andReturn(['email' => 'test@test.com', 'password' => '123456']);
        });
        $mockUserService = Mockery::mock(UserService::class, function (MockInterface $mock) {
            $mock->shouldReceive('login')->once()->andReturn(['user', 'access_token']);
        });
        $controller = new UserController($mockUserService);

        $response = $controller->login($mockUserLoginRequest);
        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertEquals('Logged in successfully', $responseData['message']);
        $this->assertEquals('user', $responseData['data']['user']);
        $this->assertEquals('access_token', $responseData['data']['access_token']);
    }

    public function testLoginNotSuccessful(): void
    {
        $mockUserLoginRequest = Mockery::mock(UserLoginRequest::class, function (MockInterface $mock) {
            $mock->shouldReceive('validated')->once()->andReturn(['email' => 'test@test.com', 'password' => '123456']);
        });
        $mockUserService = Mockery::mock(UserService::class, function (MockInterface $mock) {
            $mock->shouldReceive('login')->once()->andReturn(false);
        });
        $controller = new UserController($mockUserService);

        $response = $controller->login($mockUserLoginRequest);
        $responseData = json_decode($response->getContent(), true);

        $this->assertFalse($responseData['success']);
        $this->assertEquals('The provided credentials do not match our records.', $responseData['message']);
    }

    public function testRegisterUserSuccessful(): void
    {
        $validatedUserData = ['name' => 'test', 'email' => 'test@test.com', 'password' => '123456'];
        $mockUserRegisterRequest = Mockery::mock(UserRegisterRequest::class, function (MockInterface $mock) use ($validatedUserData) {
            $mock->shouldReceive('validated')->once()->andReturn($validatedUserData);
        });
        $mockUserService = Mockery::mock(UserService::class, function (MockInterface $mock) use ($validatedUserData) {
            $mock->shouldReceive('register')->once()->andReturn(new User($validatedUserData));
        });
        $controller = new UserController($mockUserService);

        $response = $controller->register($mockUserRegisterRequest);
        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertEquals('User created successfully', $responseData['message']);
        $this->assertEquals('test', $responseData['data']['user']['name']);
        $this->assertEquals('test@test.com', $responseData['data']['user']['email']); 
    }

    public function testRegisterUserLogout(): void
    {
        $mockUserService = Mockery::mock(UserService::class, function (MockInterface $mock) {
            $mock->shouldReceive('logout')->once()->andReturn(true);
        });
        $controller = new UserController($mockUserService);

        $response = $controller->logout();
        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['success']);
        $this->assertEquals('Logged out successfully', $responseData['message']);
    }
}
