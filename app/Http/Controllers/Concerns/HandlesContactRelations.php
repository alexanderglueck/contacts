<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Contact;
use App\Models\ContactRelation;
use Illuminate\Http\Request;

/**
 * Shared helpers for the web and API contact-relation controllers — the label
 * defaulting rule and the two-sided ownership guard, kept in one place so the
 * two layers can't drift apart.
 */
trait HandlesContactRelations
{
    /**
     * The inverse label mirrors the forward label when left blank, so a
     * symmetric relation (friend/friend) only needs one field filled.
     */
    protected function inverseLabel(Request $request): string
    {
        $inverse = trim((string) $request->input('inverse_label', ''));

        return $inverse !== '' ? $inverse : $request->input('forward_label');
    }

    /**
     * Guard the two-sided pivot: confirm the relation actually references the
     * parent contact. Route-model binding already tenant-scopes it via the
     * ulid + BelongsToTenantScope; this rules out pointing an unrelated
     * relation's ulid at a contact the user happens to be able to see.
     */
    protected function verifyInvolves(Contact $contact, ContactRelation $relation): void
    {
        abort_unless(
            in_array($contact->id, [$relation->contact_a_id, $relation->contact_b_id], true),
            404,
        );
    }
}
