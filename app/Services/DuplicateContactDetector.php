<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Collection;

class DuplicateContactDetector
{
    public function __construct(private readonly int $teamId)
    {
    }

    /**
     * Returns an array of groups, each shaped as:
     *   [
     *     'signal' => 'email' | 'name' | 'phone' | 'dob_lastname',
     *     'value'  => 'foo@bar.com' (or display value for the signal),
     *     'contacts' => [Contact, Contact, ...]   // always ≥ 2
     *   ]
     *
     * A single contact may appear in multiple groups when it shares more than
     * one signal with its duplicates.
     */
    public function find(): array
    {
        return [
            ...$this->byEmail(),
            ...$this->byName(),
            ...$this->byPhone(),
            ...$this->byDobAndLastname(),
        ];
    }

    private function byEmail(): array
    {
        $duplicateEmails = \DB::table('contact_emails')
            ->join('contacts', 'contacts.id', '=', 'contact_emails.contact_id')
            ->where('contacts.team_id', $this->teamId)
            ->whereNotNull('contact_emails.email')
            ->where('contact_emails.email', '!=', '')
            ->selectRaw('LOWER(contact_emails.email) AS email_lc')
            ->groupBy('email_lc')
            ->havingRaw('COUNT(DISTINCT contacts.id) > 1')
            ->pluck('email_lc');

        return $duplicateEmails->map(function ($email) {
            $contacts = Contact::query()
                ->withoutGlobalScopes()
                ->where('team_id', $this->teamId)
                ->whereHas('emails', fn ($q) => $q->whereRaw('LOWER(email) = ?', [$email]))
                ->with('emails')
                ->orderBy('lastname')
                ->get();

            return [
                'signal' => 'email',
                'value' => $email,
                'contacts' => $this->summarise($contacts),
            ];
        })->all();
    }

    private function byName(): array
    {
        $pairs = Contact::query()
            ->withoutGlobalScopes()
            ->where('team_id', $this->teamId)
            ->whereNotNull('firstname')
            ->whereNotNull('lastname')
            ->where('firstname', '!=', '')
            ->where('lastname', '!=', '')
            ->selectRaw('LOWER(TRIM(firstname)) AS first_lc, LOWER(TRIM(lastname)) AS last_lc')
            ->groupBy('first_lc', 'last_lc')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        return $pairs->map(function ($row) {
            $contacts = Contact::query()
                ->withoutGlobalScopes()
                ->where('team_id', $this->teamId)
                ->whereRaw('LOWER(TRIM(firstname)) = ?', [$row->first_lc])
                ->whereRaw('LOWER(TRIM(lastname)) = ?', [$row->last_lc])
                ->orderBy('id')
                ->get();

            return [
                'signal' => 'name',
                'value' => trim("{$row->first_lc} {$row->last_lc}"),
                'contacts' => $this->summarise($contacts),
            ];
        })->all();
    }

    private function byPhone(): array
    {
        // MySQL 5.7 lacks REGEXP_REPLACE, so do the normalisation in PHP and
        // bucket contact_ids by normalised phone. Per-tenant rowcounts make
        // this cheap.
        $rows = \DB::table('contact_numbers')
            ->join('contacts', 'contacts.id', '=', 'contact_numbers.contact_id')
            ->where('contacts.team_id', $this->teamId)
            ->whereNotNull('contact_numbers.number')
            ->where('contact_numbers.number', '!=', '')
            ->select('contacts.id AS contact_id', 'contact_numbers.number')
            ->get();

        $byNorm = [];
        foreach ($rows as $row) {
            $norm = preg_replace('/[^0-9+]/', '', (string) $row->number);
            if (strlen($norm) < 5) {
                continue; // skip short/junk values
            }
            $byNorm[$norm][$row->contact_id] = true;
        }

        $groups = [];
        foreach ($byNorm as $norm => $contactIds) {
            if (count($contactIds) < 2) {
                continue;
            }
            $contacts = Contact::query()
                ->withoutGlobalScopes()
                ->whereIn('id', array_keys($contactIds))
                ->with('numbers')
                ->orderBy('lastname')
                ->get();

            $groups[] = [
                'signal' => 'phone',
                'value' => $norm,
                'contacts' => $this->summarise($contacts),
            ];
        }

        return $groups;
    }

    private function byDobAndLastname(): array
    {
        $pairs = Contact::query()
            ->withoutGlobalScopes()
            ->where('team_id', $this->teamId)
            ->whereNotNull('date_of_birth')
            ->whereNotNull('lastname')
            ->where('lastname', '!=', '')
            ->selectRaw('date_of_birth, LOWER(TRIM(lastname)) AS last_lc')
            ->groupBy('date_of_birth', 'last_lc')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        return $pairs->map(function ($row) {
            $contacts = Contact::query()
                ->withoutGlobalScopes()
                ->where('team_id', $this->teamId)
                ->where('date_of_birth', $row->date_of_birth)
                ->whereRaw('LOWER(TRIM(lastname)) = ?', [$row->last_lc])
                ->orderBy('id')
                ->get();

            return [
                'signal' => 'dob_lastname',
                'value' => "{$row->last_lc} / {$row->date_of_birth}",
                'contacts' => $this->summarise($contacts),
            ];
        })->all();
    }

    /**
     * Slim down to the fields the UI needs on the index page.
     */
    private function summarise(Collection $contacts): array
    {
        return $contacts->map(fn (Contact $c) => [
            'ulid' => $c->ulid,
            'firstname' => $c->firstname,
            'lastname' => $c->lastname,
            'company' => $c->company,
            'date_of_birth' => $c->date_of_birth,
            'emails' => $c->relationLoaded('emails')
                ? $c->emails->pluck('email')->all()
                : [],
            'numbers' => $c->relationLoaded('numbers')
                ? $c->numbers->pluck('number')->all()
                : [],
        ])->all();
    }
}
