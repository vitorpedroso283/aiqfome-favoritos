<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs in a user with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@test.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            'token',
        ]
    ]);
});

it('fails to login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('correct-password'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@test.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
    $response->assertJson(['message' => 'Credenciais Inválidas']);
});

it('fails if email is not provided', function () {
    $response = $this->postJson('/api/auth/login', [
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('fails if password is not provided', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@test.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('returns the token upon successful login', function () {
    User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@test.com',
        'password' => 'password123',
    ]);

    $response->assertJsonStructure([
        'data' => [
            'token',
        ]
    ]);
});
