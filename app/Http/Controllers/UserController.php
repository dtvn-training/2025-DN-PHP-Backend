<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\APIResponse;

class UserController extends ControllerWithGuard
{
    use APIResponse;

    private $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct(); 
        $this->userService = $userService;
    }

    public function index()
    {
        $result = $this->userService->index();
        return $this->responseSuccessWithData($result);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->userService->store($request->name, $request->email, $request->password);

        return $this->responseSuccessWithData($result, true);
    }

    public function show($id)
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return $this->responseSuccessWithData($user);
    }

    public function update($id, Request $request)
    {
        $result = $this->userService->update($id, ['name'=>($request->name), 'email' => ($request->email), 'password'=> ($request->password)]);
        return $this->responseSuccessWithData($result);
    }

    public function destroy($id)
    {
        $this->userService->destroy($id);
        return $this->responseSuccess('User soft deleted successfully!');
    }

    public function getDeletedUsers()
    {
        $result = $this->userService->getDeletedUsers();
        return $this->responseSuccessWithData($result);
    }

    public function restore($id)
    {
        $this->userService->restore($id);
        return $this->responseSuccess('Deleted user restored successfully!');
    }

    public function forceDelete($id)
    {
        $this->userService->forceDelete($id);
        return $this->responseSuccess('User permanently deleted!');
    }

    public function changePassword($id, Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed', 
        ]);

        $result = $this->userService->changePassword($id, $request->password);
        return $this->responseSuccessWithData($result);
    }

    public function me(Request $request)
    {
        $result = $request->user();
        return $this->responseSuccessWithData($result);
    }
}
