<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactRelation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Bidirectional contact relations (web/Inertia endpoints). Relations are
 * stored once per pair (contact_a_id < contact_b_id, labels swapped to match)
 * and read back oriented to whichever contact you're viewing. There is no GET
 * endpoint — reads happen via the lazy `relations` prop on the contact's show
 * page — so orientation is asserted through Contact::relationEntries().
 */
class ContactRelationsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: Contact, 1: Contact} a pair of contacts in one team */
    private function aPair(): array
    {
        $user = $this->createUser();

        $a = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);
        $b = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);

        return [$a, $b];
    }

    /** The single relation entry as seen from $contact's perspective. */
    private function entryFrom(Contact $contact): array
    {
        return $contact->fresh()->relationEntries()->first();
    }

    // ── Create ───────────────────────────────────────────────────────────

    #[Test]
    public function it_stores_a_relation_read_correctly_from_both_sides()
    {
        [$father, $child] = $this->aPair();

        $this->post(route('contact_relations.store', $father->ulid), [
            'related_contact' => $child->ulid,
            'forward_label' => 'daughter',
            'inverse_label' => 'father',
        ])->assertRedirect();

        $this->assertDatabaseCount('contact_relations', 1);

        // From the father, the child is his "daughter"; he is her "father".
        $fromFather = $this->entryFrom($father);
        $this->assertSame($child->id, $fromFather['contact']->id);
        $this->assertSame('daughter', $fromFather['label']);
        $this->assertSame('father', $fromFather['inverse']);

        // The inverse view comes for free from the single stored row.
        $fromChild = $this->entryFrom($child);
        $this->assertSame($father->id, $fromChild['contact']->id);
        $this->assertSame('father', $fromChild['label']);
        $this->assertSame('daughter', $fromChild['inverse']);
    }

    #[Test]
    public function it_normalises_the_pair_so_contact_a_id_is_always_the_lower_id()
    {
        [$a, $b] = $this->aPair();

        // Create the link from the higher-id contact to force the swap path.
        [$lower, $higher] = $a->id < $b->id ? [$a, $b] : [$b, $a];

        $this->post(route('contact_relations.store', $higher->ulid), [
            'related_contact' => $lower->ulid,
            'forward_label' => 'brother',
            'inverse_label' => 'sister',
        ])->assertRedirect();

        $relation = ContactRelation::first();
        $this->assertSame($lower->id, $relation->contact_a_id);
        $this->assertSame($higher->id, $relation->contact_b_id);
        // Stored row is still read correctly from the creating contact.
        $this->assertSame('brother', $this->entryFrom($higher)['label']);
    }

    #[Test]
    public function a_blank_inverse_label_mirrors_the_forward_label()
    {
        [$a, $b] = $this->aPair();

        $this->post(route('contact_relations.store', $a->ulid), [
            'related_contact' => $b->ulid,
            'forward_label' => 'friend',
            // inverse_label omitted
        ])->assertRedirect();

        $entry = $this->entryFrom($a);
        $this->assertSame('friend', $entry['label']);
        $this->assertSame('friend', $entry['inverse']);
    }

    #[Test]
    public function it_rejects_relating_a_contact_to_itself()
    {
        [$a] = $this->aPair();

        $this->post(route('contact_relations.store', $a->ulid), [
            'related_contact' => $a->ulid,
            'forward_label' => 'self',
        ])->assertSessionHasErrors('related_contact');

        $this->assertDatabaseCount('contact_relations', 0);
    }

    #[Test]
    public function it_rejects_a_duplicate_pair_in_either_direction()
    {
        [$a, $b] = $this->aPair();
        ContactRelation::linkBetween($a, $b, 'colleague', 'colleague');

        // Same direction.
        $this->post(route('contact_relations.store', $a->ulid), [
            'related_contact' => $b->ulid,
            'forward_label' => 'friend',
        ])->assertSessionHasErrors('related_contact');

        // Reverse direction.
        $this->post(route('contact_relations.store', $b->ulid), [
            'related_contact' => $a->ulid,
            'forward_label' => 'friend',
        ])->assertSessionHasErrors('related_contact');

        $this->assertDatabaseCount('contact_relations', 1);
    }

    #[Test]
    public function it_validates_that_a_forward_label_is_required()
    {
        [$a, $b] = $this->aPair();

        $this->post(route('contact_relations.store', $a->ulid), [
            'related_contact' => $b->ulid,
        ])->assertSessionHasErrors('forward_label');

        $this->assertDatabaseCount('contact_relations', 0);
    }

    // ── Update ───────────────────────────────────────────────────────────

    #[Test]
    public function it_updates_labels_keeping_orientation()
    {
        [$a, $b] = $this->aPair();
        $relation = ContactRelation::linkBetween($a, $b, 'daughter', 'father');

        $this->put(route('contact_relations.update', [$a->ulid, $relation->ulid]), [
            'forward_label' => 'kid',
            'inverse_label' => 'dad',
        ])->assertRedirect();

        $fromA = $this->entryFrom($a);
        $this->assertSame('kid', $fromA['label']);
        $this->assertSame('dad', $fromA['inverse']);

        // The other side flips accordingly.
        $fromB = $this->entryFrom($b);
        $this->assertSame('dad', $fromB['label']);
        $this->assertSame('kid', $fromB['inverse']);
    }

    #[Test]
    public function it_updates_labels_correctly_when_edited_from_the_higher_id_side()
    {
        [$a, $b] = $this->aPair();
        [$lower, $higher] = $a->id < $b->id ? [$a, $b] : [$b, $a];
        $relation = ContactRelation::linkBetween($lower, $higher, 'mentor', 'mentee');

        // Edit from the higher-id contact: it sees the lower as its "mentor".
        $this->put(route('contact_relations.update', [$higher->ulid, $relation->ulid]), [
            'forward_label' => 'boss',
            'inverse_label' => 'report',
        ])->assertRedirect();

        $this->assertSame('boss', $this->entryFrom($higher)['label']);
        $this->assertSame('report', $this->entryFrom($lower)['label']);
    }

    // ── Delete ───────────────────────────────────────────────────────────

    #[Test]
    public function it_deletes_a_relation()
    {
        [$a, $b] = $this->aPair();
        $relation = ContactRelation::linkBetween($a, $b, 'friend', 'friend');

        $this->delete(route('contact_relations.destroy', [$a->ulid, $relation->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('contact_relations', ['id' => $relation->id]);
    }

    #[Test]
    public function it_404s_when_the_relation_does_not_involve_the_parent_contact()
    {
        [$b, $c] = $this->aPair();
        $a = create(Contact::class, ['created_by' => $b->created_by, 'updated_by' => $b->updated_by]);
        $relation = ContactRelation::linkBetween($b, $c, 'brother', 'sister');

        // $a is a valid, visible contact but is not part of this relation.
        $this->delete(route('contact_relations.destroy', [$a->ulid, $relation->ulid]))->assertNotFound();
        $this->assertDatabaseHas('contact_relations', ['id' => $relation->id]);
    }

    // ── Search picker ────────────────────────────────────────────────────

    #[Test]
    public function search_finds_candidates_by_name()
    {
        $user = $this->createUser();
        $a = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);
        $target = create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'lastname' => 'Zzytestsson',
        ]);

        $this->getJson(route('contact_relations.search', $a->ulid) . '?q=Zzytestsson')
            ->assertOk()
            ->assertJsonFragment(['ulid' => $target->ulid]);
    }

    #[Test]
    public function search_excludes_the_contact_itself_and_already_related_contacts()
    {
        $user = $this->createUser();
        $a = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id, 'lastname' => 'Selfsson']);
        $linked = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id, 'lastname' => 'Linkedsson']);
        ContactRelation::linkBetween($a, $linked, 'friend', 'friend');

        // Already-linked contact is filtered out.
        $this->getJson(route('contact_relations.search', $a->ulid) . '?q=Linkedsson')
            ->assertOk()
            ->assertJsonMissing(['ulid' => $linked->ulid]);

        // The contact never matches itself.
        $this->getJson(route('contact_relations.search', $a->ulid) . '?q=Selfsson')
            ->assertOk()
            ->assertJsonMissing(['ulid' => $a->ulid]);
    }
}
