<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => $validator->errors()->all()
                ],
                204
            );
        }

        $user = User::query()
            ->Where('email', $request->email)
            ->first();
        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => ['user not found']
                ], 204
            );
        }

        if (!Hash::check($request->get('password'), $user->password)) {
            return response()->json(
                [
                    'success' => false,
                    'data' => null,
                    'message' => ['password not matched']
                ],
                204
            );
        }
        $res = [
            'user' =>
                [
                    'id' => $user->id,
                    'email' => $user->email,
                    'nama' => $user->nama,
                    'no_hp' => $user->no_hp,
                ],
            'token' => $user->createToken('test')->accessToken
        ];
        return response()->json(
            [
                'success' => true,
                'data' => $res
            ],
            202
        );
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'nama' => 'required',
            'no_hp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [
                    ],
                    'message' => $validator->errors()->all()
                ],
                400
            );
        }

        $user = User::query()->create([
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'nama' => $request->nama,
            'no_hp' => $request->no_hp
        ]);

        $res = [
            'user' =>
                [
                    'id' => $user->id,
                    'email' => $user->email,
                    'nama' => $user->nama,
                    'no_hp' => $user->no_hp,
                ],
            'token' => $user->createToken('test')->accessToken
        ];
        return response()->json(
            [
                'success' => true,
                'data' => $res
            ],
            201
        );
    }
}
