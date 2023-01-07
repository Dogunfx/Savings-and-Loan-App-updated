<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Menu;
use Nwidart\Modules\Facades\Module;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:core.modules.index'])->only(['index', 'show']);
        $this->middleware(['permission:core.modules.create'])->only(['create', 'store', 'upload']);
        $this->middleware(['permission:core.modules.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->action) {
            $module = Module::find($request->name);
            if ($request->action == 'disable') {
                if ($module->get('is_core') == 1) {
                    \flash(trans_choice("core::general.cannot_disable_core_module", 1))->error()->important();
                    return redirect()->back();
                }
                $module->disable();
                activity()->on($module)
                    ->log('Disable Module:' . $request->name);
                \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
                return redirect()->back();
            }
            if ($request->action == 'enable') {
                $module->enable();
                activity()->on($module)
                    ->log('Enable Module:' . $request->name);
                \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
                return redirect()->back();
            }
            if ($request->action == 'reconfigure') {
                //check if all permissions have been installed
                $permissions = config($module->getLowerName() . '.permissions');
                $permissions_added = 0;
                if ($permissions) {
                    foreach ($permissions as $key) {
                        if (!Permission::where('name', $key['name'])->first()) {
                            Permission::create($key);
                            $permissions_added++;
                        }
                    }
                }
                $admin = Role::findByName('admin');
                $admin->syncPermissions(Permission::all());
                //reconfigure menu
                $menus = config($module->getLowerName() . '.menus');
                Menu::where('module', $module->getName())->delete();
                if ($menus) {
                    foreach ($menus as $menu) {
                        $m = new Menu();
                        $m->is_parent = $menu['is_parent'];
                        if ($menu['is_parent'] != 1) {
                            //find the parent
                            $parent = Menu::where('slug', $menu['parent_slug'])->first();
                            if (!empty($parent)) {
                                $m->parent_id = $parent->id;
                            }
                        }
                        $m->parent_slug = $menu['parent_slug'];
                        $m->name = $menu['name'];
                        $m->slug = $menu['slug'];
                        $m->module = $menu['module'];
                        $m->url = $menu['url'];
                        $m->icon = $menu['icon'];
                        $m->menu_order = $menu['order'];
                        $m->permissions = $menu['permissions'];
                        $m->save();
                    }
                }
                activity()->log('Reconfigure Module:' . $request->name);
                \flash(trans_choice("core::general.successfully_saved", 1) . ". Added $permissions_added permissions")->success()->important();
                return redirect()->back();
            }
        }
        $data = Module::getOrdered();
        return theme_view('core::module.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function upload()
    {
        return theme_view('core::module.upload');
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
