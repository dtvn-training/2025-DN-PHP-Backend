<?php

namespace App\Repositories\Post;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface PostRepositoryInterface extends BaseRepositoryInterface {
    public function getDeletedPosts();
    public function restore($id);
    public function forceDelete($id);
    public function getMyPosts($userId);
}
