<?php
// app/Http/Controllers/FormPublicController.php

namespace App\Http\Controllers;

use App\Http\Requests\FormSubmissionRequest;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\FormSubmissionFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class FormPublicController extends Controller
{
    public function show(Form $form): Response|RedirectResponse
    {
        if (auth()->check() && !$form->isTargetedAt(auth()->user()->role)) {
            abort(404);
        }

        if ($response = $this->closedResponse($form)) {
            return $response;
        }

        if ($form->needsLogin() && !auth()->check()) {
            return redirect()->guest(route('login', ['redirect' => route('form.public.show', $form->slug)]));
        }

        $userId = auth()->id();

        if ($form->allow_multiple_submissions && $userId) {
            return $this->renderSubmissionList($form);
        }

        if (!$form->allow_multiple_submissions && $userId) {
            $submission = $form->submissions()->where('user_id', $userId)->first();
            if ($submission) {
                return redirect()->route('form.public.review', [$form->slug, $submission->id]);
            }
        }

        return $this->renderFillForm($form);
    }

    protected function renderSubmissionList(Form $form): Response
    {
        $submissions = $form->submissionsBy(auth()->id())->get(['id', 'submission_token', 'submitted_at']);

        return Inertia::render('Form/SubmissionList', [
            'form' => $form->only('id', 'title', 'slug', 'description', 'banner_image', 'end_at', 'status'),
            'submissions' => $submissions,
        ]);
    }

    // ---------------------------------------------------------------
    // URL helper (guest memakai signed URL, user login memakai route biasa)
    // ---------------------------------------------------------------

    protected function signed(string $route, Form $form, FormSubmission $submission): string
    {
        return URL::temporarySignedRoute($route, now()->addDays(5), [
            'form' => $form->slug,
            'submission' => $submission->id,
        ]);
    }

    protected function reviewUrl(Form $form, FormSubmission $submission): string
    {
        return $submission->user_id
            ? route('form.public.review', [$form->slug, $submission->id])
            : $this->signed('form.public.review.guest', $form, $submission);
    }

    protected function editUrl(Form $form, FormSubmission $submission): string
    {
        return $submission->user_id
            ? route('form.public.edit', [$form->slug, $submission->id])
            : $this->signed('form.public.edit.guest', $form, $submission);
    }

    protected function updateUrl(Form $form, FormSubmission $submission): string
    {
        return $submission->user_id
            ? route('form.public.update', [$form->slug, $submission->id])
            : $this->signed('form.public.update.guest', $form, $submission);
    }

    protected function closedResponse(Form $form): ?Response
    {
        $reason = $form->closedReason();

        if ($reason === null) {
            return null;
        }

        return Inertia::render('Form/Closed', [
            'form' => $form->only('title', 'description', 'banner_image', 'status', 'start_at', 'end_at'),
            'reason' => $reason,
        ]);
    }

    // ---------------------------------------------------------------
    // Isi / edit form
    // ---------------------------------------------------------------

    protected function renderFillForm(Form $form, ?FormSubmission $submission = null, bool $isGuestEdit = false): Response
    {
        $form->load([
            'sections.fields' => fn($q) => $q->orderBy('sort_order'),
            'fields' => fn($q) => $q->whereNull('section_id')->orderBy('sort_order'),
        ]);

        if ($submission) {
            $submission->load('files.field');
        }

        return Inertia::render('Form/Show', [
            'form' => $form,
            'submission' => $submission ? [
                'id' => $submission->id,
                'data' => $submission->data,
                'files' => $submission->files->mapWithKeys(fn($f) => [
                    $f->field->name => ['original_name' => $f->original_name, 'url' => $f->url()],
                ]),
            ] : null,
            'updateUrl' => $submission ? $this->updateUrl($form, $submission) : null,
            'reviewUrl' => $submission ? $this->reviewUrl($form, $submission) : null,
            'isGuestEdit' => $isGuestEdit,
        ]);
    }

    public function create(Form $form): Response|RedirectResponse
    {
        if ($response = $this->closedResponse($form)) {
            return $response;
        }
        abort_unless($form->allow_multiple_submissions, 403);

        if ($form->needsLogin() && !auth()->check()) {
            return redirect()->guest(route('login'));
        }

        return $this->renderFillForm($form);
    }

    public function edit(Form $form, FormSubmission $submission): Response
    {
        if ($response = $this->closedResponse($form)) {
            return $response;
        }
        Gate::authorize('update', $submission);

        return $this->renderFillForm($form, $submission);
    }

    public function editGuest(Form $form, FormSubmission $submission): Response
    {
        if ($response = $this->closedResponse($form)) {
            return $response;
        }
        abort_if($submission->user_id !== null, 403);

        return $this->renderFillForm($form, $submission, isGuestEdit: true);
    }

    // ---------------------------------------------------------------
    // Review (preview jawaban, read-only)
    // ---------------------------------------------------------------

    public function review(Form $form, FormSubmission $submission): Response
    {
        Gate::authorize('update', $submission);

        return $this->renderReview($form, $submission);
    }

    public function reviewGuest(Form $form, FormSubmission $submission): Response
    {
        abort_if($submission->user_id !== null, 403);

        return $this->renderReview($form, $submission, isGuest: true);
    }

    protected function renderReview(Form $form, FormSubmission $submission, bool $isGuest = false): Response
    {
        $form->load([
            'sections.fields' => fn($q) => $q->orderBy('sort_order'),
            'fields' => fn($q) => $q->whereNull('section_id')->orderBy('sort_order'),
        ]);
        $submission->load('files.field');

        return Inertia::render('Form/Review', [
            'form' => $form,
            'submission' => [
                'id' => $submission->id,
                'submission_token' => $submission->submission_token,
                'submitted_at' => $submission->submitted_at,
                'data' => $submission->data,
                'files' => $submission->files->mapWithKeys(fn($f) => [
                    $f->field->name => ['original_name' => $f->original_name, 'url' => $f->url()],
                ]),
            ],
            'canEdit' => $form->closedReason() === null,
            'editUrl' => $this->editUrl($form, $submission),
            'isGuest' => $isGuest,
        ]);
    }

    // ---------------------------------------------------------------
    // Simpan / update
    // ---------------------------------------------------------------

    public function update(FormSubmissionRequest $request, Form $form, FormSubmission $submission): RedirectResponse
    {
        Gate::authorize('update', $submission);
        abort_if($form->closedReason() !== null, 403, 'Form sudah ditutup.');

        return $this->processUpdate($request, $form, $submission);
    }

    public function updateGuest(FormSubmissionRequest $request, Form $form, FormSubmission $submission): RedirectResponse
    {
        abort_if($submission->user_id !== null, 403);
        abort_if($form->closedReason() !== null, 403, 'Form sudah ditutup.');

        return $this->processUpdate($request, $form, $submission);
    }

    public function store(FormSubmissionRequest $request, Form $form): RedirectResponse|Response
    {
        if ($response = $this->closedResponse($form)) {
            return $response;
        }

        $validated = $request->validated();
        $data = $validated['data'] ?? [];
        $files = $validated['files'] ?? [];

        $submission = DB::transaction(function () use ($form, $request, $data, $files) {
            $submission = FormSubmission::create([
                'form_id' => $form->id,
                'user_id' => $request->user()?->id,
                'data' => $data,
                'submitted_at' => now(),
            ]);

            $this->storeFiles($form, $submission, $files);

            return $submission;
        });

        return $this->redirectWithSuccessFlash($form, $submission);
    }

    protected function processUpdate(FormSubmissionRequest $request, Form $form, FormSubmission $submission): RedirectResponse
    {
        $validated = $request->validated();
        $data = $validated['data'] ?? [];
        $files = $validated['files'] ?? [];

        DB::transaction(function () use ($form, $submission, $data, $files) {
            $submission->update(['data' => $data, 'submitted_at' => now()]);
            $this->storeFiles($form, $submission, $files, replace: true);
        });

        return $this->redirectWithSuccessFlash($form, $submission, isUpdate: true);
    }

    /**
     * Susun data flash untuk modal sukses, lalu arahkan ke halaman review
     * (atau ke daftar jawaban untuk form multi-submit).
     */
    protected function redirectWithSuccessFlash(Form $form, FormSubmission $submission, bool $isUpdate = false): RedirectResponse
    {
        $payload = [
            'is_update' => $isUpdate,
            'submission_token' => $submission->submission_token,
        ];

        $toList = $form->allow_multiple_submissions && auth()->check();

        if ($form->allow_multiple_submissions) {
            $payload['list_url'] = auth()->check() ? route('form.public.show', $form->slug) : null;
        } else {
            $payload['review_url'] = $this->reviewUrl($form, $submission);
            $payload['is_guest_link'] = $submission->user_id === null;
        }

        $redirectTo = $toList
            ? route('form.public.show', $form->slug)
            : $this->reviewUrl($form, $submission);

        return redirect($redirectTo)->with('success_submission', $payload);
    }

    protected function storeFiles(Form $form, FormSubmission $submission, array $files, bool $replace = false): void
    {
        foreach ($files as $fieldName => $uploadedFile) {
            if (!$uploadedFile) continue;

            $field = $form->fields()->where('name', $fieldName)->first();
            if (!$field) continue;

            if ($replace) {
                $old = $submission->files()->where('field_id', $field->id)->first();
                $old?->delete();
            }

            $path = $uploadedFile->store("forms/{$form->id}/{$submission->submission_token}", 'public');

            FormSubmissionFile::create([
                'submission_id' => $submission->id,
                'field_id' => $field->id,
                'original_name' => $uploadedFile->getClientOriginalName(),
                'path' => $path,
                'disk' => 'public',
                'mime_type' => $uploadedFile->getClientMimeType(),
                'size' => $uploadedFile->getSize(),
            ]);
        }
    }
}