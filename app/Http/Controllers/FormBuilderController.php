<?php
// app/Http/Controllers/FormBuilderController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Models\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\UpdateFormStructureRequest;
use App\Models\FormField;
use App\Models\FormSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormBuilderController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Form::class);

        $user = $request->user();

        $forms = Form::query()
            ->when(!$user->isAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->withCount('submissions')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('FormBuilder/Index', [
            'forms' => $forms,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Form::class);

        return Inertia::render('FormBuilder/Create');
    }

    public function store(StoreFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')
                ->store('forms/banners', 'public');
        }

        $data['created_by'] = $request->user()->id;
        $data['school_id'] = $request->user()->school_id;

        $form = Form::create($data);

        return redirect()
            ->route('forms.edit', $form)
            ->with('success', 'Form berhasil dibuat. Sekarang tambahkan field.');
    }

    public function edit(Form $form): Response
    {
        Gate::authorize('update', $form);

        // Sections + fields dimuat sekaligus untuk halaman builder
        $form->load(['sections.fields', 'fields' => fn($q) => $q->whereNull('section_id')]);

        return Inertia::render('FormBuilder/Edit', [
            'form' => $form,
        ]);
    }

    public function update(UpdateFormRequest $request, Form $form): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('banner_image')) {
            if ($form->banner_image) {
                Storage::disk('public')->delete($form->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')
                ->store('forms/banners', 'public');
        }

        $form->update($data);

        return back()->with('success', 'Form berhasil diperbarui.');
    }

    public function destroy(Form $form): RedirectResponse
    {
        Gate::authorize('delete', $form);

        $form->delete(); // soft delete

        return redirect()
            ->route('forms.index')
            ->with('success', 'Form berhasil dihapus.');
    }

    public function updateStructure(UpdateFormStructureRequest $request, Form $form): \Illuminate\Http\RedirectResponse
    {

        $data = $request->validated();
        $usedNames = [];

        DB::transaction(function () use ($data, $form, &$usedNames) {
            $keepSectionIds = [];
            $keepFieldIds = [];

            foreach ($data['sections'] ?? [] as $sectionData) {
                $section = FormSection::updateOrCreate(
                    ['id' => $sectionData['id'] ?? null, 'form_id' => $form->id],
                    [
                        'title' => $sectionData['title'],
                        'description' => $sectionData['description'] ?? null,
                        'sort_order' => $sectionData['sort_order'],
                    ]
                );
                $keepSectionIds[] = $section->id;

                foreach ($sectionData['fields'] ?? [] as $fieldData) {
                    $name = $this->resolveFieldName($form, $fieldData['name'] ?? null, $fieldData['label'], $usedNames, $fieldData['id'] ?? null);

                    $field = FormField::updateOrCreate(
                        ['id' => $fieldData['id'] ?? null, 'form_id' => $form->id],
                        [
                            'section_id' => $section->id,
                            'type' => $fieldData['type'],
                            'name' => $name,
                            'label' => $fieldData['label'],
                            'description' => $fieldData['description'] ?? null,
                            'config' => empty($fieldData['config']) ? null : $fieldData['config'],
                            'is_required' => $fieldData['is_required'] ?? false,
                            'sort_order' => $fieldData['sort_order'],
                        ]
                    );
                    $keepFieldIds[] = $field->id;
                }
            }

            foreach ($data['fields'] ?? [] as $fieldData) {
                $name = $this->resolveFieldName($form, $fieldData['name'] ?? null, $fieldData['label'], $usedNames, $fieldData['id'] ?? null);

                $field = FormField::updateOrCreate(
                    ['id' => $fieldData['id'] ?? null, 'form_id' => $form->id],
                    [
                        'section_id' => null,
                        'type' => $fieldData['type'],
                        'name' => $name,
                        'label' => $fieldData['label'],
                        'description' => $fieldData['description'] ?? null,
                        'config' => empty($fieldData['config']) ? null : $fieldData['config'],
                        'is_required' => $fieldData['is_required'] ?? false,
                        'sort_order' => $fieldData['sort_order'],
                    ]
                );
                $keepFieldIds[] = $field->id;
            }

            $form->sections()->whereNotIn('id', $keepSectionIds)->delete();
            $form->fields()->whereNotIn('id', $keepFieldIds)->delete();
        });

        return back()->with('success', 'Struktur form berhasil disimpan.');
    }

    // tambahkan method private ini di dalam FormBuilderController
    private function resolveFieldName(Form $form, ?string $rawName, string $label, array &$usedNames, ?int $currentFieldId): string
    {
        $base = $rawName ? Str::slug($rawName, '_') : Str::slug($label, '_');
        $base = $base ?: 'field';
        $name = $base;
        $i = 1;

        $nameTaken = function (string $candidate) use ($form, $usedNames, $currentFieldId) {
            if (in_array($candidate, $usedNames, true)) {
                return true;
            }
            return $form->fields()
                ->where('name', $candidate)
                ->when($currentFieldId, fn($q) => $q->where('id', '!=', $currentFieldId))
                ->exists();
        };

        while ($nameTaken($name)) {
            $name = "{$base}_{$i}";
            $i++;
        }

        $usedNames[] = $name;
        return $name;
    }
}
