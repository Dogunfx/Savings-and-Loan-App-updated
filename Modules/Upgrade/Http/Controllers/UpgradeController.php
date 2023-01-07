<?php

namespace Modules\Upgrade\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use Modules\Upgrade\Jobs\UpgradeFromV2;

class UpgradeController extends Controller
{
    /**
     * SettingController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:upgrade.upgrades.index'])->only(['index', 'show']);
        $this->middleware(['permission:upgrade.upgrades.manage'])->only(['create', 'store']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        config(['database.old_mysql.database' => 'America/Chicago']);
        return theme_view('upgrade::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function v2(Request $request)
    {
        $upgrade_done = false;
        if (Storage::exists('v2upgrade')) {
            $upgrade_done = true;
        }
        if ($request->isMethod('POST')) {
            $path = base_path('.env');
            $env = file($path);
            $env = str_replace('OLD_DB_HOST=' . env('OLD_DB_HOST'), 'OLD_DB_HOST=' . $request->host, $env);
            $env = str_replace('OLD_DB_DATABASE=' . env('OLD_DB_DATABASE'), 'OLD_DB_DATABASE=' . $request->name, $env);
            $env = str_replace('OLD_DB_USERNAME=' . env('OLD_DB_USERNAME'), 'OLD_DB_USERNAME=' . $request->username, $env);
            $env = str_replace('OLD_DB_PASSWORD=' . env('OLD_DB_PASSWORD'), 'OLD_DB_PASSWORD=' . $request->password, $env);
            $env = str_replace('OLD_DB_PORT=' . env('OLD_DB_PORT'), 'OLD_DB_PORT=' . $request->port, $env);
            file_put_contents($path, $env);
            try {
                DB::connection()->statement("SHOW TABLES");
                //fire update job
                UpgradeFromV2::dispatch();
                Flash::success('Your upgrade is now running in background. You will receive an email when its complete.');
                return redirect('upgrade');
           } catch (\Exception $e) {
                Log::info($e->getMessage());
                Flash::warning(trans('Connection failed, please check the details'));
                return redirect()->back();
            }

            return redirect('upgrade');
        }
        return theme_view('upgrade::v2', compact('upgrade_done'));
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

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('upgrade::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return theme_view('upgrade::edit');
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
