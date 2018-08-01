<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Role\RoleStoreRequest;
use App\Http\Requests\Role\RoleUpdateRequest;

class RoleController extends Controller
{
    protected $accessEntity = 'roles';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->can('view');

        return view('role.index', [
            'roles' => Role::paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
     *
     * @param RoleStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $request)
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
     *
     * @param  \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $this->can('view');

        return view('role.show', [
            'role' => $role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request           $request
     * @param  \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Role $role)
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
     *
     * @param RoleUpdateRequest $request
     * @param  \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, Role $role)
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
     *
     * @param  \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Role $role)
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
     *
     * @param  \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Role $role)
    {
        $this->can('delete');

        return view('role.delete', [
            'role' => $role
        ]);
    }
}
