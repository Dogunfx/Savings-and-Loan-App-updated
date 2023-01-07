<?php

namespace Modules\CustomField\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\CustomField\Entities\CustomField;
use Modules\User\Entities\User;
use Yajra\DataTables\Facades\DataTables;

class CustomFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','2fa']);
        $this->middleware(['permission:customfield.custom_fields.index'])->only(['index', 'show']);
        $this->middleware(['permission:customfield.custom_fields.create'])->only(['create', 'store']);
        $this->middleware(['permission:customfield.custom_fields.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:customfield.custom_fields.destroy'])->only(['destroy']);

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
        $data = CustomField::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('customfield::custom_field.index',compact('data'));
    }

    public function get_custom_fields(Request $request)
    {


        $query = CustomField::query();
        return DataTables::of($query)->editColumn('user', function ($data) {
            return $data->first_name . ' ' . $data->last_name;
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('customfield.custom_fields.edit')) {
                $action .= '<li><a href="' . url('custom_field/' . $data->id . '/edit') . '" class="">' . trans_choice('user::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('customfield.custom_fields.destroy')) {
                $action .= '<li><a href="' . url('custom_field/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('user::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('custom_field/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->editColumn('category', function ($data) {
            if ($data->category == "add_client") {
                return trans_choice('core::general.add', 1) . ' ' . trans_choice('client::general.client', 1);
            }
            if ($data->category == "add_loan") {
                return trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.loan', 1);
            }
            if ($data->category == "add_repayment") {
                return trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.repayment', 1);
            }
            if ($data->category == "add_savings") {
                return trans_choice('core::general.add', 1) . ' ' . trans_choice('savings::general.savings', 1);
            }
            if ($data->category == "add_collateral") {
                return trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.collateral', 1);
            }
            if ($data->category == "add_user") {
                return trans_choice('core::general.add', 1) . ' ' . trans_choice('user::general.user', 1);
            }
            if ($data->category == "add_branch") {
                return trans_choice('core::general.add', 1) . ' ' . trans_choice('core::general.branch', 1);
            }
            if ($data->category == "add_journal_entry") {
                return ttrans_choice('core::general.add', 1) . ' ' . trans_choice('accounting::general.journal', 1);
            }


        })->editColumn('type', function ($data) {
            if ($data->type == "textfield") {
                return trans_choice('customfield::general.textfield', 1);
            }
            if ($data->type == "select") {
                return trans_choice('customfield::general.select', 1);
            }
            if ($data->type == "number") {
                return trans_choice('customfield::general.number', 1);
            }
            if ($data->type == "date") {
                return trans_choice('customfield::general.date', 1);
            }
            if ($data->type == "checkbox") {
                return trans_choice('customfield::general.checkbox', 1);
            }
            if ($data->type == "textarea") {
                return trans_choice('customfield::general.textarea', 1);
            }
            if ($data->type == "select_db") {
                return trans_choice('customfield::general.select_db', 1);
            }
        })->editColumn('required', function ($data) {
            if ($data->required == "1") {
                return trans_choice('core::general.yes', 1);
            }
            if ($data->required == "0") {
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
        $categories = [
            "add_client" => trans_choice('core::general.add', 1) . ' ' . trans_choice('client::general.client', 1),
            "add_loan" => trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.loan', 1),
            "add_repayment" => trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.repayment', 1),
            "add_savings" => trans_choice('core::general.add', 1) . ' ' . trans_choice('savings::general.savings', 1),
            "add_collateral" => trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.collateral', 1),
            "add_user" => trans_choice('core::general.add', 1) . ' ' . trans_choice('user::general.user', 1),
            "add_branch" => trans_choice('core::general.add', 1) . ' ' . trans_choice('core::general.branch', 1),
            "add_journal_entry" => trans_choice('core::general.add', 1) . ' ' . trans_choice('accounting::general.journal', 1),
        ];
        $types = [
            "textfield" => trans_choice('customfield::general.textfield', 1),
            "select" => trans_choice('customfield::general.select', 1),
            "number" => trans_choice('customfield::general.number', 1),
            "date" => trans_choice('customfield::general.date', 1),
            "checkbox" => trans_choice('customfield::general.checkbox', 1),
            "textarea" => trans_choice('customfield::general.textarea', 1),
            "select_db" => trans_choice('customfield::general.select_db', 1),
            "radio" => trans_choice('customfield::general.radio', 1),
        ];
        \JavaScript::put([
            "categories" => $categories,
            "types" => $types
        ]);
        return theme_view('customfield::custom_field.create');
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
            'category' => ['required'],
            'type' => ['required'],
            'active' => ['required'],
        ]);
        $custom_field = new CustomField();
        $custom_field->created_by_id = Auth::id();
        $custom_field->category = $request->category;
        $custom_field->type = $request->type;
        $custom_field->name = $request->name;
        $custom_field->label = $request->label;
        $custom_field->options = $request->options;
        $custom_field->class = $request->classes;
        $custom_field->db_columns = $request->db_columns;
        $custom_field->rules = $request->rules;
        $custom_field->default_values = $request->default_values;
        $custom_field->required = $request->required;
        $custom_field->active = $request->active;
        $custom_field->save();
        activity()->on($custom_field)
            ->withProperties(['id' => $custom_field->id])
            ->log('Create Custom Field');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('custom_field');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $custom_field = CustomField::find($id);
        return theme_view('customfield::custom_field.show', compact('custom_field'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $categories = [
            "add_client" => trans_choice('core::general.add', 1) . ' ' . trans_choice('client::general.client', 1),
            "add_loan" => trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.loan', 1),
            "add_repayment" => trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.repayment', 1),
            "add_savings" => trans_choice('core::general.add', 1) . ' ' . trans_choice('savings::general.savings', 1),
            "add_collateral" => trans_choice('core::general.add', 1) . ' ' . trans_choice('loan::general.collateral', 1),
            "add_user" => trans_choice('core::general.add', 1) . ' ' . trans_choice('user::general.user', 1),
            "add_branch" => trans_choice('core::general.add', 1) . ' ' . trans_choice('core::general.branch', 1),
            "add_journal_entry" => trans_choice('core::general.add', 1) . ' ' . trans_choice('accounting::general.journal', 1),
        ];
        $types = [
            "textfield" => trans_choice('customfield::general.textfield', 1),
            "select" => trans_choice('customfield::general.select', 1),
            "number" => trans_choice('customfield::general.number', 1),
            "date" => trans_choice('customfield::general.date', 1),
            "checkbox" => trans_choice('customfield::general.checkbox', 1),
            "textarea" => trans_choice('customfield::general.textarea', 1),
            "select_db" => trans_choice('customfield::general.select_db', 1),
            "radio" => trans_choice('customfield::general.radio', 1),
        ];
        \JavaScript::put([
            "categories" => $categories,
            "types" => $types
        ]);
        $custom_field = CustomField::find($id);
        return theme_view('customfield::custom_field.edit', compact('custom_field'));
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
            'name' => ['required'],
            'category' => ['required'],
            'type' => ['required'],
            'active' => ['required'],
        ]);
        $custom_field = CustomField::find($id);
        $custom_field->category = $request->category;
        $custom_field->type = $request->type;
        $custom_field->name = $request->name;
        $custom_field->label = $request->label;
        $custom_field->options = $request->options;
        $custom_field->class = $request->classes;
        $custom_field->db_columns = $request->db_columns;
        $custom_field->rules = $request->rules;
        $custom_field->default_values = $request->default_values;
        $custom_field->required = $request->required;
        $custom_field->active = $request->active;
        $custom_field->save();
        activity()->on($custom_field)
            ->withProperties(['id' => $custom_field->id])
            ->log('Update Custom Field');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('custom_field');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $custom_field = CustomField::find($id);
        $custom_field->delete();
        activity()->on($custom_field)
            ->withProperties(['id' => $custom_field->id])
            ->log('Delete Custom Field');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
