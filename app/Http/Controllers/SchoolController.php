<?php
// app/Http/Controllers/SchoolController.php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSchoolRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SchoolController extends Controller
{
    public function edit(School $school): Response
    {
        Gate::authorize('view', $school);

        return Inertia::render('Schools/Edit', [
            'school' => $school,
        ]);
    }

    public function update(UpdateSchoolRequest $request, School $school): RedirectResponse
    {
        $school->update($request->validated());

        return back()->with('success', 'Data sekolah berhasil diperbarui.');
    }
}