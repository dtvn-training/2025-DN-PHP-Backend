<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
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

    public function store(UserStoreRequest  $request)
    {
        $result = $this->userService->store($request->full_name, $request->email, $request->password);
        return $this->responseSuccessWithData($result, true);
    }

    public function show($id)
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return $this->responseError('User not found', 404);
        }

        return $this->responseSuccessWithData($user);
    }

    public function update($id, UserUpdateRequest $request)
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return $this->responseError('User not found', 404);
        }

        $result = $this->userService->update($id, [
            'full_name' => ($request->full_name),
            'email' => ($request->email),
            'password' => ($request->password)
        ]);
        return $this->responseSuccessWithData($result);
    }

    /**
     * Soft deletes a user by their ID.
     *
     * @param int $id The ID of the user to be soft deleted.
     * @return \Illuminate\Http\JsonResponse A success response indicating the user has been soft deleted.
     */
    public function destroy($id)
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return $this->responseError('User not found', 404);
        }

        $this->userService->destroy($id);
        return $this->responseSuccess('User soft deleted successfully!');
    }

    /**
     * Get all the deleted users.
     * @return \Illuminate\Http\JsonResponse A success response with data of the deleted users.
     */
    public function getDeletedUsers()
    {
        $result = $this->userService->getDeletedUsers();
        return $this->responseSuccessWithData($result);
    }

    /**
     * Restore a deleted user by their ID.
     *
     * @param int $id The ID of the deleted user.
     * @return \Illuminate\Http\JsonResponse A success response indicating the user has been restored.
     */
    public function restore($id)
    {
        $this->userService->restore($id);
        return $this->responseSuccess('Deleted user restored successfully!');
    }

    /**
     * Delete user permanently by their ID.
     *
     * @param int $id The ID of the user.
     * @return \Illuminate\Http\JsonResponse A success response indicating the user has been delted permanently.
     */
    public function forceDelete($id)
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return $this->responseError('User not found', 404);
        }

        $this->userService->forceDelete($id);
        return $this->responseSuccess('User permanently deleted!');
    }

    public function changePassword($id, UserChangePasswordRequest $request)
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return $this->responseError('User not found', 404);
        }

        $result = $this->userService->changePassword($id, $request->password);
        return $this->responseSuccessWithData($result);
    }

    public function me(Request $request)
    {
        $result = $request->user();
        return $this->responseSuccessWithData($result);
    }
}
