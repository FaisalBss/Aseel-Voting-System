<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Exception;

class AuthService
{

    public function register(array $data): User
    {

        $otpCode = rand(100000, 999999);
        $otpExpiry = Carbon::now()->addMinutes(10);

        $user = User::create([
            'name' => $data['name'],
            'mobile_number' => $data['mobile_number'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiry,
            'is_verified' => false,
            'role' => 0,
        ]);

        return $user;
    }

    public function verifyOtp(array $data): bool
    {
        $user = User::where('mobile_number', $data['mobile_number'])->first();

        if ($user->is_verified) {
            throw new Exception('Account already verified.', 400);
        }

        if ($user->otp_code !== $data['otp_code']) {
            throw new Exception('Invalid OTP code.', 400);
        }

        if (Carbon::now()->isAfter($user->otp_expires_at)) {
            throw new Exception('OTP code has expired.', 400);
        }

        $user->is_verified = true;
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return true;
    }

    public function login(array $data): array
    {


        $loginField = is_numeric($data['login']) ? 'mobile_number' : 'username';

        $user = User::where($loginField, $data['login'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new Exception('Invalid credentials.', 401);
        }

        if (!$user->is_verified) {
            throw new Exception('Account not verified. Please verify your OTP.', 403);
        }

        $token = $user->createToken('auth_token_for_'  . $user->username)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}

