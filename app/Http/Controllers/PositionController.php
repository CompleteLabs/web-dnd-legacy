<?php

namespace App\Http\Controllers;

use App\Exports\PositionExport;
use App\Exports\TemplatePosition;
use App\Imports\PositionImport;
use App\Models\Position;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('kpi.position.index', [
            'title' => 'Position',
            'active' => 'position',
            'positions' => Position::orderBy('name')->get(),
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
            Position::create([
                'name' => strtoupper($request->name),
            ]);

            return redirect('position')->with(['success' => 'Data added !']);
        } catch (Exception $e) {
            return redirect('position')->with(['error' => 'Failed, try again..' . $e->getMessage()]);
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
    public function edit(Position $position)
    {
        return view('kpi.position.edit', [
            'title' => 'Job Position',
            'active' => 'job-position',
            'position' => $position,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        try {
            $position->update([
                'name' => strtoupper($request->name),
            ]);

            return redirect('position')->with('success', 'Successfully Updated !');
        } catch (Exception $e) {
            return redirect('position')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $position)
    {
        try {
            $position->delete($position);

            return redirect('position')->with('success', 'Successfully Deleted !');
        } catch (Exception $e) {
            return redirect('position')->with(['error' => $e->getMessage()]);
        }
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move(public_path('import'), $namaFile);

        try {
            Excel::import(new PositionImport(), public_path('/import/' . $namaFile));
        } catch (Exception $e) {
            return redirect('position')->with(['error' => $e->getMessage()]);
        }
        return redirect('position')->with(['success' => 'Successfully Uploaded !']);
    }

    public function template(){
        
        return Excel::download(new TemplatePosition(), 'Job_Position_Template.xlsx');
    }
}
