<?php

namespace App\Http\Controllers;

use App\Models\KpiCategory;
use App\Models\KpiDescription;
use Exception;
use Illuminate\Http\Request;

class KpiDescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kpiDescriptions = KpiDescription::all();
        return view('kpi.kpi_description.index', [
            'title' => 'KPI Desc',
            'active' => 'kpi-description',
            'kpiDescriptions' => $kpiDescriptions,
            'kpiCategories' => KpiCategory::all(),
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
                'description' => 'required',
            ]);
            KpiDescription::create($request->all());

            return redirect('kpidescription')->with(['success' => 'Data added !']);
        } catch (Exception $e) {
            return redirect('kpidescription')->with(['error' => 'Failed, try again..' . $e->getMessage()]);
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
    public function edit(KpiDescription $kpiDescription)
    {
        return view('kpi.kpi_description.edit', [
            'title' => 'KPI Description',
            'active' => 'kpi-description',
            'kpiDescription' => $kpiDescription,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KpiDescription $kpiDescription)
    {
        try {
            $kpiDescription->update($request->all());

            return redirect('kpidescription')->with('success', 'Successfully Updated !');
        } catch (Exception $e) {
            return redirect('kpidescription')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(KpiDescription $kpiDescription)
    {
        try {
            $kpiDescription->delete($kpiDescription);

            return redirect('kpidescription')->with('success', 'Successfully Deleted !');
        } catch (Exception $e) {
            return redirect('kpidescription')->with(['error' => $e->getMessage()]);
        }
    }

    public function get(Request $request)
    {
        $kpiDescs = KpiDescription::with('kpi_category')
        ->where('kpi_category_id', $request->kpi_category_id)
        ->orderBy('description')
        ->get();

        return response()->json($kpiDescs);
    }
}
