<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientIdentification;
use Modules\Client\Entities\ClientIdentificationType;

class ClientIdentificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.identification.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.identification.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.identification.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.identification.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('client::client_identification.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        $client_identification_types = ClientIdentificationType::all();
        return theme_view('client::client_identification.create', compact('client_identification_types', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'client_identification_type_id' => ['required'],
            'identification_value' => ['required'],
            'photo' => ['nullable','file', 'mimes:jpg,jpeg,png,pdf'],
        ]);
        $client_identification = new ClientIdentification();
        $client_identification->created_by_id = Auth::id();
        $client_identification->client_id = $id;
        $client_identification->identification_value = $request->identification_value;
        $client_identification->client_identification_type_id = $request->client_identification_type_id;
        $client_identification->description = $request->description;
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads/clients');
            $client_identification->link = basename($file_name);
        }
        $client_identification->save();
        activity()->on($client_identification)
            ->withProperties(['id' => $client_identification->id])
            ->log('Create Client Identification');
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
        $client_identification = ClientIdentification::find($id);
        return theme_view('client::client_identification.show', compact('client_identification'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_identification_types = ClientIdentificationType::all();
        $client_identification = ClientIdentification::find($id);
        return theme_view('client::client_identification.edit', compact('client_identification','client_identification_types'));
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
            'client_identification_type_id' => ['required'],
            'identification_value' => ['required'],
            'photo' => ['nullable','file', 'mimes:jpg,jpeg,png,pdf'],
        ]);
        $client_identification = ClientIdentification::find($id);
        $client_identification->identification_value = $request->identification_value;
        $client_identification->client_identification_type_id = $request->client_identification_type_id;
        $client_identification->description = $request->description;
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads/clients');
            if ($client_identification->link) {
                Storage::delete('public/uploads/clients/' . $client_identification->link);
            }
            $client_identification->link = basename($file_name);
        }
        $client_identification->save();
        activity()->on($client_identification)
            ->withProperties(['id' => $client_identification->id])
            ->log('Update Client Identification');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/' . $client_identification->client_id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $client_identification = ClientIdentification::find($id);
        if ($client_identification->link) {
            Storage::delete('public/uploads/clients/' . $client_identification->link);
        }
        $client_identification->delete();
        activity()->on($client_identification)
            ->withProperties(['id' => $client_identification->id])
            ->log('Delete Client Identification');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
