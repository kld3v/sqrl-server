<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Import the Validator facade
use Illuminate\Validation\Rules;
use App\Rules\ValidUsername;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = $this->validateRegistration($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $token = $user->createToken('NativeSignInToken')->plainTextToken;
            
        return response()->json([
            'username' => $user->username,
            'token' => $token,
        ]);
    }

    /**
     * Validate the registration request.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateRegistration(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username', new ValidUsername()],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    }
}
