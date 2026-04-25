<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $fields = Field::with(['creator', 'assignments.agent'])
                ->latest()
                ->get();
        } else {
            $fields = Field::whereHas('assignments', function ($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->with(['creator', 'assignments.agent'])
                ->latest()
                ->get();
        }

        return response()->json($fields);
    }


    public function myFields(): JsonResponse
{
    $fields = Field::whereHas('assignments', function ($q) {
        $q->where('agent_id', auth()->id());
    })->with('assignments')->get();

    return response()->json($fields);
}
    public function store(StoreFieldRequest $request): JsonResponse
    {
        $field = Field::create([
            'name' => $request->name,
            'crop_type' => $request->crop_type,
            'planting_date' => $request->planting_date,
            'stage' => $request->stage ?? 'planted',
            'created_by' => $request->user()->id,
        ]);

        return response()->json($field->load('creator'), 201);
    }

    public function show(Request $request, Field $field): JsonResponse
    {
        if ($request->user()->isAgent()) {
            $isAssigned = $field->assignments()
                ->where('agent_id', $request->user()->id)
                ->exists();

            abort_if(!$isAssigned, 403, 'You are not assigned to this field.');
        }

        return response()->json(
            $field->load(['creator', 'assignments.agent', 'updates.agent'])
        );
    }

    public function update(UpdateFieldRequest $request, Field $field): JsonResponse
    {
        $field->update($request->validated());

        return response()->json($field->fresh()->load('creator'));
    }

    public function destroy(Field $field): JsonResponse
    {
        $field->delete();

        return response()->json([
            'message' => 'Field deleted successfully.'
        ]);
    }
}