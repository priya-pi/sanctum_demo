<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Controllers\Api\BaseController as BaseController;

use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), [
                'name' => 'required|regex:/^[a-zA-Z ]*$/',
                'email' => 'required|email',
                'password' =>
                    'required|regex:/^[a-zA-Z0-9_!@#$%&]*$/|min:8|max:16',
                'interests' => 'required',
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
            ]);

            if ($validateUser->fails()) {
                return $this->sendError(
                    'Validation Error.',
                    $validateUser->errors()
                );
            }
            $json = json_encode($request->get('interests'));
            $file_name = $request->file('image')->getClientOriginalName();
            $image_path = $request->file('image')->storeAs('photo', $file_name);

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => hash::make($request->get('password')),
                'gender' => $request->get('gender'),
                'interests' => $json,
                'image' => $image_path,
            ]);
            $success['token'] = $user->createToken('API TOKEN')->plainTextToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'User register successfully.');
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                500
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'validation error',
                        'errors' => $validateUser->errors(),
                    ],
                    401
                );
            }
            if (
                Auth::attempt([
                    'email' => $request->email,
                    'password' => $request->password,
                ])
            ) {
                $user = User::where('email', $request->email)->first();
                // $success['token'] = $user->createToken(
                //     'API TOKEN'
                // )->plainTextToken;
                $success['name'] = $user->name;
                return $this->sendResponse(
                    $success,
                    'User login successfully.'
                );
            } else {
                return $this->sendError('Unauthorised.', [
                    'error' => 'Email or password is not valid',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                500
            );
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->bearerToken();
        $token = PersonalAccessToken::findToken($accessToken); // Get access token from database
        $token->delete(); // Revoke token
        // dd($token);// Get bearer token from the request
        return [
            'message' => 'user logged out',
        ];


    }
}
