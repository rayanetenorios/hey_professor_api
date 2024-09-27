<?php

use App\Models\Question;
use App\Models\User;
use App\Rules\WithQuestionMark;
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

    test('question ending with question mark', function() {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
    
        postJson(route('questions.store'), [
            'question' => 'Question without a question mark',
        ])
            ->assertJsonValidationErrors([
                'question' => 'The question should end with question mark (?).'
            ]);
    });

    // colocar teste de quantidade de caracteres aqui

    test('question should be unique', function() {
        $user = User::factory()->create();

        Question::factory()->create([
            'question' => 'Lorem ipsun jeremias?', 
            'status'   => 'draft',
            'user_id'  => $user->id
        ]);

        Sanctum::actingAs($user);
    
        postJson(route('questions.store'), [
            'question' => 'Lorem ipsun jeremias?',
        ])
            ->assertJsonValidationErrors([
                'question' => 'already been taken'
            ]);
    });
});

test('after creating we should return a status 201 with the created question', function() {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $request = postJson(route('questions.store'), [
        'question' => 'Lorem ipsun jeremias?',
    ])->assertCreated();

    $question = Question::latest()->first();

    $request->assertJson([
        'data' => [
            'id'         => $question->id,
            'question'   => $question->question,
            'status'     => $question->status,
            'created_by' => [
                'id'   => $question->user->id,
                'name' => $question->user->name,
            ],
            'created_at' => $question->created_at->format('Y-m-d h:i:s'),
            'updated_at' => $question->updated_at->format('Y-m-d h:i:s'),
        ]
    ]);
});
