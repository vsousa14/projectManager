<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /**
     * Get all users.
     *
     * @return \Illuminate\Database\Eloquent\Collection<User>
     */
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    /**
     * Get a paginated list of users.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedUsers(int $perPage = 10)
    {
        return User::paginate($perPage);
    }

    /**
     * Updates existing user
     *
     * @param User $user
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function update(User $user, array $data): User
    {
        if (isset($data['email']) && $data['email'] !== $user->email && User::where('email', $data['email'])->exists()) {
             throw ValidationException::withMessages([
                'email' => ['Este email já está registado por outro utilizador.'],
            ]);
        }

        $user->name = $data['name'] ?? $user->name;
        $user->email = $data['email'] ?? $user->email;

        if (isset($data['password']) && !empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }
}