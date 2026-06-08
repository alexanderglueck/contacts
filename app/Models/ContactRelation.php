<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use App\Scopes\BelongsToTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A bidirectional, free-text-labelled link between two contacts, stored once.
 *
 * The pair is always normalised so contact_a_id < contact_b_id; the two
 * direction labels are swapped to match on write. That way a single unique
 * key on (team_id, contact_a_id, contact_b_id) catches the pair regardless of
 * which contact the link was created from, and each link exists as one row.
 *
 *   a_to_b_label — what B is to A (shown when viewing A, next to B)
 *   b_to_a_label — what A is to B (shown when viewing B, next to A)
 *
 * "Forward" in the create/edit flow means "what the linked contact is to the
 * contact whose page I'm on"; "inverse" is the reverse. orientation is handled
 * by linkBetween()/applyLabelsFrom() so callers never touch a/b directly.
 */
class ContactRelation extends Model
{
    use HasUlidRouteKey, HasFactory;

    protected $fillable = [
        'contact_a_id',
        'contact_b_id',
        'a_to_b_label',
        'b_to_a_label',
        'team_id',
        'created_by',
        'updated_by',
    ];

    // Cast the FK columns so the strict (===) orientation comparisons in
    // orient()/otherContactId() stay correct regardless of whether the PDO
    // driver returns native ints or strings (e.g. with emulated prepares).
    protected $casts = [
        'contact_a_id' => 'integer',
        'contact_b_id' => 'integer',
        'team_id' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new BelongsToTenantScope());

        static::creating(function (self $relation) {
            if (auth()->check() && $relation->team_id === null) {
                $relation->team_id = auth()->user()->current_team_id;
            }

            $relation->created_by = auth()->id();
            $relation->updated_by = auth()->id();
        });

        static::updating(function (self $relation) {
            $relation->updated_by = auth()->id();
        });
    }

    public function contactA(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_a_id');
    }

    public function contactB(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_b_id');
    }

    /**
     * Create a normalised relation between two contacts.
     *
     * @param  Contact  $from           the contact whose page the link is created from
     * @param  Contact  $to             the contact being linked to
     * @param  string   $forwardLabel   what $to is to $from (shown on $from's page)
     * @param  string   $inverseLabel   what $from is to $to (shown on $to's page)
     */
    public static function linkBetween(Contact $from, Contact $to, string $forwardLabel, string $inverseLabel): self
    {
        $relation = new self();
        $relation->orient($from, $to, $forwardLabel, $inverseLabel);
        $relation->save();

        return $relation;
    }

    /**
     * Re-apply labels for an existing relation, given the perspective contact.
     * $forwardLabel is what the *other* contact is to $perspective.
     */
    public function applyLabelsFrom(Contact $perspective, string $forwardLabel, string $inverseLabel): void
    {
        $other = $this->otherContactId($perspective->id);
        $this->orient($perspective, (new Contact())->forceFill(['id' => $other]), $forwardLabel, $inverseLabel);
        $this->save();
    }

    /**
     * The id of the contact on the far side of this relation from $contactId.
     */
    public function otherContactId(int $contactId): int
    {
        return $this->contact_a_id === $contactId ? $this->contact_b_id : $this->contact_a_id;
    }

    /**
     * Assign contact_a/contact_b (ordered by id) and the matching labels.
     * a_to_b_label = role of B as seen from A; b_to_a_label = role of A from B.
     */
    private function orient(Contact $from, Contact $to, string $forwardLabel, string $inverseLabel): void
    {
        if ($from->id < $to->id) {
            $this->contact_a_id = $from->id;
            $this->contact_b_id = $to->id;
            $this->a_to_b_label = $forwardLabel; // B (=to) as seen from A (=from)
            $this->b_to_a_label = $inverseLabel; // A (=from) as seen from B (=to)
        } else {
            $this->contact_a_id = $to->id;
            $this->contact_b_id = $from->id;
            $this->a_to_b_label = $inverseLabel; // B (=from) as seen from A (=to)
            $this->b_to_a_label = $forwardLabel; // A (=to) as seen from B (=from)
        }
    }
}
