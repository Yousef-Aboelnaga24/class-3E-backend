<?php

namespace App\Services;

use App\Models\ClassCode;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClassCodeService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return ClassCode::query()->latest()->paginate($perPage);
    }

    public function create(array $data, User $admin): ClassCode
    {
        return ClassCode::create([
            'code' => $data['code'],
            'created_by' => $admin->id,
            'is_active' => $data['is_active'] ?? true,
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }

    public function delete(ClassCode $classCode): void
    {
        $classCode->delete();
    }
}
