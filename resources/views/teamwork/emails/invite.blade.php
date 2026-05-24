{{ __('mail.team_invite.body', ['team' => $team->name]) }}<br>
{{ __('mail.team_invite.cta') }} <a href="{{route('teams.accept_invite', $invite->accept_token)}}">{{route('teams.accept_invite', $invite->accept_token)}}</a>
