<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Role\RoleStoreRequest;
use App\Http\Requests\Role\RoleUpdateRequest;

class RoleController extends Controller
{
    protected ?string $accessEntity = 'roles';

    public function index(): Response
    {
        $this->can('view');

        return Inertia::render('Roles/Index', [
            'roles' => Role::paginate(10)->through(fn ($role) => [
                'id' => $role->id,
                'ulid' => $role->ulid,
                'name' => $role->name,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create roles'),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->can('create');

        return Inertia::render('Roles/Create', [
            'permissions' => Permission::all(['id', 'name']),
            'users' => $request->user()->currentTeam->users->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
            ])->values(),
        ]);
    }

    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $role = new Role();
        $role->fill($request->except(['permissions', 'users']));
        $role->team_id = $request->user()->currentTeam->id;

        if ($role->save()) {
            $role->syncPermissions($this->normaliseIds($request->permissions));

            $role->syncUsers($this->normaliseIds($request->users));

            Session::flash('alert-success', trans('flash_message.role.created'));

            return redirect()->route('roles.show', [$role->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.role.not_created'));

            return redirect()->route('roles.create');
        }
    }

    public function show(Role $role): Response
    {
        $this->can('view');

        $user = Auth::user();
        $permissions = $role->permissions()->get(['permissions.id', 'permissions.name']);
        $users = $role->users()->get(['users.id', 'users.name']);

        return Inertia::render('Roles/Show', [
            'role' => [
                'id' => $role->id,
                'ulid' => $role->ulid,
                'name' => $role->name,
            ],
            'permissions' => $permissions->map(fn ($p) => ['id' => $p->id, 'name' => $p->name]),
            'users' => $users->map(fn ($u) => ['id' => $u->id, 'name' => $u->name]),
            'can' => [
                'edit' => $user->checkPermissionTo('edit roles'),
                'delete' => $user->checkPermissionTo('delete roles'),
            ],
        ]);
    }

    public function edit(Request $request, Role $role): Response
    {
        $this->can('edit');

        return Inertia::render('Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'ulid' => $role->ulid,
                'name' => $role->name,
                'permissions' => $role->permissions()->pluck('permissions.id')->all(),
                'users' => $role->users()->pluck('users.id')->all(),
            ],
            'permissions' => Permission::all(['id', 'name']),
            'users' => $request->user()->currentTeam->users->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
            ])->values(),
        ]);
    }

    public function update(RoleUpdateRequest $request, Role $role): RedirectResponse
    {
        $role->fill($request->except(['users', 'permissions']));

        $role->syncPermissions($this->normaliseIds($request->permissions));

        $role->syncUsers($this->normaliseIds($request->users));

        if ($role->save()) {
            Session::flash('alert-success', trans('flash_message.role.updated'));

            return redirect()->route('roles.show', [$role->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.role.not_updated'));

            return redirect()->route('roles.edit', [$role->ulid]);
        }
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->can('delete');

        if ($role->delete()) {
            Session::flash('alert-success', trans('flash_message.role.deleted'));

            return redirect()->route('roles.index');
        } else {
            Session::flash('alert-danger', trans('flash_message.role.not_deleted'));

            return redirect()->route('roles.delete', [$role->ulid]);
        }
    }

    public function delete(Role $role): Response
    {
        $this->can('delete');

        return Inertia::render('Roles/Delete', [
            'role' => [
                'id' => $role->id,
                'ulid' => $role->ulid,
                'name' => $role->name,
            ],
        ]);
    }

    private function normaliseIds($value): array
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return array_values(array_filter($value, fn ($v) => $v !== null && $v !== ''));
        }

        return [$value];
    }
}
