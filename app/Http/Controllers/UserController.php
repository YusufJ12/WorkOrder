<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $roles = Role::all();
        return view('users.index', compact('roles'));
    }

    public function getUserData()
    {
        $users = DB::table('users')
            ->select('users.id', 'users.name', 'users.email', 'users.password', 'users.type', 'roles.nm_roles')
            ->join('roles', 'users.type', '=', 'roles.id')
            ->get();

        return datatables()->of($users)->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'roles' => 'required',
            'password' => 'required|string|min:4',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->type = $request->roles;
        $user->save();

        return response()->json(['success' => 'User berhasil ditambahkan']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => 'User berhasil dihapus']);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->type = $request->roles;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['success' => 'User berhasil diperbarui']);
    }
}
