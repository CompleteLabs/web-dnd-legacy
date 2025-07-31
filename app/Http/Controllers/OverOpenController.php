<?php

namespace App\Http\Controllers;

use App\Exports\cutpointExport;
use App\Models\Overopen;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;


class OverOpenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $overopens = Overopen::with(['user', 'leader', 'user.area', 'user.divisi'])->simplePaginate(100);

        return view('admin.overopen.index')->with([
            'title' => 'Over Open',
            'active' => 'overopen',
            'overopens' => $overopens,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.overopen.create')->with([
            'title' => 'Over Open',
            'active' => 'overopen',
            'users' => User::orderBy('nama_lengkap')->get()->except(1),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            $data = $request->all();
            unset($data['_token']);
            $data['daily'] = Carbon::parse(strtotime($request->date))->timestamp;
            unset($data['date']);
            $data['atasan'] = $user->approval->id;
            Overopen::create($data);
            return redirect('/admin/overopen')->with(['success' => 'Berhasil menambahkan cut point']);
        } catch (Exception $e) {
            return redirect('/admin/overopen')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            return Excel::download(new cutpointExport($request->date), 'cutpoint_' . date('M_Y',strtotime($request->date)) . '.xlsx',);
        } catch (Exception $e) {
            return redirect('/admin/overopen')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $cutpoint = Overopen::find($request->id);
            $cutpoint->delete();
            return redirect('/admin/overopen')->with(['success' => 'Berhasil menghapus cut point']);
        } catch (Exception $e) {
            return redirect('/admin/overopen')->with(['error' => $e->getMessage()]);
        }
    }
}
