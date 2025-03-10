<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class UserRepositoryImplementation implements UserRepositoryInterface {
    public function getAll()
    {
        return User::all();
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['hashedPassword'],
        ]);
    }

    public function getById($id)
    {
        return User::find($id);
    }

    public function update($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['hashedPassword'],
        ]);
        return $user;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            // $user->deleted_by = auth()->id(); // Track who deleted the user
            $user->save();
            $user->delete(); // soft delete
        }
    
        return response()->json(['message' => 'User soft deleted successfully']);
    }

    public function getDeletedUsers()
    {
        return User::onlyTrashed()->get();
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json(['message' => 'Deleted user restored successfully']);
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return response()->json(['message' => 'User permanently deleted']);
    }

    public function changePassword($id, $hashedPassword)
    {
        $user = User::findOrFail($id);
        $user->update(['password' => $hashedPassword]);
        return $user;
    }
}