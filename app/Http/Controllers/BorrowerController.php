<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\UserRegistered;
use App\Models\Borrower;

use App\Models\BorrowerGroupMember;
use App\Models\Country;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Laracasts\Flash\Flash;
use Yajra\DataTables\Facades\DataTables;

class BorrowerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Sentinel::hasAccess('borrowers')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!empty($request->active)) {
            $active = $request->active;
        } else {
            $active = null;
        }

        $data = Borrower::where('branch_id', session('branch_id'))->get();

        return view('borrower.data', compact('data', 'active'));
    }

    public function get_borrowers(Request $request)
    {
        if (!Sentinel::hasAccess('borrowers')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!empty($request->active)) {
            $active = $request->active;
        } else {
            $active = null;
        }

        $query = Borrower::query()->where('branch_id', session('branch_id'))->when($active, function ($query) use ($active) {
            $query->where("active", $active);
        });
        return DataTables::of($query)->editColumn('name', function ($data) {
            return '<a href="' . url('borrower/' . $data->id . '/show') . '">' . $data->first_name . ' ' . $data->last_name . '</a>';
        })->editColumn('gender', function ($data) {
            if ($data->gender == "Male") {
                return trans_choice('general.male', 1);
            } else {
                return trans_choice('general.female', 1);
            }

        })->editColumn('status', function ($data) {
            if ($data->active == 1) {
                return '<span class="label label-success">' . trans_choice('general.active', 1) . '</span>';
            } else {
                return '<span class="label label-danger">' . trans_choice('general.pending', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {
            $action = '<ul class="icons-list"><li class="dropdown">  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Sentinel::hasAccess('borrowers.view')) {
                $action .= '<li><a href="' . url('borrower/' . $data->id . '/show') . '" class="">' . trans_choice('general.detail', 2) . '</a></li>';
            }
            if (Sentinel::hasAccess('borrowers.update')) {
                $action .= '<li><a href="' . url('borrower/' . $data->id . '/edit') . '" class="">' . trans_choice('general.edit', 2) . '</a></li>';
            }
            if (Sentinel::hasAccess('borrowers.delete')) {
                $action .= '<li><a href="' . url('borrower/' . $data->id . '/delete') . '" class="delete">' . trans_choice('general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('borrower/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'name', 'action', 'status'])->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('borrowers.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.create', compact('user', 'custom_fields', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('borrowers.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = new Borrower();
        $borrower->first_name = $request->first_name;
        $borrower->last_name = $request->last_name;
        $borrower->user_id = Sentinel::getUser()->id;
        $borrower->gender = $request->gender;
        $borrower->country_id = $request->country_id;
        $borrower->loan_officer_id = $request->loan_officer_id;
        $borrower->title = $request->title;
        $borrower->branch_id = session('branch_id');
        $borrower->mobile = $request->mobile;
        $borrower->notes = $request->notes;
        $borrower->email = $request->email;
        if ($request->hasFile('photo')) {
            $file = array('photo' => Input::file('photo'));
            $rules = array('photo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $fname = "borrower_" . uniqid() . '.' . $request->file('photo')->guessExtension();
                $borrower->photo = $fname;
                $request->file('photo')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $borrower->unique_number = $request->unique_number;
        $borrower->dob = $request->dob;
        $borrower->address = $request->address;
        $borrower->city = $request->city;
        $borrower->state = $request->state;
        $borrower->zip = $request->zip;
        $borrower->phone = $request->phone;
        $borrower->business_name = $request->business_name;
        $borrower->working_status = $request->working_status;
        $borrower->loan_officers = serialize([]);
        $date = explode('-', date("Y-m-d"));
        $borrower->year = $date[0];
        $borrower->month = $date[1];
        $files = array();
        if (!empty($request->file('files'))) {
            $count = 0;
            foreach ($request->file('files') as $key) {
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = "borrower_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }
                $count++;
            }
        }
        $borrower->files = serialize($files);
        $borrower->save();
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        foreach ($custom_fields as $key) {
            $custom_field = new CustomFieldMeta();
            $id = $key->id;
            $custom_field->name = $request->$id;
            $custom_field->parent_id = $borrower->id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "borrowers";
            $custom_field->save();
        }
        //check for borrower group
        if(!empty($request->borrower_group_id)){
            $member = new BorrowerGroupMember();
            $member->borrower_group_id = $request->borrower_group_id;
            $member->borrower_id = $borrower->id;
            $member->save();
        }
        GeneralHelper::audit_trail("Added borrower  with id:" . $borrower->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/data');
    }


    public function show($borrower)
    {
        if (!Sentinel::hasAccess('borrowers.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomFieldMeta::where('category', 'borrowers')->where('parent_id', $borrower->id)->get();
        return view('borrower.show', compact('borrower', 'user', 'custom_fields'));
    }


    public function edit($borrower)
    {
        if (!Sentinel::hasAccess('borrowers.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $countries = array();
        foreach (Country::all() as $key) {
            $countries[$key->id] = $key->name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.edit', compact('borrower', 'user', 'custom_fields', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = Borrower::find($id);
        $borrower->first_name = $request->first_name;
        $borrower->last_name = $request->last_name;
        $borrower->gender = $request->gender;
        $borrower->country_id = $request->country_id;
        $borrower->loan_officer_id = $request->loan_officer_id;
        $borrower->title = $request->title;
        $borrower->mobile = $request->mobile;
        $borrower->notes = $request->notes;
        $borrower->email = $request->email;
        if ($request->hasFile('photo')) {
            $file = array('photo' => Input::file('photo'));
            $rules = array('photo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $fname = "borrower_" . uniqid() . '.' . $request->file('photo')->guessExtension();
                $borrower->photo = $fname;
                $request->file('photo')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $borrower->unique_number = $request->unique_number;
        $borrower->dob = $request->dob;
        $borrower->address = $request->address;
        $borrower->city = $request->city;
        $borrower->state = $request->state;
        $borrower->zip = $request->zip;
        $borrower->phone = $request->phone;
        $borrower->business_name = $request->business_name;
        $borrower->working_status = $request->working_status;
        $files = unserialize($borrower->files);
        $count = count($files);
        if (!empty($request->file('files'))) {
            foreach ($request->file('files') as $key) {
                $count++;
                $file = array('files' => $key);
                $rules = array('files' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,xlsx');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = "borrower_" . uniqid() . '.' . $key->guessExtension();
                    $files[$count] = $fname;
                    $key->move(public_path() . '/uploads',
                        $fname);
                }

            }
        }
        $borrower->files = serialize($files);
        $borrower->username = $request->username;
        $borrower->save();
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        foreach ($custom_fields as $key) {
            if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                'borrowers')->first())
            ) {
                $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                    $id)->where('category', 'borrowers')->first();
            } else {
                $custom_field = new CustomFieldMeta();
            }
            $kid = $key->id;
            $custom_field->name = $request->$kid;
            $custom_field->parent_id = $id;
            $custom_field->custom_field_id = $key->id;
            $custom_field->category = "borrowers";
            $custom_field->save();
        }
        GeneralHelper::audit_trail("Updated borrower  with id:" . $borrower->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/data');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('borrowers.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Borrower::destroy($id);
        Loan::where('borrower_id', $id)->delete();
        LoanRepayment::where('borrower_id', $id)->delete();
        GeneralHelper::audit_trail("Deleted borrower  with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('borrower/data');
    }

    public function deleteFile(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = Borrower::find($id);
        $files = unserialize($borrower->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $borrower->files = serialize($files);
        $borrower->save();


    }

    public function approve(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = Borrower::find($id);
        $borrower->active = 1;
        $borrower->save();
        GeneralHelper::audit_trail("Approved borrower  with id:" . $borrower->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function decline(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = Borrower::find($id);
        $borrower->active = 0;
        $borrower->save();
        GeneralHelper::audit_trail("Declined borrower  with id:" . $borrower->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function blacklist(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.blacklist')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = Borrower::find($id);
        $borrower->blacklisted = 1;
        $borrower->save();
        GeneralHelper::audit_trail("Blacklisted borrower  with id:" . $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function unBlacklist(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.blacklist')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = Borrower::find($id);
        $borrower->blacklisted = 0;
        $borrower->save();
        GeneralHelper::audit_trail("Undo Blacklist for borrower  with id:" . $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function store_login_details(Request $request, $id)
    {
        if (!Sentinel::hasAccess('borrowers.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = Borrower::find($id);
        $rules = array(
            'email' => 'required|unique:users',
            'password' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                'first_name' => $borrower->first_name,
                'last_name' => $borrower->last_name,
                'address' => $borrower->address,
                'gender' => $borrower->gender,
                'phone' => $borrower->mobile,
            ];
            $user = Sentinel::registerAndActivate($credentials);
            $borrower->linked_user_id = $user->id;
            $borrower->save();
            $role = Sentinel::findRoleBySlug('client');
            $role->users()->attach($user->id);
            if ($request->send_email == 1) {
                Mail::to($user->email)->send(new UserRegistered($user,$request->password));
            }
            GeneralHelper::audit_trail("Added user with id:" . $user->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }
}
