<?php

namespace Tests\Unit\Models;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StateTest extends TestCase
{
    use RefreshDatabase;

    public function test_stores_and_soft_deletes_state(): void
    {
        $state = State::query()->create([
            'state_code' => '01',
            'name' => 'Aguascalientes',
            'total_population' => 1425607,
        ]);

        $state->delete();

        $this->assertSoftDeleted('states', ['id' => $state->id]);
        $this->assertNull(State::query()->find($state->id));
    }
}
