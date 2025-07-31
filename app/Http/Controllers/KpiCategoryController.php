<?php

namespace App\Http\Controllers;

use App\Models\KpiCategory;
use Exception;
use Illuminate\Http\Request;

class KpiCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kpiCategories = KpiCategory::all();
        return view('kpi.kpi_category.index', [
            'title' => 'KPI Category',
            'active' => 'kpi-category',
            'kpiCategories' => $kpiCategories,
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
            KpiCategory::create([
                'name' => strtoupper($request->name),
            ]);

            return redirect('/kpicategory')->with(['success' => 'Data added !']);
        } catch (Exception $e) {
            return redirect('/kpicategory')->with(['error' => 'Failed, try again..' . $e->getMessage()]);
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
    public function edit(KpiCategory $kpiCategory)
    {
        return view('kpi.kpi_category.edit', [
            'title' => 'KPI Category',
            'active' => 'kpi-category',
            'kpiCategory' => $kpiCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KpiCategory $kpiCategory)
    {
        try {
            $kpiCategory->update([
                'name' => strtoupper($request->name),
            ]);

            return redirect('kpicategory')->with('success', 'Successfully Updated !');
        } catch (Exception $e) {
            return redirect('kpicategory')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(KpiCategory $kpiCategory)
    {
        try {
            $kpiCategory->delete($kpiCategory);

            return redirect('kpicategory')->with('success', 'Successfully Deleted !');
        } catch (Exception $e) {
            return redirect('kpicategory')->with(['error' => $e->getMessage()]);
        }
    }
}
