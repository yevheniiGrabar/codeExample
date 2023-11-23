<?php

namespace App\Services;

use App\Models\User;
use App\Traits\CurrentCompany;
use Carbon\Carbon;

class UserService
{
    public function updateUser(User $user, array $data): User
    {
        $user->update(
            [
                'name' => $data['name'] ?? $user->name,
                'last_name' => $data['last_name'] ?? $user->last_name,
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? $user->phone,
                'country_id' => $data['country'] ?? $user->country_id,
                'language_id' => $data['language'] ?? $user->language_id,
            ]
        );

        $user->save();

        return $user;
    }

    public function inviteUser(array $data): \Exception|User
    {
        try {
            $company = CurrentCompany::getDefaultCompany();
            list($name, $last_name) = explode(' ', $data['name'], 2);

            $user = new User();

            $user->name = $name;
            $user->last_name = $last_name;
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->confirm_password = bcrypt($data['password']);
            $user->save();

            $user->roles()->attach($data['role']);
            $user->companies()->attach($company->company_id, ['is_default' => true]);

            $user->sendEmailVerificationNotification();

        } catch (\Exception $exception) {
            return $exception;
        }


        return $user;
    }
}
