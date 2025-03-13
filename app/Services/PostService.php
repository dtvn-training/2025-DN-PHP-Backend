<?php

namespace App\Services;

use App\Repositories\Post\PostRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class PostService
{
    private $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }


    public function index()
    {
        // 
    }

    public function store(array $data)
    {
        return $this->postRepository->create($data);
    }

    public function show($id)
    {
        return $this->postRepository->getById($id);
    }

    public function myPosts($userId) {
        return $this->postRepository->getMyPosts($userId);
    }

    public function update($id, array $data)
    {
        return $this->postRepository->update($id, $data);
    }

    public function destroy($id)
    {
        return $this->postRepository->delete($id);
    }

    public function getDeletedPosts()
    {
        return $this->postRepository->getDeletedPosts();
    }

    public function restore($id)
    {
        return $this->postRepository->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->postRepository->forceDelete($id);
    }
}
