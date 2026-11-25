<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('renders repair materials fallback page without undefined variable error when no repair is selected', function (): void {
    $user = User::factory()->create();

    $response = $this->withoutVite()
        ->actingAs($user)
        ->get('/dashboard/repair/materials');

    $response->assertOk()
        ->assertSee('Material Perbaikan')
        ->assertSee('Pilih Data Perbaikan Terlebih Dahulu');
});
