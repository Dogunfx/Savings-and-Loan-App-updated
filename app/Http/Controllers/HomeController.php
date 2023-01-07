<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\PasswordReset;
use App\Models\Borrower;
use App\Models\Setting;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use Sentinel;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Http\Requests;

class HomeController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return redirect('login')->send();

    }

    public function error()
    {
        return view('errors.general_error');
    }

    public function login()
    {
        return view('login');
    }


    public function adminLogin()
    {
        return view('admin_login');
    }

    public function logout()
    {
        GeneralHelper::audit_trail("Logged out of system");
        Sentinel::logout(null, true);
        return redirect('/');
    }

    public function processLogin()
    {
        $rules = array(
            'email' => 'required',
            'password' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            //process validation here
            $credentials = array(
                "email" => Input::get('email'),
                "password" => Input::get('password'),
            );
            if (!empty(Input::get('remember'))) {
                //remember me token set
                if (Sentinel::authenticateAndRemember($credentials)) {
                    if (Sentinel::inRole('client')) {
                        $borrower = Borrower::where('linked_user_id', Sentinel::getUser()->id)->first();
                        if (!empty($borrower)) {
                            session(["borrower_id" => $borrower->id]);
                        }else{
                            Sentinel::logout(null, true);
                            Flash::warning("You have no linked client. Contact admin");
                            return redirect('/login');
                        }
                    }
                    GeneralHelper::audit_trail("Logged in to system");
                    return redirect('/');
                } else {
                    //return back
                    Flash::warning(trans('login.failure'));
                    return redirect()->back()->withInput()->withErrors('Invalid email or password.');
                }
            } else {
                if (Sentinel::authenticate($credentials)) {
                    //logged in, redirect
                    if (Sentinel::inRole('client')) {
                        $borrower = Borrower::where('linked_user_id', Sentinel::getUser()->id)->first();
                        if (!empty($borrower)) {
                            session(["borrower_id" => $borrower->id]);
                        }else{
                            Sentinel::logout(null, true);
                            Flash::warning("You have no linked client. Contact admin");
                            return redirect('/login');
                        }
                    }
                    GeneralHelper::audit_trail("Logged in to system");
                    return redirect('/');
                } else {
                    //return back
                    Flash::warning(trans('login.failure'));
                    return redirect()->back()->withInput()->withErrors('Invalid email or password.');
                }
            }


        }
    }

    public function register()
    {
        $rules = array(
            'email' => 'required|unique:users',
            'password' => 'required',
            'rpassword' => 'required|same:password',
            'first_name' => 'required',
            'last_name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            Flash::warning(trans('login.failure'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            //process validation here
            $credentials = array(
                "email" => Input::get('email'),
                "password" => Input::get('password'),
                "first_name" => Input::get('first_name'),
                "last_name" => Input::get('last_name'),
            );
            $user = Sentinel::registerAndActivate($credentials);
            $role = Sentinel::findRoleByName('Client');
            $role->users()->attach($user);
            $msg = trans('login.success');
            Flash::success(trans('login.success'));
            return redirect('login')->with('msg', $msg);

        }
    }

    public function password_reset()
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        return view('password_reset');
    }

    /*
     * Password Resets
     */
    public function process_password_reset()
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        $rules = array(
            'email' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            //process validation here
            $credentials = array(
                "email" => Input::get('email'),
            );
            $user = Sentinel::findByCredentials($credentials);
            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(trans_choice('general.user_email_not_found', 1));
            } else {
                Mail::to($user->email)->send(new PasswordReset($user));
                Flash::success(trans('general.password_reset_success'));
                return redirect()->back()
                    ->withSuccess(trans('general.password_reset_success'));
            }

        }
    }

    public function confirm_password_reset($id, $code)
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        return view('confirm_password_reset', compact('id', 'code'));
    }

    public function process_confirm_password_reset(Request $request, $id, $code)
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        $rules = array(
            'password' => 'required',
            'repeat_password' => 'required|same:password',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            //process validation here

            $user = Sentinel::findById($id);
            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(trans_choice('general.user_email_not_found', 1));
            }
            $credentials = array(
                "email" => $user->email,
                'password' => Input::get('password'),
            );
            if (!Reminder::complete($user, $code, Input::get('password'))) {
                return redirect()->to('password_reset')
                    ->withErrors(trans('general.invalid_password_reset_code'));
            }
            Sentinel::authenticate($credentials);
            Flash::success(trans('general.password_reset_complete'));
            return redirect('dashboard');

        }
    }

    //client functions

    public function clientLogin(Request $request)
    {
        if ($request->session()->has('uid')) {
            //user is logged in
            return redirect('client_dashboard');
        }
        return view('client.login');
    }

    public function clientRegister(Request $request)
    {
        if ($request->session()->has('uid')) {
            //user is logged in
            return redirect('client_dashboard');
        }
        return view('client.register');
    }

    public function processClientRegister(Request $request)
    {
        if (Setting::where('setting_key', 'allow_self_registration')->first()->setting_value == 1) {
            $rules = array(
                'repeat_password' => 'required|same:password|min:6',
                'password' => 'required|min:6',
                'first_name' => 'required',
                'mobile' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'email' => 'required|email|unique:borrowers',
                'dob' => 'required',
                'username' => 'required|unique:borrowers',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                Flash::warning('Validation errors occurred');
                return redirect()->back()->withInput()->withErrors($validator);

            } else {
                $borrower = new Borrower();
                $borrower->first_name = $request->first_name;
                $borrower->last_name = $request->last_name;
                $borrower->gender = $request->gender;
                $borrower->mobile = $request->mobile;
                $borrower->email = $request->email;
                $borrower->dob = $request->dob;
                $borrower->files = serialize(array());
                $borrower->working_status = $request->working_status;
                if (Setting::where('setting_key', 'client_auto_activate_account')->first()->setting_value == 1) {
                    $borrower->active = 1;
                } else {
                    $borrower->active = 0;
                }
                $borrower->source = 'online';
                $borrower->username = $request->username;
                $borrower->password = md5($request->password);
                $date = explode('-', date("Y-m-d"));
                $borrower->year = $date[0];
                $borrower->month = $date[1];
                $borrower->save();
                if ($borrower->active == 1) {
                    $request->session()->put('uid', $borrower->id);
                    Flash::success(trans('general.successfully_registered_logged_in'));
                    return redirect('client_dashboard')->with('msg', trans('general.logged_in'));
                }
                Flash::success(trans('general.successfully_registered'));
                return redirect('client')->with('msg', trans('general.successfully_registered'));
            }
        } else {
            Flash::success("Registration disabled");
            return redirect()->back();
        }
    }

    public function processClientLogin(Request $request)
    {
        if (Borrower::where('username', $request->username)->where('password', md5($request->password))->count() == 1) {
            $borrower = Borrower::where('username', $request->username)->where('password',
                md5($request->password))->first();
            //session('uid',$borrower->id);
            if ($borrower->active == 1) {
                $request->session()->put('uid', $borrower->id);
                return redirect('client')->with('msg', "Logged in");
            } else {
                Flash::warning(trans_choice('general.account_not_active', 1));
                return redirect('client')->with('error', trans_choice('general.account_not_active', 1));
            }
        } else {
            //no match
            Flash::warning(trans_choice('general.invalid_login_details', 1));
            return redirect('client')->with('error', trans_choice('general.invalid_login_details', 1));
        }
    }

    public function clientLogout(Request $request)
    {
        $request->session()->forget('uid');
        return redirect('client');

    }

    public function clientDashboard(Request $request)
    {
        if ($request->session()->has('uid')) {
            $borrower = Borrower::find($request->session()->get('uid'));
            return view('client.dashboard', compact('borrower'));
        }
        return view('client_login');

    }

    public function clientProfile(Request $request)
    {
        if ($request->session()->has('uid')) {
            $borrower = Borrower::find($request->session()->get('uid'));
            return view('client.profile', compact('borrower'));
        }
        return view('client_login');

    }

    public function processClientProfile(Request $request)
    {
        if ($request->session()->has('uid')) {
            $rules = array(
                'repeatpassword' => 'required|same:password',
                'password' => 'required'
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                Flash::warning('Passwords do not match');
                return redirect()->back()->withInput()->withErrors($validator);

            } else {
                $borrower = Borrower::find($request->session()->get('uid'));
                $borrower->password = md5($request->password);
                $borrower->save();
                Flash::success('Successfully Saved');
                return redirect('client_dashboard')->with('msg', "Successfully Saved");
            }
            $borrower = Borrower::find($request->session()->get('uid'));
            return view('client.profile', compact('borrower'));
        }
        return view('client_login');

    }

}
