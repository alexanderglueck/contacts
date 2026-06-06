<?php

namespace App\Console\Commands\Migration;

use App\Models\ContactDate;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-off data migration: the legacy app stored birthdays as ContactDate rows
 * named "Geburtstag". The rewrite has a dedicated Contact::$date_of_birth
 * field. This command copies each "Geburtstag" date into date_of_birth (when
 * empty) and removes the now-redundant ContactDate so birthdays aren't shown
 * twice in mail / iCal / dashboard.
 *
 * Idempotent: re-running finds nothing once migrated.
 */
class MigrateBirthdays extends Command
{
    protected $signature = 'contacts:migrate-birthdays
                            {--dry-run : Report what would change without writing}';

    protected $description = 'Move "Geburtstag" contact dates into the contacts.date_of_birth field.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry run — no changes will be written.');
        }

        $migrated = 0;
        $duplicatesRemoved = 0;
        $skipped = 0;
        $orphans = 0;

        // ContactDate has no tenant global scope, and Contact's scope no-ops in
        // CLI (no authenticated user), so this sweeps every team's data.
        $birthdayDates = ContactDate::with('contact')
            ->whereRaw('LOWER(name) = ?', ['geburtstag'])
            ->get();

        $this->info("Found {$birthdayDates->count()} \"Geburtstag\" contact date(s).");

        foreach ($birthdayDates as $contactDate) {
            $contact = $contactDate->contact;

            if (! $contact) {
                $orphans++;
                $this->warn("ContactDate #{$contactDate->id} has no contact — skipped.");

                continue;
            }

            $birthday = $this->extractDate($contactDate->date);

            if ($birthday === null) {
                $skipped++;
                $this->warn("ContactDate #{$contactDate->id} has an unparseable date '{$contactDate->date}' — skipped.");

                continue;
            }

            $existing = $this->extractDate($contact->getRawOriginal('date_of_birth'));

            if ($existing !== null && $existing !== $birthday) {
                $skipped++;
                $this->warn(
                    "Contact #{$contact->id} ({$contact->fullname}) already has date_of_birth "
                    ."{$existing}, differs from Geburtstag {$birthday} — left untouched."
                );

                continue;
            }

            if ($existing === $birthday) {
                // Birthday already on the contact — just drop the duplicate row.
                $duplicatesRemoved++;
                $this->line("Contact #{$contact->id}: duplicate Geburtstag row removed.");

                if (! $dryRun) {
                    ContactDate::whereKey($contactDate->id)->delete();
                }

                continue;
            }

            $migrated++;
            $this->line("Contact #{$contact->id} ({$contact->fullname}): date_of_birth ← {$birthday}");

            if (! $dryRun) {
                // Direct write bypasses model events (activity log / auth) which
                // aren't available in CLI; stores the canonical Y-m-d form.
                DB::table('contacts')->where('id', $contact->id)->update(['date_of_birth' => $birthday]);
                ContactDate::whereKey($contactDate->id)->delete();
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->table(
            ['Migrated', 'Duplicates removed', 'Skipped', 'Orphans'],
            [[$migrated, $duplicatesRemoved, $skipped, $orphans]],
        );

        if ($dryRun) {
            $this->warn('Dry run complete — nothing was written.');
        }

        return self::SUCCESS;
    }

    /**
     * Normalize a stored date (Y-m-d H:i:s, Y-m-d, or null/empty) to Y-m-d,
     * or null when absent/unparseable.
     */
    private function extractDate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $date = date_create_from_format('Y-m-d H:i:s', $value)
            ?: date_create_from_format('Y-m-d', $value)
            ?: (DateTime::createFromFormat('Y-m-d', substr($value, 0, 10)) ?: null);

        return $date ? $date->format('Y-m-d') : null;
    }
}
