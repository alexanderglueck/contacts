<div class="nav flex-column nav-pills">
    <a class="nav-link " href="{{ route('user_settings.edit') }}">
        Settings
    </a>

    <a class="nav-link {{ return_if(on_page('*/profile'), ' active') }}" href="{{ route('user_settings.profile.show') }}">
        Profile
    </a>

    <a class="nav-link {{ return_if(on_page('*/password'), ' active') }}" href="{{ route('user_settings.password.show') }}">
        Change password
    </a>
</div>
