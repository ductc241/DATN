<?php

namespace App\Http\Resources;
use Carbon\Carbon; 
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name, 
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password' => $this->password,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'gender' => $this->gender, 
            'avatar' => $this->avatar,
            'role_id' => $this->role_id,
            'status' => $this->status     
        ];
    }
}
