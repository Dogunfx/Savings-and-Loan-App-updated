<?php

namespace Modules\ActivityLog\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:activitylog.activity_logs.index'])->only(['index', 'show', 'get_activity_logs']);
        $this->middleware(['permission:activitylog.activity_logs.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $causer_id = $request->causer_id;
        $search = $request->s;
        $data = Activity::leftJoin('users', 'users.id', 'activity_log.causer_id')
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('activity_log.id', 'like', "%$search%");
                $query->orWhere('activity_log.description', 'like', "%$search%");
                $query->orWhere('activity_log.causer_id', 'like', "%$search%");
                $query->orWhere('users.first_name', 'like', "%$search%");
                $query->orWhere('users.last_name', 'like', "%$search%");
            })
            ->when($causer_id, function ($query) use ($causer_id) {
                $query->where('activity_log.causer_id', $causer_id);
            })
            ->selectRaw("activity_log.*,concat(users.first_name,' ',users.last_name) user")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('activitylog::activity_log.index', compact('data'));
    }

    public function get_activity_logs(Request $request)
    {
        $causer_id = $request->causer_id;
        $query = Activity::leftJoin('users', 'users.id', 'activity_log.causer_id')
            ->when($causer_id, function ($query) use ($causer_id) {
                $query->where('activity_log.causer_id', $causer_id);
            })
            ->selectRaw("activity_log.*,concat(users.first_name,' ',users.last_name) user");
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '';
            $action .= '<a href="' . url('activity_log/' . $data->id . '/show') . '" class=""><i class="fa fa-eye"></i></a>';
            return $action;
        })->editColumn('user', function ($data) {
            return '<a href="' . url('user/' . $data->causer_id . '/show') . '">' . $data->user . '</a>';

        })->editColumn('created_at', function ($data) {
            return $data->created_at->format('Y-m-d H:i:s');

        })->editColumn('id', function ($data) {
            return '<a href="' . url('activity_log/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->rawColumns(['id', 'action', 'user'])->make(true);
    }

    public function show($id)
    {
        $activity_log = Activity::find($id);
        return theme_view('activitylog::activity_log.show', compact('activity_log'));
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
