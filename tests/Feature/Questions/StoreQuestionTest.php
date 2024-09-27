<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('shold be able to store a new question', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store'), [
        'question' => 'Lorem ipsun jeremias?'
    ]);

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'question' => 'Lorem ipsun jeremias?'
    ]);
});

test('after creating a new question, I need to make sure that it creates on _draft_ status', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store'), [
        'question' => 'Lorem ipsun jeremias?',
    ]);

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'status'   => 'draft',
        'question' => 'Lorem ipsun jeremias?',
    ]);
});

describe('validation rules', function() {
    test('question required', function() {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
    
        postJson(route('questions.store'), [])
            ->assertJsonValidationErrors([
                'question' => 'required'
            ]);
    });
});
