<div class="nav flex-column nav-pills">
    <a class="nav-link {{ return_if(on_page('*/profile'), ' active') }}" href="{{ route('user_settings.profile.show') }}">
        Profile
    </a>

    <a class="nav-link {{ return_if(on_page('*/password'), ' active') }}" href="{{ route('user_settings.password.show') }}">
        Change password
    </a>

    <a class="nav-link {{ return_if(on_page('*/image'), ' active') }}" href="{{ route('user_settings.image.show') }}">
        Profile image
    </a>

    <a class="nav-link {{ return_if(on_page('*/notifications'), ' active') }}" href="{{ route('user_settings.notifications.show') }}">
        {{ trans('ui.notification_settings') }}
    </a>

    <a class="nav-link {{ return_if(on_page('*/two-factor*'), ' active') }}" href="{{ route('user_settings.two_factor.edit') }}">
        Two-Factor authentication
    </a>

    <a class="nav-link {{ return_if(on_page('*/api-token'), ' active') }}" href="{{ route('user_settings.api_token.show') }}">
        API Token
    </a>

    <a class="nav-link {{ return_if(on_page('*/deactivate'), ' active') }}" href="{{ route('user_settings.deactivate.index') }}">
        Deactivate
    </a>

    <a class="nav-link {{ return_if(on_page('*/delete-account'), ' active') }}" href="{{ route('user_settings.delete.show') }}">
        {{ trans('ui.delete_account') }}
    </a>
</div>

@subscribed
    @notpiggybacksubscription
        <hr>
        <div class="nav flex-column nav-pills">
            @subscriptionnotcancelled
            <a class="nav-link {{ return_if(on_page('*/subscription/swap'), ' active') }}"
               href="{{ route('user_settings.subscription.swap.index') }}"
            >
                Change plan
            </a>

            <a class="nav-link {{ return_if(on_page('*/subscription/cancel'), ' active') }}"
               href="{{ route('user_settings.subscription.cancel.index') }}"
            >
                Cancel subscription
            </a>
            @endsubscriptionnotcancelled

            @subscriptioncancelled
            <a class="nav-link {{ return_if(on_page('*/subscription/resume'), ' active') }}"
               href="{{ route('user_settings.subscription.resume.index') }}"
            >
                Resume subscription
            </a>
            @endsubscriptioncancelled

            <a class="nav-link {{ return_if(on_page('*/subscription/card'), ' active') }}"
               href="{{ route('user_settings.subscription.card.index') }}"
            >
                Update card
            </a>

            @teamsubscription
            <a class="nav-link {{ return_if(on_page('*/subscription/team'), ' active') }}"
               href="{{ route('user_settings.subscription.card.index') }}"
            >
                Manage team
            </a>
            @endteamsubscription
        </div>
    @endnotpiggybacksubscription
@endsubscribed
