<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ReportController extends Controller
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
        return theme_view('client::report.index');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

}
