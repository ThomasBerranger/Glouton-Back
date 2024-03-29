<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user): true
    {
        return true;
    }

    public function view(User $user, Product $product): Response
    {
        return $user->id === $product->user_id ? Response::allow() : Response::denyWithStatus(404);
    }

    public function create(User $user): true
    {
        return true;
    }

    public function update(User $user, Product $product): Response
    {
        return $user->id === $product->user_id ? Response::allow() : Response::denyWithStatus(404);
    }

    public function delete(User $user, Product $product): Response
    {
        return $user->id === $product->user_id ? Response::allow() : Response::denyWithStatus(404);
    }
}
