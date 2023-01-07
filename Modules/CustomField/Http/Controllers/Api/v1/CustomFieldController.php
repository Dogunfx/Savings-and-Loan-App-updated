<?php

namespace Modules\CustomField\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\CustomField\Entities\CustomField;
use Modules\User\Entities\User;
use Yajra\DataTables\Facades\DataTables;

class CustomFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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
        $limit = $request->limit ? $request->limit : 20;
        $data = CustomField::paginate($limit);
        return response()->json([$data]);
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

        return response()->json(['categories' => $categories, 'types' => $types]);
    }

    public function get_categories()
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


        return response()->json(['data' => $categories]);
    }

    public function get_types()
    {
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

        return response()->json(['data' => $types]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'category' => ['required'],
            'type' => ['required'],
            'active' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
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
            return response()->json(['data' => $custom_field, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $custom_field = CustomField::find($id);
        return response()->json(['data' => $custom_field]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $custom_field = CustomField::find($id);
        return response()->json(['data' => $custom_field]);
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
            'category' => ['required'],
            'type' => ['required'],
            'active' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
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
            return response()->json(['data' => $custom_field, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        CustomField::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
