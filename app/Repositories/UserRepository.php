<?php


namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function searchByEmail($email, $isAdmin = false)
    {
        $query = User::where('email', $email);

        if ($isAdmin) {
            $query->where('is_admin', true);
        }

        return $query->get();
    }
}
