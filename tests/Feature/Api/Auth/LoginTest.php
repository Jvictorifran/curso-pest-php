<?php

use function Pest\Laravel\postJson;
use App\Models\User;

test('should auth user', function () {
    $user = User::factory()->create();
    $data = [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'e2e_test',
    ];
    postJson(route('auth.login'), $data )
    ->assertOk()
    ->assertJsonStructure(['token']);
 });

 test('should fail auth user - with wrong password', function () {
    $user = User::factory()->create();
    $data = [
        'email' => $user->email,
        'password' => 'wrong-password',
        'device_name' => 'e2e_test',
    ];
    postJson(route('auth.login'), $data )
    ->assertStatus(422);
 });

 describe('validations', function(){
    it('o email é obrigatorio', function(){
        postJson(route('auth.login'),[
            'password'=> 'password',
            'device_name' => 'e2e_test',
        ])
        ->assertJsonValidationErrors([
            'email' => trans('validation.required', ['attribute' => 'email'])
        ]);
    });

 });