<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\Borrower;
use App\Models\BorrowerGroup;
use App\Models\BorrowerGroupMember;
use App\Models\BorrowerGroupPaymentDay;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class BorrowerGroupController extends Controller
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
        $day=$request->day;
        $data = BorrowerGroup::with('days')->when($day,function ($query) use ($day){
            $query->join('borrower_group_payment_days','borrower_group_payment_days.borrower_group_id','borrower_groups.id')->where('day',$day);
        })->get();

        return view('borrower.group.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //get custom fields
        return view('borrower.group.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $group = new BorrowerGroup();
        $group->name = $request->name;
        $group->notes = $request->notes;
        $group->save();
        if (!empty($request->borrower_group_payment_days)) {
            foreach ($request->borrower_group_payment_days as $key) {
                $borrower_group_payment_day = new BorrowerGroupPaymentDay();
                $borrower_group_payment_day->borrower_group_id = $group->id;
                $borrower_group_payment_day->day = $key;
                $borrower_group_payment_day->save();
            }
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/group/data');
    }


    public function show($borrower_group)
    {
        $borrowers = array();
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        return view('borrower.group.show', compact('borrower_group', 'borrowers'));
    }


    public function edit($borrower_group)
    {
        return view('borrower.group.edit', compact('borrower_group'));
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
        $group = BorrowerGroup::find($id);
        $group->name = $request->name;
        $group->notes = $request->notes;
        $group->save();
        BorrowerGroupPaymentDay::where('borrower_group_id',$id)->delete();
        if (!empty($request->borrower_group_payment_days)) {
            foreach ($request->borrower_group_payment_days as $key) {
                $borrower_group_payment_day = new BorrowerGroupPaymentDay();
                $borrower_group_payment_day->borrower_group_id = $group->id;
                $borrower_group_payment_day->day = $key;
                $borrower_group_payment_day->save();
            }
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('borrower/group/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        BorrowerGroup::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('borrower/group/data');
    }

    public function addBorrower(Request $request, $id)
    {
        if (BorrowerGroupMember::where('borrower_id', $request->borrower_id)->count() > 0) {
            Flash::warning(trans('general.borrower_already_added_to_group'));
            return redirect()->back();
        }
        $member = new BorrowerGroupMember();
        $member->borrower_group_id = $id;
        $member->borrower_id = $request->borrower_id;
        $member->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function removeBorrower(Request $request, $id)
    {
        BorrowerGroupMember::destroy($id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }
}
