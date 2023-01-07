<?php

namespace Modules\Accounting\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\ChartOfAccount;
use Modules\User\Entities\User;
use Yajra\DataTables\Facades\DataTables;

class ChartOfAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
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
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $data = ChartOfAccount::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
                $query->orWhere('gl_code', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('accounting::chart_of_account.index',compact('data'));
    }

    public function get_chart_of_accounts(Request $request)
    {


        $query = ChartOfAccount::orderBy('gl_code');
        return DataTables::of($query)->editColumn('user', function ($data) {
            return $data->first_name . ' ' . $data->last_name;
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('accounting/chart_of_account/' . $data->id . '/show') . '" class="">' . trans_choice('core::general.detail', 2) . '</a></li>';
            if (Auth::user()->hasPermissionTo('accounting.chart_of_accounts.edit')) {
                $action .= '<li><a href="' . url('accounting/chart_of_account/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('accounting.chart_of_accounts.destroy')) {
                $action .= '<li><a href="' . url('accounting/chart_of_account/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('accounting/chart_of_account/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->editColumn('name', function ($data) {
            return '<a href="' . url('accounting/chart_of_account/' . $data->id . '/show') . '">' . $data->name . ' ' . $data->last_name . '</a>';

        })->editColumn('account_type', function ($data) {
            if ($data->account_type == "asset") {
                return trans_choice('accounting::general.asset', 1);
            }
            if ($data->account_type == "expense") {
                return trans_choice('accounting::general.expense', 1);
            }
            if ($data->account_type == "equity") {
                return trans_choice('accounting::general.equity', 1);
            }
            if ($data->account_type == "liability") {
                return trans_choice('accounting::general.liability', 1);
            }
            if ($data->account_type == "income") {
                return trans_choice('accounting::general.income', 1);
            }
        })->editColumn('allow_manual', function ($data) {
            if ($data->allow_manual == "1") {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->account_type == "0") {
                return trans_choice('core::general.no', 1);
            }

        })->editColumn('active', function ($data) {
            if ($data->active == "1") {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->active == "0") {
                return trans_choice('core::general.no', 1);
            }

        })->rawColumns(['id', 'name', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $chart_of_accounts = ChartOfAccount::orderBy('gl_code')->get();
        return theme_view('accounting::chart_of_account.create', compact('chart_of_accounts'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'gl_code' => ['required', 'unique:chart_of_accounts,gl_code']
        ]);
        $chart_of_account = new ChartOfAccount();
        $chart_of_account->name = $request->name;
        $chart_of_account->parent_id = $request->parent_id;
        $chart_of_account->gl_code = $request->gl_code;
        $chart_of_account->account_type = $request->account_type;
        $chart_of_account->allow_manual = $request->allow_manual;
        $chart_of_account->active = $request->active;
        $chart_of_account->notes = $request->notes;
        $chart_of_account->save();
        activity()->on($chart_of_account)
            ->withProperties(['id' => $chart_of_account->id])
            ->log('Create Chart Of Account');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('accounting/chart_of_account');
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
        $chart_of_accounts = ChartOfAccount::where('id', '!=', $id)->orderBy('gl_code')->get();
        return theme_view('accounting::chart_of_account.edit', compact('chart_of_account', 'chart_of_accounts'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $chart_of_account = ChartOfAccount::find($id);
        $request->validate([
            'name' => ['required'],
            'gl_code' => ['required', Rule::unique('chart_of_accounts', 'gl_code')->ignore($chart_of_account->id)]
        ]);

        $chart_of_account->name = $request->name;
        $chart_of_account->parent_id = $request->parent_id;
        $chart_of_account->gl_code = $request->gl_code;
        $chart_of_account->account_type = $request->account_type;
        $chart_of_account->allow_manual = $request->allow_manual;
        $chart_of_account->active = $request->active;
        $chart_of_account->notes = $request->notes;
        $chart_of_account->save();
        activity()->on($chart_of_account)
            ->withProperties(['id' => $chart_of_account->id])
            ->log('Update Chart Of Account');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('accounting/chart_of_account');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $chart_of_account = ChartOfAccount::find($id);
        $chart_of_account->delete();
        activity()->on($chart_of_account)
            ->withProperties(['id' => $chart_of_account->id])
            ->log('Delete Chart Of Account');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
