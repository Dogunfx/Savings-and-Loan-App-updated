<?php
/**
 * Created by PhpStorm.
 * User: Tj
 * Date: 10/12/2018
 * Time: 20:36
 */

namespace App\Http\Controllers\Portal;


use App\Http\Controllers\Controller;
use App\Models\CustomFieldMeta;
use App\Models\SavingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Yajra\DataTables\Facades\DataTables;

class SavingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel']);
    }

    public function index(Request $request)
    {

        if (!empty($request->status)) {
            $status = $request->status;
        } else {
            $status = "";
        }

        return view('portal.saving.data', compact('status'));
    }

    public function get_savings(Request $request)
    {
        if (!empty($request->status)) {
            $status = $request->status;
        } else {
            $status = null;
        }

        $query = DB::table("savings")->leftJoin("borrowers", "borrowers.id", "savings.borrower_id")->leftJoin("savings_products", "savings_products.id", "savings.savings_product_id")->selectRaw(DB::raw("borrowers.first_name,borrowers.last_name,savings.id,savings.borrower_id,savings_products.name savings_product,savings.status,(SELECT SUM(credit) FROM savings_transactions WHERE savings_transactions.savings_id=savings.id) credit,(SELECT SUM(debit) FROM savings_transactions WHERE savings_transactions.savings_id=savings.id) debit"))->where('savings.branch_id', session('branch_id'))->when($status, function ($query) use ($status) {
            $query->where("savings.status", $status);
        })->where("savings.borrower_id", session('borrower_id'))->groupBy("savings.id");
        return DataTables::of($query)->editColumn('borrower', function ($data) {
            return '<a href="' . url('portal/borrower/' . $data->borrower_id . '/show') . '">' . $data->first_name . ' ' . $data->last_name . '</a>';
        })->editColumn('balance', function ($data) {
            return number_format($data->credit - $data->debit, 2);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-warning">' . trans_choice('general.awaiting', 1) . ' ' . trans_choice('general.activation', 1) . '</span>';
            }
            if ($data->status == 'disbursed') {
                return '<span class="label label-info">' . trans_choice('general.active', 1) . '</span>';
            }
            if ($data->status == 'declined') {
                return '<span class="label label-danger">' . trans_choice('general.declined', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('general.withdrawn', 1) . '</span>';
            }
            if ($data->status == 'written_off') {
                return '<span class="label label-danger">' . trans_choice('general.written_off', 1) . '</span>';
            }
            if ($data->status == 'closed') {
                return '<span class="label label-success">' . trans_choice('general.closed', 1) . '</span>';
            }
            if ($data->status == 'pending_reschedule') {
                return '<span class="label label-warning">' . trans_choice('general.pending_reschedule', 1) . '</span>';
            }
            if ($data->status == 'rescheduled') {
                return '<span class="label label-info">' . trans_choice('general.rescheduled', 1) . '</span>';
            }

        })->editColumn('action', function ($data) {
            $action = '<ul class="icons-list"><li class="dropdown">  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a> <ul class="dropdown-menu dropdown-menu-right" role="menu">';

            $action .= '<li><a href="' . url('portal/saving/' . $data->id . '/show') . '" class="">' . trans_choice('general.detail', 2) . '</a></li>';

            $action .= "</ul></li></ul>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('portal/saving/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';

        })->rawColumns(['id', 'borrower', 'action', 'status'])->make(true);
    }

    public function show($saving)
    {
        if (session('borrower_id') != $saving->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        //$transactions = SavingTransaction::where('savings_id', $saving->id)->orderBy('date', 'desc')->orderBy('time','desc')->get();
        $transactions = array();
        $balance = 0;
        foreach (SavingTransaction::where('savings_id', $saving->id)->orderBy('date', 'asc')->orderBy('time',
            'asc')->get() as $key) {
            $savings_transactions = array();
            if ($key->type == 'deposit' || $key->type == 'interest' || $key->type == 'dividend' || $key->type == 'guarantee_restored') {
                $balance = $balance + $key->amount;
            } else {
                $balance = $balance - $key->amount;
            }
            $savings_transactions['id'] = $key->id;
            $savings_transactions['type'] = $key->type;
            $savings_transactions['time'] = $key->time;
            $savings_transactions['date'] = $key->date;
            $savings_transactions['amount'] = $key->amount;
            $savings_transactions['notes'] = $key->notes;
            $savings_transactions['user'] = $key->user;
            $savings_transactions['balance'] = $balance;
            array_push($transactions, $savings_transactions);
        }
        $transactions = array_reverse($transactions);
        $custom_fields = CustomFieldMeta::where('category', 'savings')->where('parent_id',
            $saving->id)->get();
        return view('portal.saving.show', compact('saving', 'custom_fields', 'transactions'));
    }

    public function printStatement($saving)
    {
        if (session('borrower_id') != $saving->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $custom_fields = CustomFieldMeta::where('category', 'savings')->where('parent_id',
            $saving->id)->get();
        return view('portal.saving.print', compact('saving', 'custom_fields', 'transactions'));
    }

    public function pdfStatement($saving)
    {
        if (session('borrower_id') != $saving->borrower_id) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $custom_fields = CustomFieldMeta::where('category', 'savings')->where('parent_id',
            $saving->id)->get();
        $pdf = PDF::loadView('portal.saving.pdf_statement',
            compact('saving', 'custom_fields', 'transactions'));
        return $pdf->download($saving->borrower->title . ' ' . $saving->borrower->first_name . ' ' . $saving->borrower->last_name . " - Savings Statement.pdf");

    }
}