<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassCodeStoreRequest;
use App\Models\ClassCode;
use App\Services\ClassCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassCodeController extends Controller
{
    public function __construct(private readonly ClassCodeService $classCodeService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $classCodes = $this->classCodeService->list((int) $request->integer('per_page', 15));

        return response()->json($classCodes);
    }

    public function store(ClassCodeStoreRequest $request): JsonResponse
    {
        $classCode = $this->classCodeService->create($request->validated(), $request->user());

        return response()->json([
            'message' => 'Class code created successfully.',
            'class_code' => $classCode,
        ], 201);
    }

    public function destroy(ClassCode $classCode): JsonResponse
    {
        $this->classCodeService->delete($classCode);

        return response()->json(['message' => 'Class code deleted successfully.']);
    }
}
