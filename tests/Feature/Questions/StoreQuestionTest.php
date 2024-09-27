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
        'question' => 'Lorem ipsun jeremias?'
    ]);
});
