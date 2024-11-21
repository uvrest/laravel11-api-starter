<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface HasAvatarInterface
{
    public function uploadAvatar(UploadedFile $avatar): void;

    public function deleteAvatar(): void;

    public function getAvatarPath(): ?string;
}
