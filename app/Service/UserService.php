<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    private ImageUploader $imageUploader;

    public function __construct(ImageUploader $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }

    public function register(string $email, string $password, ?string $name = null): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        event(new Registered($user));

        return $user;
    }

    public function updateProfile(Authenticatable $user, array $attributes): User
    {
        if (isset($attributes['email'])) {
            $user->email = $attributes['email'];
        }

        if (isset($attributes['name'])) {
            $user->name = $attributes['name'];
        }

        if (isset($attributes['current_dictionary'])) {
            $user->current_dictionary = $attributes['current_dictionary'];
        }

        if (isset($attributes['avatar'])) {
            /** @var UploadedFile $file */
            $file = $attributes['avatar'];
            $name = 'profile/avatar/'.Str::uuid().'.'.$file->getClientOriginalExtension();

            $this->imageUploader->uploadImage($name, $file);

            $user->avatar = $name;
        }

        $user->save();

        return $user;
    }
}
