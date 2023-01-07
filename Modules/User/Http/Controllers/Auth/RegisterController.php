<?php

namespace Modules\User\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientUser;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $branches = Branch::all();
        return theme_view('user::auth.register',compact('branches'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'branch_id' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'phone' => ['required', 'numeric'],
            'agree' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \Modules\User\Entities\User
     */
    protected function create(array $data)
    {
        $user = \Modules\User\Entities\User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => date("Y-m-d H:i:s")
        ]);
        //attach client role
        $role = Role::findByName("client");
        $user->assignRole($role);
        //create client record
        $client = new Client();
        $client->first_name = $user->first_name;
        $client->last_name = $user->last_name;
        $client->created_by_id = Auth::id();
        $client->gender = $user->gender;
        $client->branch_id = $data['branch_id'];
        $client->mobile = $user->phone;
        $client->email = $user->email;
        $client->created_date = date("Y-m-d");
        $client->save();
        $client_user = new ClientUser();
        $client_user->client_id = $client->id;
        $client_user->created_by_id = Auth::id();
        $client_user->user_id = $user->id;
        $client_user->save();
        session(['client_id' => $client->id]);
        return $user;
    }
}
