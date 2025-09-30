<?php

namespace App\Repositories\Contracts;

use Dotenv\Util\Str;

interface UserRepositoryInterface
{
    
    public function create(array $data);

    public function findByEmail(string $email);

    public function findByUsername(string $username);


}
