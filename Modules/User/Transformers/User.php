<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class User extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [

                'id'=>$this->id,
                'username'=>$this->username,
                'email'=>$this->email,
                'first_name'=>$this->first_name,
                'last_name'=>$this->last_name,
                'name'=>$this->name,
                'gender'=>$this->gender,
                'phone'=>$this->phone,
                'address'=>$this->address,
                'created_at'=>$this->created_at
        ];
    }
}
