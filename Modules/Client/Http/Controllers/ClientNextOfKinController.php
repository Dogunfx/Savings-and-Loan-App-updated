<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\ClientNextOfKin;
use Modules\Client\Entities\ClientRelationship;
use Modules\Client\Entities\Profession;
use Modules\Client\Entities\Title;
use Modules\Core\Entities\Country;

class ClientNextOfKinController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.next_of_kin.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.next_of_kin.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.next_of_kin.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.next_of_kin.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('client::client_next_of_kin.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        $titles = Title::all();
        $professions = Profession::all();
        $client_relationships = ClientRelationship::all();
        $countries = Country::all();
        return theme_view('client::client_next_of_kin.create',compact('titles','professions','client_relationships','countries','id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request,$id)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'mobile' => ['numeric'],
            'client_relationship_id' => ['required'],
            'email' => ['string', 'email', 'max:255'],
            'dob' => [ 'date'],
            'photo' => ['nullable','image', 'mimes:jpg,jpeg,png'],
        ]);
        $client_next_of_kin = new ClientNextOfKin();
        $client_next_of_kin->first_name = $request->first_name;
        $client_next_of_kin->last_name = $request->last_name;
        $client_next_of_kin->client_id = $id;
        $client_next_of_kin->created_by_id = Auth::id();
        $client_next_of_kin->gender = $request->gender;
        $client_next_of_kin->country_id = $request->country_id;
        $client_next_of_kin->client_relationship_id = $request->client_relationship_id;
        $client_next_of_kin->title_id = $request->title_id;
        $client_next_of_kin->profession_id = $request->profession_id;
        $client_next_of_kin->marital_status = $request->marital_status;
        $client_next_of_kin->mobile = $request->mobile;
        $client_next_of_kin->notes = $request->notes;
        $client_next_of_kin->email = $request->email;
        $client_next_of_kin->address = $request->address;
        $request->dob ? $client_next_of_kin->dob = $request->dob : '';
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads/clients');
            $client_next_of_kin->photo = basename($file_name);
        }
        $client_next_of_kin->save();
        activity()->on($client_next_of_kin)
            ->withProperties(['id' => $client_next_of_kin->id])
            ->log('Create Client Next Of Kin');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/'.$id.'/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_next_of_kin=ClientNextOfKin::find($id);
        return theme_view('client::client_next_of_kin.show',compact('client_next_of_kin'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $titles = Title::all();
        $professions = Profession::all();
        $client_relationships = ClientRelationship::all();
        $countries = Country::all();
        $client_next_of_kin=ClientNextOfKin::find($id);
        return theme_view('client::client_next_of_kin.edit',compact('titles','professions','client_relationships','countries','client_next_of_kin'));
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
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'mobile' => ['numeric'],
            'client_relationship_id' => ['required'],
            'email' => ['string', 'email', 'max:255'],
            'dob' => [ 'date'],
            'photo' => ['nullable','image', 'mimes:jpg,jpeg,png'],
        ]);
        $client_next_of_kin =  ClientNextOfKin::find($id);
        $client_next_of_kin->first_name = $request->first_name;
        $client_next_of_kin->last_name = $request->last_name;
        $client_next_of_kin->gender = $request->gender;
        $client_next_of_kin->country_id = $request->country_id;
        $client_next_of_kin->client_relationship_id = $request->client_relationship_id;
        $client_next_of_kin->title_id = $request->title_id;
        $client_next_of_kin->profession_id = $request->profession_id;
        $client_next_of_kin->mobile = $request->mobile;
        $client_next_of_kin->marital_status = $request->marital_status;
        $client_next_of_kin->notes = $request->notes;
        $client_next_of_kin->email = $request->email;
        $client_next_of_kin->address = $request->address;
        $request->dob ? $client_next_of_kin->dob = $request->dob : '';
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads/clients');
            if ($client_next_of_kin->photo) {
                Storage::delete('public/uploads/clients/' . $client_next_of_kin->photo);
            }
            $client_next_of_kin->photo = basename($file_name);
        }
        $client_next_of_kin->save();
        activity()->on($client_next_of_kin)
            ->withProperties(['id' => $client_next_of_kin->id])
            ->log('Update Client Next Of Kin');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/'.$client_next_of_kin->client_id.'/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $client_next_of_kin =  ClientNextOfKin::find($id);
        if ($client_next_of_kin->photo) {
            Storage::delete('public/uploads/clients/' . $client_next_of_kin->photo);
        }
        $client_next_of_kin->delete();
        activity()->on($client_next_of_kin)
            ->withProperties(['id' => $client_next_of_kin->id])
            ->log('Delete Client Next Of Kin');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}
