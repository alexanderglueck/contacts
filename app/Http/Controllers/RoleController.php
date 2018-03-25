<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    private $validationRules = [
        'name' => 'required',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('role.index', [
            'roles' => Role::paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('role.create', [
            'role' => new Role
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->validationRules);

        $role = new Role();
        $role->fill($request->all());
        $role->team_id = Auth::user()->currentTeam->id;

        if ($role->save()) {
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
        return view('role.show', [
            'role' => $role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('role.edit', [
            'role' => $role,
            'createButtonText' => trans('ui.edit_role'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Role         $role
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $this->validate($request, $this->validationRules);

        $role->fill($request->all());

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
     */
    public function destroy(Role $role)
    {
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
        return view('role.delete', [
            'role' => $role
        ]);
    }
}
