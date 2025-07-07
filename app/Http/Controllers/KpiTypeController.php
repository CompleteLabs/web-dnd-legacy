<?php

namespace App\Http\Controllers;

use App\Models\KpiType;
use Exception;
use Illuminate\Http\Request;

class KpiTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kpiTypes = KpiType::all();
        return view('kpi.kpi_type.index', [
            'title' => 'KPI Type',
            'active' => 'kpi-type',
            'kpiTypes' => $kpiTypes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $request->validate([
                'name' => 'required',
            ]);
            KpiType::create([
                'name' => strtoupper($request->name),
            ]);

            return redirect('kpitype')->with(['success' => 'Data added !']);
        } catch (Exception $e) {
            return redirect('kpitype')->with(['error' => 'Failed, try again..' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(KpiType $kpiType)
    {
        return view('kpi.kpi_type.edit', [
            'title' => 'KPI Type',
            'active' => 'kpi-type',
            'kpiType' => $kpiType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KpiType $kpiType)
    {
        try {
            $kpiType->update([
                'name' => strtoupper($request->name),
            ]);

            return redirect('kpitype')->with('success', 'Successfully Updated !');
        } catch (Exception $e) {
            return redirect('kpitype')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(KpiType $kpiType)
    {
        try {
            $kpiType->delete($kpiType);

            return redirect('kpitype')->with('success', 'Successfully Deleted !');
        } catch (Exception $e) {
            return redirect('kpitype')->with(['error' => $e->getMessage()]);
        }
    }
}
