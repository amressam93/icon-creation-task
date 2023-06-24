<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->transform(function($data){
            return [
                'id' => $data->id,
                'name' => $data->name,
                'email' => $data->email,
                'is_blocked' => $data->is_user_blocked,
                'created_at' => $data->created_at->format('Y-m-d h:i A'),
                'last_login' => $data->last_login_attempt ? date('Y-m-d h:i A', strtotime($data->last_login_attempt)) : null,
            ];
        });
    }
}
