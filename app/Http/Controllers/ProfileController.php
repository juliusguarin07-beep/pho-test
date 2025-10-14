<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $user->load(['roles', 'facility', 'municipality']);

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'userDetails' => [
                'name' => $user->name,
                'email' => $user->email,
                'position' => $user->position,
                'contact_number' => $user->contact_number,
                'user_type' => $user->user_type,
                'roles' => $user->roles->pluck('name')->toArray(),
                'facility' => $user->facility ? $user->facility->name : null,
                'municipality' => $user->municipality ? $user->municipality->name : null,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Profile information can only be updated by PESU administrators
        // Users cannot modify their own profile details
        return Redirect::route('profile.edit')
            ->with('error', 'Profile information can only be updated by the PESU Administrator. Please contact your system administrator for any changes.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Account deletion can only be performed by PESU administrators
        // Users cannot delete their own accounts
        return Redirect::route('profile.edit')
            ->with('error', 'Account deletion can only be performed by the PESU Administrator. Please contact your system administrator if you need to deactivate your account.');
    }
}
