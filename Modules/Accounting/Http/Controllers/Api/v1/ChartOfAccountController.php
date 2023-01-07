<?php

namespace Modules\Accounting\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\User\Entities\User;
use Yajra\DataTables\Facades\DataTables;

class ChartOfAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:accounting.chart_of_accounts.index'])->only(['index', 'show']);
        $this->middleware(['permission:accounting.chart_of_accounts.create'])->only(['create', 'store']);
        $this->middleware(['permission:accounting.chart_of_accounts.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:accounting.chart_of_accounts.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $account_type = $request->account_type;
        $data = ChartOfAccount::orderBy('gl_code')->when($account_type, function ($query) use ($account_type) {
            $query->where('account_type', $account_type);
        })->paginate($limit);
        return response()->json([$data]);
    }

    public function get_accounts_via_type(Request $request)
    {
        $data = ChartOfAccount::where('account_type', $request->account_type)->get();
        return response()->json([$data]);
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'gl_code' => ['required'],
            'account_type' => ['required'],
            'allow_manual' => ['required'],
            'active' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $chart_of_account = new ChartOfAccount();
            $chart_of_account->name = $request->name;
            $chart_of_account->parent_id = $request->parent_id;
            $chart_of_account->gl_code = $request->gl_code;
            $chart_of_account->account_type = $request->account_type;
            $chart_of_account->allow_manual = $request->allow_manual;
            $chart_of_account->active = $request->active;
            $chart_of_account->notes = $request->notes;
            $chart_of_account->save();
            return response()->json(['data' => $chart_of_account, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $chart_of_account = ChartOfAccount::find($id);
        return theme_view('accounting::chart_of_account.show', compact('chart_of_account'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $chart_of_account = ChartOfAccount::find($id);
        return response()->json(['data' => $chart_of_account]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'gl_code' => ['required'],
            'account_type' => ['required'],
            'allow_manual' => ['required'],
            'active' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $chart_of_account = ChartOfAccount::find($id);
            $chart_of_account->name = $request->name;
            $chart_of_account->parent_id = $request->parent_id;
            $chart_of_account->gl_code = $request->gl_code;
            $chart_of_account->account_type = $request->account_type;
            $chart_of_account->allow_manual = $request->allow_manual;
            $chart_of_account->active = $request->active;
            $chart_of_account->notes = $request->notes;
            $chart_of_account->save();
            return response()->json(['data' => $chart_of_account, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        ChartOfAccount::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
