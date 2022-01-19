<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Models\User;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_login_success()
    {
    	$user = User::inRandomOrder()->first();

        $this->post('/api/login', ['email' => 'kciptaraksa@gmail.com', 'password' => 'qwerty'])
             ->seeJson([
                'status' => 'success',
             ]);
    }

    public function test_login_failed()
    {
    	$user = User::inRandomOrder()->first();

        $this->post('/api/login', ['email' => 'kciptaraksa@gmail.com', 'password' => '123qwerty'])
             ->seeJson([
                'status' => 'error',
             ]);
    }

    public function test_register_success()
    {
    	$faker = Faker\Factory::create();

    	$data = [
    		'name' => $faker->name(),
    		'email' => $faker->email(),
    		'password' => '123456'
    	];

    	$this->post('/api/register', $data)
    		->seeJson([
    			'status' => 'success'
    		]);
    }

    public function test_register_failed()
    {
    	$faker = Faker\Factory::create();

    	$data = [
    		'name' => $faker->name(),
    		'email' => $faker->email(),
    		'password' => '1234'
    	];

    	$this->post('/api/register', $data)
    		->seeJson([
    			'status' => 'error'
    		]);
    }

    public function test_logout_failed()
    {
    	$response = $this->call('POST', '/api/logout');

    	$this->assertEquals(401, $response->status());
    }
}
