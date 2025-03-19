<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\Interfaces\BaseRepositoryInterface;

interface PostRepositoryInterface extends BaseRepositoryInterface {
    public function getPostPlatformById($id);
    public function getAllSuccessPostPlatforms();
    public function getScheduledPosts();
    public function getPostPlatforms(Post $post);
    public function updatePostPlatform($postPlatform, $status);
    public function getDeletedPosts();
    public function restore($id);
    public function forceDelete($id);
    public function getMyPosts($userId);
}
