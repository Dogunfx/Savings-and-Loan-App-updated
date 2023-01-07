<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientFile;

class ClientFileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.files.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.files.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.files.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.files.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('client::file.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        return theme_view('client::file.create',compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request,$id)
    {
        $request->validate([
            'name' => ['required'],
            'file' => ['required','file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
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
        activity()->on($client_file)
            ->withProperties(['id' => $client_file->id])
            ->log('Create Client File');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/' . $id . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_file = ClientFile::find($id);
        return theme_view('client::file.show',compact('client_file'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_file = ClientFile::find($id);
        return theme_view('client::file.edit',compact('client_file'));
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
            'file' => ['nullable','file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        $client_file =  ClientFile::find($id);
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
        activity()->on($client_file)
            ->withProperties(['id' => $client_file->id])
            ->log('Update Client File');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/' . $client_file->client_id . '/show');
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
        activity()->on($client_file)
            ->withProperties(['id' => $client_file->id])
            ->log('Delete Client File');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
