@if(count($roles)>0)

    {{ $roles->links() }}

    <div class="list-group">
        @foreach($roles as $role)
            <a class="list-group-item  list-group-item-action" href="{{ route('roles.show', [$role->slug]) }}">
                {{ $role->name }}
            </a>
        @endforeach
    </div>

    {{ $roles->links() }}

@else
    <p>{{ trans('ui.no_announcements') }}</p>
@endif
