<?php

namespace App\Repositories\User;

use App\Models\User\User;

class UserRepository {
    public function findByEmail(string $email): ?User {
        return User::where('email', $email)->first();
    }
}
