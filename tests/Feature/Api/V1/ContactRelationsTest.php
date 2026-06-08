<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use App\Models\ContactRelation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/contacts/{contact}/relations CRUD. The pivot is two-sided, so these
 * routes sit outside the scopeBindings() loop and the controller verifies the
 * relation involves {contact} itself. Payloads are oriented to {contact}:
 * `label` = what the related contact is to it, `inverse` = the reverse.
 */
class ContactRelationsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: Contact, 1: Contact} a pair of contacts in one team, authed via Sanctum */
    private function aPair(): array
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $a = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);
        $b = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);

        return [$a, $b];
    }

    #[Test]
    public function it_indexes_relations_oriented_to_each_contact()
    {
        [$father, $child] = $this->aPair();
        ContactRelation::linkBetween($father, $child, 'daughter', 'father');

        $this->getJson(route('api.v1.contacts.relations.index', $father->ulid))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.label', 'daughter')
            ->assertJsonPath('data.0.inverse', 'father')
            ->assertJsonPath('data.0.contact.ulid', $child->ulid);

        $this->getJson(route('api.v1.contacts.relations.index', $child->ulid))
            ->assertOk()
            ->assertJsonPath('data.0.label', 'father')
            ->assertJsonPath('data.0.inverse', 'daughter')
            ->assertJsonPath('data.0.contact.ulid', $father->ulid);
    }

    #[Test]
    public function it_stores_a_relation()
    {
        [$a, $b] = $this->aPair();

        $this->postJson(route('api.v1.contacts.relations.store', $a->ulid), [
            'related_contact' => $b->ulid,
            'forward_label' => 'wife',
            'inverse_label' => 'husband',
        ])
            ->assertCreated()
            ->assertJsonPath('data.label', 'wife')
            ->assertJsonPath('data.inverse', 'husband')
            ->assertJsonPath('data.contact.ulid', $b->ulid);

        $this->assertDatabaseCount('contact_relations', 1);
    }

    #[Test]
    public function a_blank_inverse_label_mirrors_the_forward_label()
    {
        [$a, $b] = $this->aPair();

        $this->postJson(route('api.v1.contacts.relations.store', $a->ulid), [
            'related_contact' => $b->ulid,
            'forward_label' => 'friend',
        ])
            ->assertCreated()
            ->assertJsonPath('data.label', 'friend')
            ->assertJsonPath('data.inverse', 'friend');
    }

    #[Test]
    public function it_rejects_relating_a_contact_to_itself()
    {
        [$a] = $this->aPair();

        $this->postJson(route('api.v1.contacts.relations.store', $a->ulid), [
            'related_contact' => $a->ulid,
            'forward_label' => 'self',
        ])->assertStatus(422)->assertJsonValidationErrors('related_contact');

        $this->assertDatabaseCount('contact_relations', 0);
    }

    #[Test]
    public function it_rejects_a_duplicate_pair()
    {
        [$a, $b] = $this->aPair();
        ContactRelation::linkBetween($a, $b, 'colleague', 'colleague');

        $this->postJson(route('api.v1.contacts.relations.store', $b->ulid), [
            'related_contact' => $a->ulid,
            'forward_label' => 'friend',
        ])->assertStatus(422)->assertJsonValidationErrors('related_contact');

        $this->assertDatabaseCount('contact_relations', 1);
    }

    #[Test]
    public function it_shows_a_single_relation_oriented_to_the_contact()
    {
        [$a, $b] = $this->aPair();
        $relation = ContactRelation::linkBetween($a, $b, 'mentor', 'mentee');

        $this->getJson(route('api.v1.contacts.relations.show', [$b->ulid, $relation->ulid]))
            ->assertOk()
            ->assertJsonPath('data.label', 'mentee')
            ->assertJsonPath('data.inverse', 'mentor')
            ->assertJsonPath('data.contact.ulid', $a->ulid);
    }

    #[Test]
    public function it_updates_labels()
    {
        [$a, $b] = $this->aPair();
        $relation = ContactRelation::linkBetween($a, $b, 'daughter', 'father');

        $this->patchJson(route('api.v1.contacts.relations.update', [$a->ulid, $relation->ulid]), [
            'forward_label' => 'kid',
            'inverse_label' => 'dad',
        ])
            ->assertOk()
            ->assertJsonPath('data.label', 'kid')
            ->assertJsonPath('data.inverse', 'dad');

        $this->getJson(route('api.v1.contacts.relations.index', $b->ulid))
            ->assertJsonPath('data.0.label', 'dad');
    }

    #[Test]
    public function it_deletes_a_relation()
    {
        [$a, $b] = $this->aPair();
        $relation = ContactRelation::linkBetween($a, $b, 'friend', 'friend');

        $this->deleteJson(route('api.v1.contacts.relations.destroy', [$a->ulid, $relation->ulid]))
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_relations', ['id' => $relation->id]);
    }

    #[Test]
    public function it_404s_when_the_relation_does_not_involve_the_parent_contact()
    {
        [$b, $c] = $this->aPair();
        $a = create(Contact::class, ['created_by' => $b->created_by, 'updated_by' => $b->updated_by]);
        $relation = ContactRelation::linkBetween($b, $c, 'brother', 'sister');

        $this->getJson(route('api.v1.contacts.relations.show', [$a->ulid, $relation->ulid]))
            ->assertStatus(404);
        $this->deleteJson(route('api.v1.contacts.relations.destroy', [$a->ulid, $relation->ulid]))
            ->assertStatus(404);
    }

    #[Test]
    public function the_contact_detail_endpoint_includes_oriented_relations()
    {
        [$father, $child] = $this->aPair();
        ContactRelation::linkBetween($father, $child, 'daughter', 'father');

        $this->getJson(route('api.v1.contacts.show', $father->ulid))
            ->assertOk()
            ->assertJsonPath('data.relations.0.label', 'daughter')
            ->assertJsonPath('data.relations.0.contact.ulid', $child->ulid);
    }
}
