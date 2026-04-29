<?php

namespace App\Services;

use App\Models\ClassCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function register(array $data): User
    {
        $classCode = null;
        $role = 'user';

        if (!empty($data['class_code'])) {
            $classCode = ClassCode::query()
                ->where('code', $data['class_code'])
                ->valid()
                ->first();

            if ($classCode) {
                $role = 'student';
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'class_code_id' => $classCode?->id,
        ]);

        Log::info('User role assigned on registration', [
            'user_id' => $user->id,
            'role' => $role,
            'class_code_id' => $classCode?->id,
        ]);

        return $user;
    }
}
