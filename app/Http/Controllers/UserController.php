<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('auth:api');
        $this->userService = $userService;
    }

    public function index()
    {
        $result = $this->userService->index();
        return response()->json($result, 200);
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

        return response()->json($result, 201);
    }

    public function show($id)
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function update($id, Request $request)
    {
        $result = $this->userService->update($id, ['name'=>($request->name), 'email' => ($request->email), 'password'=> ($request->password)]);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->userService->destroy($id);
        return response()->json($result);
    }

    public function getDeletedUsers()
    {
        $result = $this->userService->getDeletedUsers();
        return response()->json($result);
    }

    public function restore($id)
    {
        $result = $this->userService->restore($id);
        return response()->json($result);
    }

    public function forceDelete($id)
    {
        $result = $this->userService->forceDelete($id);
        return response()->json($result);
    }

    public function changePassword($id, Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed', 
        ]);

        $result = $this->userService->changePassword($id, $request->password);

        return response()->json($result);
    }

    public function me(Request $request)
    {
        $result = $request->user();
        return response()->json($result);
    }
}
