<?php
namespace App\Services;

use App\Models\SalesOrder;

class SalesOrderService
{
    public function createUser($data)
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        if(! empty($data->roles)) {
            $user->assignRole($data->roles);
        }

        return $user;
    }
}
