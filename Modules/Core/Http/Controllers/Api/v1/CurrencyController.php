<?php

namespace Modules\Core\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Currency;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:core.currencies.index'])->only(['index', 'show']);
        $this->middleware(['permission:core.currencies.create'])->only(['create', 'store']);
        $this->middleware(['permission:core.currencies.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:core.currencies.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $data = Currency::paginate($limit);
        return response()->json([$data]);
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
            'symbol' => ['required'],
            'code' => ['required'],
            'position' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $currency = new Currency();
            $currency->name = $request->name;
            $currency->symbol = $request->symbol;
            $currency->code = $request->code;
            $currency->position = $request->position;
            $currency->save();
            return response()->json(['data' => $currency, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $currency = Currency::find($id);
        return response()->json(['data' => $currency]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $currency = Currency::find($id);
        return response()->json(['data' => $currency]);
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
            'symbol' => ['required'],
            'code' => ['required'],
            'position' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $currency = Currency::find($id);
            $currency->name = $request->name;
            $currency->symbol = $request->symbol;
            $currency->position = $request->position;
            $currency->save();
            return response()->json(['data' => $currency, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        Currency::destroy($id);
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);
    }
}
