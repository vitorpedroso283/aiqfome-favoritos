<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('logs in a user with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/auth/login', [
        'email' => 'user@test.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});

it('fails to login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'user@test.com',
        'password' => Hash::make('correct-password'),
    ]);

    $response = $this->postJson('/auth/login', [
        'email' => 'user@test.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
    $response->assertJson(['message' => 'Credenciais Inválidas']);
});
