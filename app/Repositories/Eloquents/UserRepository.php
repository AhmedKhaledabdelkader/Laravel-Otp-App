<?php

namespace App\Repositories\Eloquents;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    
    protected $model;


    public function __construct(User $user)
    {
        $this->model=$user ;
    }



    public function create(array $data)
    {
        return $this->model->create($data);
    }



    public function findByEmail(string $email)
    {
        return $this->model->where('email',$email)->first();
    }


    
    public function findByUsername(string $username)
    {
        return $this->model->where('username',$username)->first();
    }


    
}
