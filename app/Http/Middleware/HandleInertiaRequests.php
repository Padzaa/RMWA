<?php

namespace App\Http\Middleware;

use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = Auth::user();
        return array_merge(parent::share($request), [
            "auth" => $user ? [
                'user' => [
                    "id" => $user->id,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "email" => $user->email,
                    "picture" => $user->picture ? asset($user->picture) : null,
                    "is_admin" => $user->is_admin
                ]
            ] : null,
            'administrator' => $user && $user->is_admin ? true : false,
            'alertFlash' => $request->session()->get('alert'),
            'canRequestTechnicalSupport' => $user && !$user->is_admin && !Notification::technicalSupportRequestsByUser($user)->where('tsr_status', null)->exists(),
            'pendingRequests' => $user && !$user->is_admin ? Notification::technicalSupportRequestsByUser($user)->where('tsr_status', null)->exists() : null,
            'acceptedRequests' => $user && !$user->is_admin ? Notification::technicalSupportRequestsByUser($user)->where('tsr_status', 'accepted')->exists() : null,
            'technicalSupportRequests' => $user && $user->is_admin ? $user->technicalSupportRequests()->where(function ($query) {
                $query->where('tsr_status', null)->orWhere('tsr_status', 'accepted');
            })->get() : null,
            'currentOperator' => $user && $user->is_admin && Notification::technicalSupportRequestsByUser($user)->where('tsr_status', 'accepted')->first() !== null
                ? Notification::technicalSupportRequestsByUser($user)->where('tsr_status', 'accepted')->first()->user()->first() : []
        ]);
    }
}
