<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 6/15/19
 * Time: 11:45 AM
 */

namespace Modules\Api\Http\Controllers\v1;



use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
                    'username' => $request->username,
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8',],
        ]);
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()], 422);
        } else {
            $google2fa = app('pragmarx.google2fa');
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                "otp" => rand(20000, 29999),
                "google2fa_secret" => $google2fa->generateSecretKey(),
                'password' => Hash::make($request->password),
            ]);
            $role = Role::findByName("Client");
            $user->assignRole($role);
            //check if there is an kyc tier
            $default_kyc_tier = KycTier::find(Setting::where('setting_key',
                'default_kyc_tier')->first()->setting_value);
            if (!empty($default_kyc_tier)) {
                $user_kyc = new UserKycTier();
                $user_kyc->user_id = $user->id;
                $user_kyc->kyc_tier_id = $default_kyc_tier->id;
                $user_kyc->status = "approved";
                $user_kyc->save();
                $user->kyc_tier_id = $default_kyc_tier->id;
                $user->save();
            }
            event(new UserRegistration($user));
            if (Setting::where('setting_key', 'notify_user_registration')->first()->setting_value == 1) {
                event(new UserRegistered($user));
            }
            return response()->json(["user" => $user], 200);
        }
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json("Successfully logged out", 200);
    }

    public function get_user()
    {

        return response()->json(["user" => auth()->user()], 200);
    }
}