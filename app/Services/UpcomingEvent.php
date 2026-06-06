<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ContactDate;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * A normalized recurring event — either a contact's birthday (from
 * Contact::$date_of_birth) or an important date ("Wichtige Termine",
 * a ContactDate row). Surfaces (dashboard, mail, push, iCal feed) consume
 * this shape so they never have to special-case the two underlying models.
 */
class UpcomingEvent
{
    public const TYPE_BIRTHDAY = 'birthday';

    public const TYPE_DATE = 'date';

    public const TYPE_MEMORIAL = 'memorial';

    public function __construct(
        public readonly string $type,
        public readonly Model $model,
        public readonly Contact $contact,
        public readonly string $uid,
        public readonly DateTimeImmutable $date,
    ) {}

    public static function fromContact(Contact $contact): self
    {
        return new self(
            type: self::TYPE_BIRTHDAY,
            model: $contact,
            contact: $contact,
            uid: 'birthday-'.$contact->id,
            date: new DateTimeImmutable($contact->date_of_birth),
        );
    }

    public static function fromContactMemorial(Contact $contact): self
    {
        return new self(
            type: self::TYPE_MEMORIAL,
            model: $contact,
            contact: $contact,
            uid: 'memorial-'.$contact->id,
            date: new DateTimeImmutable($contact->died_at),
        );
    }

    public static function fromContactDate(ContactDate $contactDate): self
    {
        return new self(
            type: self::TYPE_DATE,
            model: $contactDate,
            contact: $contactDate->contact,
            uid: 'date-'.$contactDate->id,
            date: new DateTimeImmutable($contactDate->date),
        );
    }

    public function isBirthday(): bool
    {
        return $this->type === self::TYPE_BIRTHDAY;
    }

    public function isMemorial(): bool
    {
        return $this->type === self::TYPE_MEMORIAL;
    }

    public function fullname(): string
    {
        return $this->contact->fullname;
    }

    /**
     * The full calendar title for a display year, e.g. "31. Geburtstag\nMax
     * Mustermann" — delegates to the right model method for the event type.
     */
    public function calculatedName(int $year): string
    {
        return $this->isMemorial()
            ? $this->contact->getDeathCalculatedName($year)
            : $this->model->getCalculatedName($year);
    }

    /**
     * The event label for a display year with the contact name peeled off,
     * e.g. "31. Geburtstag" or "Hochzeitstag".
     */
    public function label(int $year): string
    {
        return trim(str_replace($this->fullname(), '', $this->calculatedName($year)));
    }

    /**
     * The human-formatted source date (d.m.Y, or d.m. when the year is hidden).
     */
    public function formatted(): string
    {
        return match ($this->type) {
            self::TYPE_BIRTHDAY => $this->contact->formatted_date_of_birth,
            self::TYPE_MEMORIAL => $this->contact->formatted_died_at,
            default => $this->model->formattedDate,
        };
    }

    public function url(): string
    {
        return route('contacts.show', $this->contact->ulid);
    }
}
