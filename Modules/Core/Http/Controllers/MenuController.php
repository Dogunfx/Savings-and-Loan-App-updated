<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Menu;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:core.menu.index'])->only(['index', 'show', 'update']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $data = Menu::with('children')->where('is_parent', 1)->orderBy('menu_order', 'asc')->get();
        return theme_view('core::menu.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('core::create');
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
        $data = $request->data;
        $p = 0;
        foreach ($data[0] as $key) {
            $menu = Menu::find($key['id']);
            $menu->is_parent = 1;
            $menu->name = $key['name'];
            $menu->menu_order = $p;
            $menu->save();
            $c = 0;
            if (array_key_exists('children', $key)) {
                foreach ($key['children'][0] as $child) {
                    $menu = Menu::find($child['id']);
                    $menu->parent_id = $key['id'];
                    $menu->is_parent = 0;
                    $menu->name = $child['name'];
                    $menu->menu_order = $c;
                    $menu->save();
                    $c++;
                }
            }
            $p++;
        }
        activity()->log('Update Menu');
        return response()->json(['success' => 1, 'msg' => trans_choice("core::general.successfully_saved", 1)]);
    }

}
