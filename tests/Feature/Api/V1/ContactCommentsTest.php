<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/contacts/{contact}/comments — threaded comments with the same
 * tombstone-on-delete-with-replies semantics the web flow uses.
 */
class ContactCommentsTest extends TestCase
{
    use RefreshDatabase;

    private function aContact(): Contact
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        return create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    #[Test]
    public function it_indexes_a_contacts_comments_ordered_by_id_ascending()
    {
        $contact = $this->aContact();
        $contact->comments()->create(['comment' => 'first', 'created_by' => auth()->id()]);
        $contact->comments()->create(['comment' => 'second', 'created_by' => auth()->id()]);

        $response = $this->getJson(route('api.v1.contacts.comments.index', $contact->ulid));

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
        $this->assertSame('first', $response->json('data.0.comment'));
        $this->assertSame('second', $response->json('data.1.comment'));
    }

    #[Test]
    public function it_stores_a_root_comment()
    {
        $contact = $this->aContact();

        $response = $this->postJson(route('api.v1.contacts.comments.store', $contact->ulid), [
            'comment' => 'First impression: friendly.',
        ]);

        $response->assertCreated();
        $this->assertSame('First impression: friendly.', $response->json('data.comment'));
        $this->assertNull($response->json('data.parent_ulid'));
        $this->assertDatabaseHas('comments', ['contact_id' => $contact->id]);
    }

    #[Test]
    public function it_stores_a_reply_with_a_parent_ulid()
    {
        $contact = $this->aContact();
        $parent = $contact->comments()->create(['comment' => 'parent', 'created_by' => auth()->id()]);

        $response = $this->postJson(route('api.v1.contacts.comments.store', $contact->ulid), [
            'comment' => 'reply',
            'parent_ulid' => $parent->ulid,
        ]);

        $response->assertCreated();
        $this->assertSame($parent->ulid, $response->json('data.parent_ulid'));
    }

    #[Test]
    public function it_refuses_a_parent_ulid_that_belongs_to_another_contact()
    {
        $contact = $this->aContact();
        $otherContact = create(Contact::class, [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        $otherParent = $otherContact->comments()->create([
            'comment' => 'other thread',
            'created_by' => auth()->id(),
        ]);

        // A parent_ulid that exists in the system but on a different
        // contact must not be accepted — otherwise replies could be
        // smuggled across threads.
        $this->postJson(route('api.v1.contacts.comments.store', $contact->ulid), [
            'comment' => 'reply',
            'parent_ulid' => $otherParent->ulid,
        ])->assertStatus(422)
          ->assertJsonValidationErrors('parent_ulid');
    }

    #[Test]
    public function it_updates_a_comments_body()
    {
        $contact = $this->aContact();
        $comment = $contact->comments()->create(['comment' => 'old', 'created_by' => auth()->id()]);

        $this->patchJson(route('api.v1.contacts.comments.update', [$contact->ulid, $comment->ulid]), [
            'comment' => 'new',
        ])->assertOk()
          ->assertJsonPath('data.comment', 'new');

        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'comment' => 'new']);
    }

    #[Test]
    public function deleting_a_childless_comment_removes_the_row()
    {
        $contact = $this->aContact();
        $comment = $contact->comments()->create(['comment' => 'standalone', 'created_by' => auth()->id()]);

        $this->deleteJson(route('api.v1.contacts.comments.destroy', [$contact->ulid, $comment->ulid]))
            ->assertNoContent();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    #[Test]
    public function deleting_a_comment_with_replies_tombstones_it_instead_of_removing_the_row()
    {
        $contact = $this->aContact();
        $parent = $contact->comments()->create(['comment' => 'parent', 'created_by' => auth()->id()]);
        $contact->comments()->create([
            'comment' => 'reply',
            'parent_id' => $parent->id,
            'created_by' => auth()->id(),
        ]);

        $this->deleteJson(route('api.v1.contacts.comments.destroy', [$contact->ulid, $parent->ulid]))
            ->assertNoContent();

        // Row still exists, body nulled, tombstoned_at filled — so the
        // reply has something to render under.
        $this->assertDatabaseHas('comments', ['id' => $parent->id, 'comment' => null]);
        $this->assertNotNull($parent->fresh()->tombstoned_at);
    }

    #[Test]
    public function deleting_the_last_reply_cleans_up_a_tombstoned_parent()
    {
        $contact = $this->aContact();
        $parent = $contact->comments()->create(['comment' => 'parent', 'created_by' => auth()->id()]);
        $reply = $contact->comments()->create([
            'comment' => 'reply',
            'parent_id' => $parent->id,
            'created_by' => auth()->id(),
        ]);

        // First delete tombstones the parent (still has a reply).
        $this->deleteJson(route('api.v1.contacts.comments.destroy', [$contact->ulid, $parent->ulid]))
            ->assertNoContent();

        // Second delete on the reply — there are no other children, so the
        // tombstoned parent should also be cleaned out by cleanupAncestors.
        $this->deleteJson(route('api.v1.contacts.comments.destroy', [$contact->ulid, $reply->ulid]))
            ->assertNoContent();

        $this->assertDatabaseMissing('comments', ['id' => $reply->id]);
        $this->assertDatabaseMissing('comments', ['id' => $parent->id]);
    }

    #[Test]
    public function comments_require_authentication()
    {
        $user = $this->createUser();
        $contact = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);

        $this->postJson('/logout');
        // Now a guest:
        $this->getJson(route('api.v1.contacts.comments.index', $contact->ulid))
            ->assertStatus(401);
    }
}
