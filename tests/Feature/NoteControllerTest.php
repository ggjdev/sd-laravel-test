<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotesControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testNoteCanBeCreatedWithNoContent(): void
    {
        $this->actingAs($this->user);

        $noteData = [
            'title' => 'Test Note',
            'content' => null,
        ];

        $response = $this->post(route('note.store'), $noteData);

        $response->assertRedirect(route('note.list'));

        $noteData['user_id'] = $this->user->id;
        $this->assertDatabaseHas('notes', $noteData);
    }

    public function testNoteCanBeUpdatedToHaveNoContent(): void
    {
        $this->actingAs($this->user);

        $note = Note::create([
            'user_id' => $this->user->id,
            'title' => 'Test Note',
            'content' => 'This is a test note',
        ]);

        $noteData = [
            'title' => 'Test Note',
            'content' => null,
        ];

        $response = $this->post(route('note.store', ['note_id' => $note->id]), $noteData);

        $response->assertRedirect(route('note.list'));

        $noteData['id'] = $note->id;
        $noteData['user_id'] = $this->user->id;

        $this->assertDatabaseHas('notes', $noteData);
    }
}
