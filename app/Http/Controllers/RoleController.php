<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Role\RoleStoreRequest;
use App\Http\Requests\Role\RoleUpdateRequest;

class RoleController extends Controller
{
    protected ?string $accessEntity = 'roles';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->can('view');

        return view('role.index', [
            'roles' => Role::paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->can('create');

        return view('role.create', [
            'permissions' => Permission::all(),
            'users' => $request->user()->currentTeam->users,
            'role' => new Role
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $role = new Role();
        $role->fill($request->except(['permissions', 'users']));
        $role->team_id = $request->user()->currentTeam->id;

        if ($role->save()) {
            $role->syncPermissions($request->permissions);

            $role->syncUsers($request->users);

            Session::flash('alert-success', trans('flash_message.role.created'));

            return redirect()->route('roles.show', [$role->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.role.not_created'));

            return redirect()->route('roles.create');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View
    {
        $this->can('view');

        return view('role.show', [
            'role' => $role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Role $role): View
    {
        $this->can('edit');

        return view('role.edit', [
            'role' => $role,
            'createButtonText' => trans('ui.edit_role'),
            'users' => $request->user()->currentTeam->users,
            'permissions' => Permission::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUpdateRequest $request, Role $role): RedirectResponse
    {
        $role->fill($request->except(['users', 'permissions']));

        $role->syncPermissions($request->permissions);

        $role->syncUsers($request->users);

        if ($role->save()) {
            Session::flash('alert-success', trans('flash_message.role.updated'));

            return redirect()->route('roles.show', [$role->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.role.not_updated'));

            return redirect()->route('roles.edit', [$role->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->can('delete');

        if ($role->delete()) {
            Session::flash('alert-success', trans('flash_message.role.deleted'));

            return redirect()->route('roles.index');
        } else {
            Session::flash('alert-danger', trans('flash_message.role.not_deleted'));

            return redirect()->route('roles.delete', [$role->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Role $role): View
    {
        $this->can('delete');

        return view('role.delete', [
            'role' => $role
        ]);
    }
}
