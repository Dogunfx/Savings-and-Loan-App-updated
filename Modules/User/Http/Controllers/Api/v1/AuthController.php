<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 6/15/19
 * Time: 11:45 AM
 */

namespace Modules\User\Http\Controllers\Api\v1;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Client\Entities\ClientUser;
use Modules\User\Entities\User;
use Nwidart\Modules\Routing\Controller;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $client = new Client();
        try {
            $response = $client->post(url('oauth/token'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('api.passport.client_id'),
                    'client_secret' => config('api.passport.client_secret'),
                    'username' => $request->username ? $request->username : $request->email,
                    'password' => $request->password
                ]
            ]);
            return $response->getBody();
        } catch (BadResponseException $e) {
            if ($e->getCode() == 400) {
                return response()->json("Invalid request, please enter username or password", $e->getCode());
            } elseif ($e->getCode() == 401) {
                return response()->json("Invalid login details", $e->getCode());
            } else {
                return response()->json("Something went wrong on our server", $e->getCode());
            }
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'phone' => 'required',
            'last_name' => 'required',
            'branch_id' => 'required',
            'gender' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8',],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            //$google2fa = app('pragmarx.google2fa');
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'email' => $request->email,
                //"google2fa_secret" => $google2fa->generateSecretKey(),
                'password' => Hash::make($request->password),
                'email_verified_at' => date("Y-m-d H:i:s")
            ]);
            $role = Role::findByName("client");
            $user->assignRole($role);
            $client = new \Modules\Client\Entities\Client();
            $client->first_name = $user->first_name;
            $client->last_name = $user->last_name;
            $client->created_by_id = Auth::id();
            $client->gender = $user->gender;
            $client->branch_id =$request->branch_id;
            $client->mobile = $user->phone;
            $client->email = $user->email;
            $client->created_date = date("Y-m-d");
            $client->save();
            $client_user = new ClientUser();
            $client_user->client_id = $client->id;
            $client_user->created_by_id = Auth::id();
            $client_user->user_id = $user->id;
            $client_user->save();
           // event(new UserRegistration($user));
            return response()->json(['data' => $user, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json(["success" => true, "message" => "Successfully logged out"]);
    }

    public function get_user()
    {
        return response()->json(["data" => auth()->user()], 200);
    }

}