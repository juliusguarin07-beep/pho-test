<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        // Load relationships if user exists
        if ($user) {
            $user->load(['facility', 'municipality']);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'position' => $user->position,
                    'user_type' => $user->user_type,
                    'facility_id' => $user->facility_id,
                    'municipality_id' => $user->municipality_id,
                    'facility' => $user->facility ? [
                        'id' => $user->facility->id,
                        'name' => $user->facility->name,
                        'type' => $user->facility->type,
                    ] : null,
                    'municipality' => $user->municipality ? [
                        'id' => $user->municipality->id,
                        'name' => $user->municipality->name,
                    ] : null,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ] : null,
            ],
        ];
    }
}
