<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return $user->isAdmin()
            ? $this->adminDashboard()
            : $this->operatorDashboard($user); // semua role selain admin (operator, dan role masa depan) pakai ini
    }

    protected function adminDashboard(): Response
    {
        $totalSchools = School::count();
        $totalUsers = User::count();
        $totalForms = Form::count();
        $activeForms = Form::published()->get()->filter->isOpen()->count();
        $totalSubmissions = Form::withCount('submissions')->get()->sum('submissions_count');

        $recentForms = Form::withCount('submissions')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'slug', 'status', 'start_at', 'end_at', 'created_at']);

        $submissionTrend = \App\Models\FormSubmission::query()
            ->where('submitted_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $trendData = collect(range(6, 0))->map(function ($daysAgo) use ($submissionTrend) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            return [
                'date' => now()->subDays($daysAgo)->translatedFormat('d M'),
                'total' => $submissionTrend[$date] ?? 0,
            ];
        });

        return Inertia::render('Dashboard', [
            'role' => 'admin',
            'stats' => compact('totalSchools', 'totalUsers', 'totalForms', 'activeForms', 'totalSubmissions'),
            'recentForms' => $recentForms,
            'trendData' => $trendData,
        ]);
    }

    /**
     * Dashboard untuk operator (dan role pengisi form lainnya di masa depan).
     * Menampilkan: form yang perlu diisi (ditargetkan ke role user ini) + info sekolahnya.
     */
    protected function operatorDashboard(User $user): Response
    {
        $submittedFormIds = $user->submissions()->pluck('form_id')->unique();

        // Form global (semua sekolah) ATAU khusus sekolah user ini,
        // DAN ditargetkan ke role user ini (atau untuk semua role)
        $activeForms = Form::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('school_id')->orWhere('school_id', $user->school_id);
            })
            ->published()
            ->get()
            ->filter->isOpen()
            ->filter(fn($form) => $form->isTargetedAt($user->role))
            ->map(fn($form) => [
                'id' => $form->id,
                'title' => $form->title,
                'slug' => $form->slug,
                'end_at' => $form->end_at,
                'allow_multiple_submissions' => $form->allow_multiple_submissions,
                'has_submitted' => $submittedFormIds->contains($form->id),
            ])
            ->values();

        return Inertia::render('Dashboard', [
            'role' => 'operator',
            'school' => $this->schoolPayload($user->school),
            'stats' => [
                'forms_active' => $activeForms->count(),
                'forms_submitted' => $submittedFormIds->count(),
                'forms_pending' => $activeForms->where('has_submitted', false)->count(),
            ],
            'activeForms' => $activeForms,
        ]);
    }

    protected function schoolPayload($school): ?array
    {
        if (!$school) return null;

        return [
            'id' => $school->id,
            'name' => $school->name,
            'npsn' => $school->npsn,
            'address' => $school->address,
            'level' => $school->level,
            'accreditation' => $school->accreditation,
            'principal_name' => $school->principal_name,
        ];
    }
}
