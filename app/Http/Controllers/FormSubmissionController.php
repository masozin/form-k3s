<?php
// app/Http/Controllers/FormSubmissionController.php

namespace App\Http\Controllers;

use App\Exports\FormSubmissionsExport;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Services\FormFileExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;


class FormSubmissionController extends Controller
{
    public function index(Request $request, Form $form): Response
    {
        Gate::authorize('view', $form);

        $search = $request->string('search')->toString();

        $submissions = $form->submissions()
            ->with('user:id,name,email,school_id', 'user.school:id,name')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhereRaw('CAST(data AS CHAR) LIKE ?', ["%{$search}%"]);
                });
            })
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        $submissions->getCollection()->transform(function ($submission) use ($form) {
            return [
                'id' => $submission->id,
                'submission_token' => $submission->submission_token,
                'submitted_at' => $submission->submitted_at,
                'user' => $submission->user ? [
                    'name' => $submission->user->name,
                    'email' => $submission->user->email,
                    'school_name' => $submission->user->school?->name,
                ] : null,
                'preview' => $this->buildPreview($form, $submission->data),
            ];
        });

        return Inertia::render('FormBuilder/Submissions/Index', [
            'form' => $form->only('id', 'title', 'slug', 'allow_multiple_submissions'),
            'submissions' => $submissions,
            'filters' => ['search' => $search],
            'totalSchoolsSubmitted' => $form->submissions()
                ->whereHas('user')
                ->with('user:id,school_id')
                ->get()
                ->pluck('user.school_id')
                ->filter()
                ->unique()
                ->count(),
        ]);
    }

    public function show(Form $form, FormSubmission $submission): Response
    {
        Gate::authorize('view', $form);

        $submission->load('files.field', 'user.school');

        $form->load([
            'sections.fields' => fn($q) => $q->orderBy('sort_order'),
            'fields' => fn($q) => $q->whereNull('section_id')->orderBy('sort_order'),
        ]);

        return Inertia::render('FormBuilder/Submissions/Show', [
            'form' => $form,
            'submission' => [
                'id' => $submission->id,
                'submission_token' => $submission->submission_token,
                'submitted_at' => $submission->submitted_at,
                'data' => $submission->data,
                'user' => $submission->user ? [
                    'name' => $submission->user->name,
                    'email' => $submission->user->email,
                    'school_name' => $submission->user->school?->name,
                ] : null,
                'files' => $submission->files->mapWithKeys(fn($f) => [
                    $f->field->name => [
                        'original_name' => $f->original_name,
                        'url' => $f->url(),
                        'size' => $f->humanSize(),
                    ],
                ]),
            ],
        ]);
    }

    /**
     * Ambil 2-3 jawaban pertama sebagai cuplikan di daftar (bukan field file/heading/paragraph).
     */
    protected function buildPreview(Form $form, array $data): array
    {
        $fields = $form->fields->merge($form->sections->flatMap->fields)
            ->whereNotIn('type', ['heading', 'paragraph', 'file'])
            ->sortBy('sort_order')
            ->take(3);

        return $fields->map(function ($field) use ($data) {
            $value = $data[$field->name] ?? null;
            if (is_array($value)) $value = implode(', ', $value);

            return [
                'label' => $field->label,
                'value' => $value ?: '—',
            ];
        })->values()->all();
    }

    public function export(Form $form)
    {
        Gate::authorize('view', $form);

        $filename = Str::slug($form->title) . '-jawaban-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new FormSubmissionsExport($form), $filename);
    }

    public function exportFiles(Form $form, FormFileExportService $exportService)
    {
        Gate::authorize('view', $form);

        $zipPath = $exportService->export($form);

        $downloadName = Str::slug($form->title) . '-files-' . now()->format('Y-m-d') . '.zip';

        return response()->download($zipPath, $downloadName)->deleteFileAfterSend(true);
    }
}
