<?php
// app/Http/Controllers/Auth/RegisteredUserController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SchoolRegisterRequest;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(SchoolRegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $school = School::create([
                'npsn' => $validated['school_npsn'],
                'name' => $validated['school_name'],
                'address' => $validated['school_address'] ?? null,
                'level' => $validated['school_level'],
                'accreditation' => $validated['school_accreditation'] ?? null,
                'principal_name' => $validated['principal_name'] ?? null,
                'principal_nip' => $validated['principal_nip'] ?? null,
            ]);

            return User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'school_id' => $school->id,
                'role' => User::ROLE_OPERATOR,
            ]);
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
