<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Menu;
use Modules\Core\Entities\PaymentGateway;
use Modules\Core\Entities\PaymentType;
use Modules\Setting\Entities\Setting;
use Nwidart\Modules\Facades\Module;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PaymentGatewayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:core.payment_gateways.index'])->only(['index', 'show']);
        $this->middleware(['permission:core.payment_gateways.create'])->only(['create', 'store', 'upload']);
        $this->middleware(['permission:core.payment_gateways.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $installed_payment_gateways = PaymentType::where('is_online',1)->get();
        $data = [];
        foreach (Module::getOrdered() as $key) {
            if ($key->get('is_payment_gateway')) {
                $data[] = $key;
            }
        }

        return theme_view('core::payment_gateway.index', compact('data', 'installed_payment_gateways'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function install(Request $request)
    {
        $module = Module::find($request->module);
        $class = 'Modules\\' . $module->getName() . '\\' . $module->getName();
        if (class_exists($class)) {
            $gateway_class = new $class;
            $gateway_class->install();
            Flash::success(trans_choice("core::general.successfully_saved", 1));
        } else {
            Flash::danger(trans_choice("core::general.payment_gateway_is_broken", 1));
        }
        return redirect()->back();

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
        return theme_view('core::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return theme_view('core::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request)
    {
        $module = Module::find($request->module);
        $class = 'Modules\\' . $module->getName() . '\\' . $module->getName();
        if (class_exists($class)) {
            $gateway_class = new $class;
            foreach ($gateway_class->getSettings() as $key) {
                $field = "field_" . $key['id'];
                if ($key['rules']) {
                    $rules = [$field => $key['rules']];
                    $request->validate($rules, [], [$field => $key['name']]);
                }
                $setting = Setting::find($key['id']);
                if (!empty($setting)) {
                    $setting->setting_value = $request->$field;
                    $setting->save();
                }else{
                    //insert the setting
                    $key['setting_value']=$request->$field;
                    unset($key['id']);
                    DB::table('settings')->insert($key);
                }
            }
            Flash::success(trans_choice("core::general.successfully_saved", 1));
        } else {
            Flash::danger(trans_choice("core::general.payment_gateway_is_broken", 1));
        }
        return redirect()->back();
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
