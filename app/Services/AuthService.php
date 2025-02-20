<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = auth('api')->login($user);
        if (!$token) {
            throw new JWTException('Could not generate token');
        }

        return $token;
    }

    public function login(array $credentials)
    {
        $token = auth('api')->attempt($credentials);
        if (!$token) {
            throw new \Exception('Invalid credentials', 401);
        }

        return $token;
    }

    public function logout()
    {
        auth('api')->logout();
    }

    public function sendResetLink(string $email)
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception('Unable to send reset link', 400);
        }
    }

    public function resetPassword(array $data)
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \Exception('Invalid token or email', 400);
        }
    }
}