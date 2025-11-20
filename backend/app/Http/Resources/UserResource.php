<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'email'     => $this->email,
            'fullname'  => $this->fullname,
            'birthday'  => $this->birthday?->toDateString(),
            'created_at'=> $this->created_at?->toDateTimeString(),
            // thêm các field “an toàn” khác nếu cần
        ];
    }
}

