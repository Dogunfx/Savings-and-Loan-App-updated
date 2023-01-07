<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use Laravel\Passport\ClientRepository;
use Modules\CustomField\Entities\CustomField;
use Modules\Setting\Entities\Setting;
use Modules\User\Entities\User;
use Modules\User\Notifications\DemoNotification;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:user.users.index'])->only(['index', 'show', 'get_users']);
        $this->middleware(['permission:user.users.create'])->only(['create', 'store']);
        $this->middleware(['permission:user.users.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:user.users.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $roles = Role::all();
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $filter = $request->filter;
        $data = User::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('first_name', 'like', "%$search%");
                $query->orWhere('last_name', 'like', "%$search%");
                $query->orWhere('email', 'like', "%$search%");
                $query->orWhere('phone', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('user::user.index', compact('data','roles'));
    }

    public function get_users(Request $request)
    {


        $query = User::query();
        return DataTables::of($query)->editColumn('user', function ($data) {
            return $data->first_name . ' ' . $data->last_name;
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('user/' . $data->id . '/show') . '" class="">' . trans_choice('user::general.detail', 2) . '</a></li>';
            if (Auth::user()->hasPermissionTo('user.users.edit')) {
                $action .= '<li><a href="' . url('user/' . $data->id . '/edit') . '" class="">' . trans_choice('user::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('user.users.destroy')) {
                $action .= '<li><a href="' . url('user/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('user::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('user/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->editColumn('created_at', function ($data) {
            return $data->created_at->format('Y-m-d');

        })->editColumn('name', function ($data) {
            return '<a href="' . url('user/' . $data->id . '/show') . '">' . $data->first_name . ' ' . $data->last_name . '</a>';

        })->editColumn('gender', function ($data) {
            if ($data->gender == "male") {
                return trans_choice('core::general.male', 1);
            }
            if ($data->gender == "female") {
                return trans_choice('core::general.female', 1);
            }

        })->rawColumns(['id', 'name', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $custom_fields = CustomField::where('category', 'add_user')->where('active', 1)->get();
        $roles = Role::all();
        return theme_view('user::user.create', compact('custom_fields', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'phone' => ['nullable', 'numeric'],
            'roles.*' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],

        ]);
        $credentials = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'notes' => $request->notes,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'email_verified_at' => date("Y-m-d H:i:s")
        ];
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads');
            $credentials['photo'] = basename($file_name);
        }
        $user = User::create($credentials);
        //attach client role
        $user->syncRoles($request->roles);
        custom_fields_save_form('add_user', $request, $user->id);
        activity()->on($user)
            ->withProperties(['id' => $user->id])
            ->log('Create User');
        //if client, add client to database
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('user');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $custom_fields = CustomField::where('category', 'add_user')->where('active', 1)->get();
        return theme_view('user::user.show', compact('user', 'custom_fields'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::all();
        $selected_roles = $user->roles->map(function ($item) {
            return $item->id;
        })->toArray();
        $custom_fields = CustomField::where('category', 'add_user')->where('active', 1)->get();
        return theme_view('user::user.edit', compact('user', 'selected_roles', 'custom_fields', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'phone' => ['nullable', 'numeric'],
            'roles.*' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['confirmed'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);
        $credentials = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'notes' => $request->notes,
            'address' => $request->address,
        ];
        if (!empty($request->password)) {
            $credentials['password'] = Hash::make($request->password);
        }
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads');
            if ($user->photo) {
                Storage::delete('public/uploads/' . $user->photo);
            }
            $credentials['photo'] = basename($file_name);
        }
        $user->syncRoles($request->roles);

        $user->update($credentials);
        custom_fields_save_form('add_user', $request, $user->id);
        activity()->on($user)
            ->withProperties(['id' => $user->id])
            ->log('Update User');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('user');
    }

    public function edit_profile()
    {

        $user = Auth::user();

        return theme_view('user::user.edit_profile', compact('user'));
    }

    public function profile()
    {

        $user = Auth::user();

        return theme_view('user::user.profile.account', compact('user'));
    }

    public function update_profile(Request $request)
    {
        $user = Auth::user();
        $request->validate(array(
            'email' => 'required|unique:users,email,' . $user->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ));
        $credentials = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
            'gender' => $request->gender,

        ];
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads');
            if ($user->photo) {
                Storage::delete('public/uploads/' . $user->photo);
            }
            $credentials['photo'] = basename($file_name);
        }

        $user->update($credentials);
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();

        return redirect()->back();
    }

    public function change_password()
    {
        $user = Auth::user();
        return theme_view('user::user.profile.change_password', compact('user'));
    }


    public function update_password(Request $request)
    {
        $user = Auth::user();
        $request->validate(array(
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ));
        if (!Hash::check($request->password, $user->password)) {
            \flash(trans_choice("core::general.Invalid Password", 1))->error()->important();
            return redirect()->back();
        }
        $credentials = [
            'password' => Hash::make($request->password),
        ];
        $user->update($credentials);
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function note()
    {
        $user = Auth::user();
        return theme_view('user::user.profile.note', compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user->id == Auth::id()) {
            \flash(trans_choice("core::general.You cannot delete your account", 1))->error()->important();
            return redirect()->back();
        }
        $user->delete();
        activity()->on($user)
            ->withProperties(['id' => $user->id])
            ->log('Delete User');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect('user');
    }

    public function notification()
    {
        $user = Auth::user();
        $data = $user->notifications()->paginate(20);
        return theme_view('user::user.profile.notification', compact('user', 'data'));
    }

    public function show_notification($id)
    {
        $user = Auth::user();
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        $notification->update(['read_at' => now()]);
        return theme_view('user::user.profile.show_notification', compact('notification', 'user'));
    }

    public function mark_all_notifications_as_read()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function mark_notification_as_read($id)
    {
        DB::table('notifications')->where('id', $id)->update(['read_at' => now()]);
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function destroy_notification($id)
    {
        DB::table('notifications')->where('id', $id)->delete();
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect('user/profile/notification');
    }

    public function activity_log()
    {
        $user = Auth::user();
        $data = Activity::where('causer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return theme_view('user::user.profile.activity_log', compact('user', 'data'));
    }

    public function get_activity_logs(Request $request)
    {
        $query = Activity::where('causer_id', Auth::id());
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '';
            $action .= '<a href="' . url('user/profile/activity_log/' . $data->id . '/show') . '" class=""><i class="fa fa-eye"></i></a>';
            return $action;
        })->editColumn('created_at', function ($data) {
            return $data->created_at->format('Y-m-d H:i:s');

        })->editColumn('id', function ($data) {
            return '<a href="' . url('user/profile/activity_log/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action', 'user'])->make(true);
    }

    public function show_activity_log($id)
    {
        $user = Auth::user();
        $activity_log = Activity::find($id);
        return theme_view('user::user.profile.show_activity_log', compact('activity_log', 'user'));
    }

    public function api()
    {
        $user = Auth::user();
        $tokens = $user->tokens()->paginate(20);
        return theme_view('user::user.profile.api', compact('tokens', 'user'));
    }

    public function store_personal_access_token(Request $request)
    {

        $token = Auth::user()->createToken($request->name ?: 'Personal Access Token');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function update_personal_access_token(Request $request)
    {

        $token = Auth::user()->tokens()->where('id', $request->personal_token_id)->update(['name' => $request->name]);
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function destroy_personal_access_token($id)
    {
        Auth::user()->tokens()->where('id', $id)->delete();
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function store_oauth_client(Request $request)
    {

        $personal_client = new ClientRepository();
        $personal_client->createPersonalAccessClient(Auth::id(), $request->name, $request->redirect);

        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function update_oauth_client(Request $request)
    {
        Auth::user()->clients()->where('id', $request->oauth_client_id)->update(['name' => $request->name, 'redirect' => $request->redirect]);
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function destroy_oauth_client($id)
    {
        Auth::user()->clients()->where('id', $id)->delete();
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function two_factor()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        if (empty(Auth::user()->google2fa_secret)) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        } else {
            $secret = Auth::user()->google2fa_secret;
        }
        $qr_image = $google2fa->getQRCodeInline(
            Setting::where('setting_key', 'core.company_name')->first()->setting_value,
            $user->email,
            $secret
        );
        return theme_view('user::user.profile.two_factor', compact('user', 'qr_image', 'secret'));
    }

    public function two_factor_enable(Request $request)
    {
        $google2fa = app('pragmarx.google2fa');
        $secret = $request->google_app_code;
        $valid = $google2fa->verifyKey(Auth::user()->google2fa_secret, $secret);
        if ($valid) {
            Auth::user()->enable_google2fa = 1;
            Auth::user()->save();
            \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
            return redirect()->back();
        } else {
            \flash(trans_choice("user::general.Invalid OTP", 1))->error()->important();
            return redirect()->back();
        }


    }

    public function two_factor_disable(Request $request)
    {
        if (!(Hash::check($request->password, Auth::user()->password))) {
            \flash(trans_choice("user::general.Invalid Password", 1))->error()->important();
            return redirect()->back();
        }
        Auth::user()->enable_google2fa = 0;
        Auth::user()->save();
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }
}
