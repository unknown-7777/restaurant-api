<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    use ApiResponse;

    public function __construct(protected ImageService $imageService) {}

    public function avatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old avatar
        $this->imageService->delete($user->avatar_path);

        // Store new avatar compressed to 400px
        $path = $this->imageService->store(
            $request->file('avatar'),
            'avatars',
            400
        );

        $user->update(['avatar_path' => $path]);

        return $this->success(new UserResource($user), 'Avatar updated.');
    }

    public function removeAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        $path = $user->avatar_path;

        $user->update(['avatar_path' => null]);
        $this->imageService->delete($path);

        return $this->success(new UserResource($user), 'Avatar removed.');
    }
}
