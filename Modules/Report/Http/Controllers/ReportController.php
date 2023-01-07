<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Nwidart\Modules\Facades\Module;

class ReportController extends Controller
{
    /**
     * ReportController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:reports.index'])->only(['index','show']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data = [];
        $modules = Module::collections();
        foreach ($modules as $module) {
            if (file_exists(Module::getPath($module) . '/' . $module . '/Reports/ReportsLinks.php')) {
                $class = "\\Modules\\" . $module . "\\Reports\\ReportsLinks";
                $class = new $class($data);
                $data[] = $class->get_links();

            }
        }
        return theme_view('report::index',compact('data'));
    }


}
