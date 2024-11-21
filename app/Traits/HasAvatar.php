<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasAvatar
{
    private string $rootDir = 'thumbnails';

    public function uploadAvatar(UploadedFile $avatar): void
    {
        if (!$this->exists) {
            throw new \LogicException('Model must be saved before uploading an avatar.');
        }

        //Remove the old Avatar
        if($this->avatar){
            $this->deleteAvatar();
        }

        //Save the file
        $path = $avatar->storeAs($this->generateUriPath(), $this->generateFileName($avatar), 'public');

        //Update the model
        $this->avatar = $path;
        $this->save();
    }

    //Generate an uri for the uploaded file
    private function generateUriPath(): string
    {
        $now = Carbon::now();
        $year = (string) $now->year;
        $month = str_pad($now->month, 2, '0', STR_PAD_LEFT);

        // Class name of the model
        $class = Str::kebab(class_basename($this));

        // URI path
        return "{$this->rootDir}/{$class}/{$year}/{$month}";
    }

    //Generate a custom and unique file name for the uploaded file
    private function generateFileName(UploadedFile $avatar): string
    {
        $extension = strtolower($avatar->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowedExtensions)) {
            throw new \InvalidArgumentException('Unsupported file type.');
        }

        return $this->id . '.' . $extension;
    }

    public function deleteAvatar(): void
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }

        // Atualiza o atributo avatar no modelo
        $this->avatar = null;
        $this->save();
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }


}
