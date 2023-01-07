<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Currency;
use Modules\Loan\Entities\Fund;
use Modules\Loan\Entities\LoanCharge;
use Modules\Loan\Entities\LoanChargeOption;
use Modules\Loan\Entities\LoanChargeType;
use Yajra\DataTables\Facades\DataTables;

class LoanChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:loan.loans.charges.index'])->only(['index', 'show']);
        $this->middleware(['permission:loan.loans.charges.create'])->only(['create', 'store']);
        $this->middleware(['permission:loan.loans.charges.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:loan.loans.charges.destroy'])->only(['destroy']);

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
        $data = LoanCharge::leftJoin('currencies', 'currencies.id', 'loan_charges.currency_id')
            ->leftJoin('loan_charge_types', 'loan_charge_types.id', 'loan_charges.loan_charge_type_id')
            ->leftJoin('loan_charge_options', 'loan_charge_options.id', 'loan_charges.loan_charge_option_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('loan_charges.name', 'like', "%$search%");
            })
            ->selectRaw("loan_charges.*,currencies.name currency,loan_charge_types.name charge_type,loan_charge_options.name charge_option")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('loan::loan_charge.index',compact('data'));
    }

    public function get_charges(Request $request)
    {
        $query = LoanCharge::leftJoin('currencies', 'currencies.id', 'loan_charges.currency_id')
            ->leftJoin('loan_charge_types', 'loan_charge_types.id', 'loan_charges.loan_charge_type_id')
            ->leftJoin('loan_charge_options', 'loan_charge_options.id', 'loan_charges.loan_charge_option_id')
            ->selectRaw("loan_charges.*,currencies.name currency,loan_charge_types.name charge_type,loan_charge_options.name charge_option");

        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('loan.loans.charges.edit')) {
                $action .= '<li><a href="' . url('loan/charge/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('loan.loans.charges.destroy')) {
                $action .= '<li><a href="' . url('loan/charge/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('charge_type', function ($data) {
            if ($data->loan_charge_type_id == 1) {
                return trans_choice('loan::general.disbursement', 1);
            }
            if ($data->loan_charge_type_id == 2) {
                return trans_choice('loan::general.specified_due_date', 1);
            }
            if ($data->loan_charge_type_id == 3) {
                return trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
            }
            if ($data->loan_charge_type_id == 4) {
                return trans_choice('loan::general.overdue', 1) . ' ' . trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 1);
            }
            if ($data->loan_charge_type_id == 5) {
                return trans_choice('loan::general.disbursement_paid_with_repayment', 1);
            }
            if ($data->loan_charge_type_id == 6) {
                return trans_choice('loan::general.loan_rescheduling_fee', 1);
            }
            if ($data->loan_charge_type_id == 7) {
                return trans_choice('loan::general.overdue_on_loan_maturity', 1);
            }
            if ($data->loan_charge_type_id == 8) {
                return trans_choice('loan::general.last_installment_fee', 1);
            }
        })->editColumn('charge_option', function ($data) {
            if ($data->loan_charge_option_id == 1) {
                return number_format($data->amount, 2) . ' ' . trans_choice('loan::general.flat', 1);
            }
            if ($data->loan_charge_option_id == 2) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('loan::general.principal_due_on_installment', 1);
            }
            if ($data->loan_charge_option_id == 3) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('loan::general.principal_interest_due_on_installment', 1);
            }
            if ($data->loan_charge_option_id == 4) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('loan::general.interest_due_on_installment', 1);
            }
            if ($data->loan_charge_option_id == 5) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('loan::general.total_outstanding_loan_principal', 1);
            }
            if ($data->loan_charge_option_id == 6) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1);
            }
            if ($data->loan_charge_option_id == 7) {
                return number_format($data->amount, 2) . ' % ' . trans_choice('loan::general.original_loan_principal', 1);
            }
        })->editColumn('active', function ($data) {
            if ($data->active == 1) {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->active == 0) {
                return trans_choice('core::general.no', 1);
            }
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/charge/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $charge_types = LoanChargeType::orderBy('id')->get();
        $charge_options = LoanChargeOption::orderBy('id')->get();
        $currencies = Currency::orderBy('name')->get();
        return theme_view('loan::loan_charge.create', compact('charge_types', 'charge_options', 'currencies'));
    }

    public function get_charge_types()
    {
        $charge_types = LoanChargeType::orderBy('id')->get();
        return response()->json(['data' => $charge_types]);
    }

    public function get_charge_options()
    {
        $charge_options = LoanChargeOption::orderBy('id')->get();
        return response()->json(['data' => $charge_options]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'currency_id' => ['required'],
            'loan_charge_option_id' => ['required'],
            'loan_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'is_penalty' => ['required'],
            'allow_override' => ['required'],
        ]);
        $loan_charge = new LoanCharge();
        $loan_charge->created_by_id = Auth::id();
        $loan_charge->currency_id = $request->currency_id;
        $loan_charge->loan_charge_type_id = $request->loan_charge_type_id;
        $loan_charge->loan_charge_option_id = $request->loan_charge_option_id;
        $loan_charge->name = $request->name;
        $loan_charge->amount = $request->amount;
        $loan_charge->is_penalty = $request->is_penalty;
        $loan_charge->active = $request->active;
        $loan_charge->allow_override = $request->allow_override;
        $loan_charge->save();
        activity()->on($loan_charge)
            ->withProperties(['id' => $loan_charge->id])
            ->log('Create Loan Charge');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/charge');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_charge = LoanCharge::find($id);
        return theme_view('loan::loan_charge.show', compact('loan_charge'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_charge = LoanCharge::find($id);
        $charge_types = LoanChargeType::orderBy('id')->get();
        $charge_options = LoanChargeOption::orderBy('id')->get();
        $currencies = Currency::orderBy('name')->get();
        return theme_view('loan::loan_charge.edit', compact('charge_types', 'charge_options', 'currencies', 'loan_charge'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'currency_id' => ['required'],
            'loan_charge_option_id' => ['required'],
            'loan_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'is_penalty' => ['required'],
            'allow_override' => ['required'],
        ]);
        $loan_charge = LoanCharge::find($id);
        $loan_charge->currency_id = $request->currency_id;
        $loan_charge->loan_charge_type_id = $request->loan_charge_type_id;
        $loan_charge->loan_charge_option_id = $request->loan_charge_option_id;
        $loan_charge->name = $request->name;
        $loan_charge->amount = $request->amount;
        $loan_charge->is_penalty = $request->is_penalty;
        $loan_charge->active = $request->active;
        $loan_charge->allow_override = $request->allow_override;
        $loan_charge->save();
        activity()->on($loan_charge)
            ->withProperties(['id' => $loan_charge->id])
            ->log('Update Loan Charge');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/charge');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_charge = LoanCharge::find($id);
        $loan_charge->delete();
        activity()->on($loan_charge)
            ->withProperties(['id' => $loan_charge->id])
            ->log('Delete Loan Charge');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
