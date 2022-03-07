<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ParentTestClass;
use Faker\Factory as Faker;

class UserRegisterTest extends ParentTestClass
{
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
        $this->user_data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'abc@123',
            'password_confirmation' => 'abc@123'
        ];
    }   

    public function test_register_user()
    {
        $response = $this->json('POST', testRoute('register'), $this->user_data);
        $response->assertStatus(200)
            ->assertSeeText('Success');
    }

    public function test_email_not_valid()
    {
        $this->user_data['email'] = "abc";
        $response = $this->json('POST', testRoute('register'), $this->user_data);
        $response->assertStatus(400)
            ->assertSeeText('The email must be a valid email address.');
    }

    public function test_password_validation()
    {
        $this->user_data['password'] = "abc";
        $response = $this->json('POST', testRoute('register'), $this->user_data);
        $response->assertStatus(400)
            ->assertSeeText('The password must be at least 6 characters')
            ->assertSeeText('The password confirmation does not match');
    }

    public function test_name_is_required()
    {
        $this->user_data['name'] = "";
        $response = $this->json('POST', testRoute('register'), $this->user_data);
        $response->assertStatus(400)
            ->assertSeeText('The name field is required');
    }

    public function test_email_is_required()
    {
        $this->user_data['email'] = "";
        $response = $this->json('POST', testRoute('register'), $this->user_data);
        $response->assertStatus(400)
            ->assertSeeText('The email field is required');
    }

    public function test_password_is_required()
    {
        $this->user_data['password'] = "";
        $response = $this->json('POST', testRoute('register'), $this->user_data);
        $response->assertStatus(400)
            ->assertSeeText('The password field is required');
    }
}