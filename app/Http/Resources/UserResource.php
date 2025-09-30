<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            "message"=>"Registered successfully. Please check your email for OTP.",
            'user' => [
                'id' => $this->id,
                'username' => $this->username,
                'email' => $this->email,
                'isVerified' => $this->is_verified,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],


        ];
    }
}
