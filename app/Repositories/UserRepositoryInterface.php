<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function findByEmail($email);

    public function searchByEmail($email, $isAdmin = false);
}
