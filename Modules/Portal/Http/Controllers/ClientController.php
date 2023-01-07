<?php

namespace Modules\Portal\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientUser;
use Modules\CustomField\Entities\CustomField;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $clients = ClientUser::with('client')->where('user_id', Auth::id())->get();
        $client = Client::where('id', session('client_id'))->first();
        if(empty($client)){
            Flash::warning(trans('loan::general.client_not_found_please_logout'));
            return redirect()->back();
        }
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();

        return theme_view('portal::client.show', compact('client', 'clients','custom_fields'));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function switch_client(Request $request)
    {
        session(['client_id' => $request->client_id]);
        Flash::success(trans_choice('core::general.successfully_saved', 1));
        return redirect()->back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('portal::client.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return theme_view('portal::client.edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
