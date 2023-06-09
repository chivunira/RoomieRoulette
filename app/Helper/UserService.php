<?php

namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;


class UserService
{
    public $email, $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function validateInput($auth = false)
    {
        $validationRule = $auth ? 'exists:users' : 'unique:users';
        $validator = Validator::make(
            ['email' => $this->email, 'password' => $this->password],
            [
                'email' => ['required', 'email:rfc,dns', $validationRule],
                'password' => ['required', 'string', Password::min(8)]
            ]
        );

        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->messages()];
        } else {
            return ['status' => true];
        }
    }

    public function register($deviceName)
    {
        $validate = $this->validateInput();
        if ($validate['status'] == false) {
            return $validate;
        } else {
            $user = User::create([
                'email' => $this->email,
                'password' => Hash::make($this->password)
            ]);
            $token = $user->createToken($deviceName)->plainTextToken;
            return ['status' => true, 'token' => $token, 'user' => $user];
        }
    }

    public function login() {
        $validate = $this->validateInput(true);
        if($validate['status'] == false) {
            return $validate;
        } else {
            $user = User::where('email', $this->email)->first();
            if(!$user || !Hash::check($this->password, $user->password)) {
                return ['status' => false, 'message' => 'Invalid credentials'];
            } else {
                $token = $user->createToken('login')->plainTextToken;
                return ['status' => true, 'token' => $token, 'user' => $user];
            }
        }
    }
}
