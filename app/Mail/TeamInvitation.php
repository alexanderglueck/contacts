<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Mpociot\Teamwork\TeamInvite;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeamInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var TeamInvite
     */
    public $invite;

    /**
     * Create a new message instance.
     *
     * @param TeamInvite $invite
     */
    public function __construct(TeamInvite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Invitation to join team ' . $this->invite->team->name)
            ->view('teamwork.emails.invite', [
                'team' => $this->invite->team
            ]);
    }
}
