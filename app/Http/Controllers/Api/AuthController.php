<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginUserRequest $request){
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email','password'))) {
            return $this->error('Invalid Credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken(
                    'API token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth())->plainTextToken,
            ]
        );
    }

    public function register(RegisterUserRequest $request){
        $user = User::firstWhere('email', $request->email);
        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken('API token for ' . $user->email)->plainTextToken,
            ]
        );
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }
}
