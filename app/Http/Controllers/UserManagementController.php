<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Facility;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'facility', 'municipality'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $municipalities = Municipality::orderBy('name')->get();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'facilities' => $facilities,
            'municipalities' => $municipalities,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'position' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'user_type' => 'required|in:encoder,validator,pesu_admin',
            'role' => 'required|exists:roles,name',
            'facility_id' => $request->user_type === 'encoder' || $request->user_type === 'validator' ? 'required|exists:facilities,id' : 'nullable|exists:facilities,id',
            'municipality_id' => $request->user_type === 'encoder' || $request->user_type === 'validator' ? 'required|exists:municipalities,id' : 'nullable|exists:municipalities,id',
            'is_active' => 'boolean',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'position' => $validated['position'],
            'contact_number' => $validated['contact_number'] ?? null,
            'user_type' => $validated['user_type'],
            'facility_id' => $validated['facility_id'] ?? null,
            'municipality_id' => $validated['municipality_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();
        $municipalities = Municipality::orderBy('name')->get();

        // Load user relationships
        $user->load(['roles', 'facility', 'municipality']);

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'facilities' => $facilities,
            'municipalities' => $municipalities,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'position' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'user_type' => 'required|in:encoder,validator,pesu_admin',
            'role' => 'required|exists:roles,name',
            'facility_id' => $request->user_type === 'encoder' || $request->user_type === 'validator' ? 'required|exists:facilities,id' : 'nullable|exists:facilities,id',
            'municipality_id' => $request->user_type === 'encoder' || $request->user_type === 'validator' ? 'required|exists:municipalities,id' : 'nullable|exists:municipalities,id',
            'is_active' => 'boolean',
        ]);

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'],
            'contact_number' => $validated['contact_number'],
            'user_type' => $validated['user_type'],
            'facility_id' => $validated['facility_id'],
            'municipality_id' => $validated['municipality_id'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Update role
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // TODO: Implement destroy method
    }

    public function toggleStatus(User $user)
    {
        // Prevent self-deactivation
        if (auth()->id() === $user->id) {
            return redirect()->back()
                ->with('error', 'You cannot deactivate your own account.');
        }

        // Toggle the status
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $action = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "User {$user->name} has been {$action} successfully.");
    }
}
