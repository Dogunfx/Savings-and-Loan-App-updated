<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Setting\Entities\Setting;
use Nwidart\Modules\Facades\Module;

class SettingController extends Controller
{
    /**
     * SettingController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:setting.setting.index'])->only(['index','show']);
        $this->middleware(['permission:setting.setting.create'])->only(['create','store']);
        $this->middleware(['permission:setting.setting.edit'])->only(['edit','update']);
        $this->middleware(['permission:setting.setting.destroy'])->only(['destroy']);

    }
    public function organisation()
    {
        $data=[];
        $modules = Module::collections();
        foreach ($modules as $module) {
            if (file_exists(Module::getPath($module) . '/' . $module . '/Settings/SettingsLinks.php')) {
                $class = "\\Modules\\" . $module . "\\Settings\\SettingsLinks";
                $class = new $class($data);
                $data[] = $class->get_links();

            }
        }
        return theme_view('setting::setting.organisation',compact('data'));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data = Setting::get();
        return theme_view('setting::setting.index', compact('data'));
    }

    public function general()
    {

        $data = Setting::where('category', 'general')->where('displayed', 1)->get();
        return theme_view('setting::setting.general', compact('data'));
    }

    public function email()
    {
        $data = Setting::where('category', 'email')->where('displayed', 1)->get();
        return theme_view('setting::setting.email', compact('data'));
    }

    public function sms()
    {
        $data = Setting::where('category', 'sms')->where('displayed', 1)->get();
        return theme_view('setting::setting.sms', compact('data'));
    }

    public function system()
    {
        $data = Setting::where('category', 'system')->where('displayed', 1)->get();
        return theme_view('setting::setting.system', compact('data'));
    }

    public function other()
    {
        $data = Setting::where('category', 'other')->where('displayed', 1)->get();
        return theme_view('setting::setting.other', compact('data'));
    }

    public function system_update()
    {
        $data = Setting::where('category', 'update')->where('displayed', 1)->get();
        return theme_view('setting::setting.update', compact('data'));
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        foreach (Setting::where('category', $request->category)->where('displayed', 1)->get() as $key) {
            $field = "field_" . $key->id;
            if ($key->rules) {
                $rules = [$field => $key->rules];
                $request->validate($rules, [], [$field => $key->name]);
            }
            $key->setting_value = $request->$field;
            if ($key->type == 'file') {
                if ($request->hasFile($field)) {
                    $file_name = $request->file($field)->store('public/uploads');
                    $key->setting_value = basename($file_name);
                }
            }
            $key->save();
        }
        activity()->log('Update Settings');
        Flash::success(trans_choice("core::general.successfully_saved", 1));
        return redirect()->back();
    }


}
