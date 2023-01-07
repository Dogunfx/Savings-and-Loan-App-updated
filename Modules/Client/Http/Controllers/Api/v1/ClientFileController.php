<?php

namespace Modules\Client\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientFile;

class ClientFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['permission:client.clients.files.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.files.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.files.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.files.destroy'])->only(['destroy']);

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client_file = new ClientFile();
            $client_file->created_by_id = Auth::id();
            $client_file->client_id = $id;
            $client_file->name = $request->name;
            $client_file->description = $request->description;
            if ($request->hasFile('file')) {
                $file_name = $request->file('file')->store('public/uploads/clients');
                $client_file->link = basename($file_name);
            }
            $client_file->save();
            return response()->json(['data' => $client_file, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_file = ClientFile::find($id);
        return response()->json(['data' => $client_file]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_file = ClientFile::find($id);
        return response()->json(['data' => $client_file]);
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
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        if ($validator->fails()) {
            return response()->json(["success" => false, "errors" => $validator->errors()], 400);
        } else {
            $client_file = ClientFile::find($id);
            $client_file->name = $request->name;
            $client_file->description = $request->description;
            if ($request->hasFile('file')) {
                $file_name = $request->file('file')->store('public/uploads/clients');
                if ($client_file->link) {
                    Storage::delete('public/uploads/clients/' . $client_file->link);
                }
                $client_file->link = basename($file_name);
            }
            $client_file->save();
            return response()->json(['data' => $client_file, "message" => trans_choice("core::general.successfully_saved", 1), "success" => true]);

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $client_file =  ClientFile::find($id);
        if ($client_file->link) {
            Storage::delete('public/uploads/clients/' . $client_file->link);
        }
        $client_file->delete();
        return response()->json(["success" => true, "message" => trans_choice("core::general.successfully_deleted", 1)]);

    }
}
