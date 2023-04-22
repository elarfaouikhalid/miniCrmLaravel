<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash; 

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'date_of_birth' => $data['date_of_birth'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
