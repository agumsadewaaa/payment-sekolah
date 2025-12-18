<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Flash;

class UserController extends Controller
{
    public function __construct()
    {
        // Only super-admin can access
        $this->middleware('role:super-admin');
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Filter: hanya tampilkan admin dan user, hide super-admin
        $roles = Role::whereIn('name', ['admin', 'user'])->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        Flash::success('User berhasil ditambahkan.');
        return redirect()->route('users.index');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Filter: hanya tampilkan admin dan user, hide super-admin
        $roles = Role::whereIn('name', ['admin', 'user'])->get();
        
        // Cek apakah user yang login adalah super-admin dan sedang edit diri sendiri
        $isEditingSelf = auth()->user()->hasRole('super-admin') && auth()->id() == $id;
        
        return view('users.edit', compact('user', 'roles', 'isEditingSelf'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update role - kecuali jika super-admin edit diri sendiri
        $isEditingSelf = auth()->user()->hasRole('super-admin') && auth()->id() == $id;
        
        if (!$isEditingSelf) {
            $user->syncRoles([$request->role]);
        }
        // Jika super-admin edit diri sendiri, role tetap super-admin (tidak diubah)

        Flash::success('User berhasil diupdate.');
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            Flash::error('Anda tidak bisa menghapus akun sendiri.');
            return redirect()->route('users.index');
        }

        $user->delete();

        Flash::success('User berhasil dihapus.');
        return redirect()->route('users.index');
    }
}
