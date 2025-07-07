<?php

namespace App\Http\Controllers;

use App\Exports\KpiMonthlyExport;
use App\Exports\KpiPerDivisionExport;
use App\Imports\KpiImport;
use App\Models\Divisi;
use App\Models\Kpi;
use App\Models\KpiCategory;
use App\Models\KpiDescription;
use App\Models\KpiDetail;
use App\Models\KpiType;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KpiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->role_id != 1) {
            $kpis = Kpi::orderBy('date', 'DESC')
                ->whereHas('user', function ($q) {
                    $q->where('divisi_id', auth()->user()->divisi_id);
                })
                ->simplePaginate(100);

            $positions = Position::whereHas('user', function ($q) {
                $q->where('divisi_id', auth()->user()->divisi_id);
            })
                ->get();
        } else {
            $kpis = Kpi::orderBy('date', 'DESC')
                ->simplePaginate(100);

                $positions = Position::orderBy('name')->get();
        }

        if ($request->position_id) {
            $kpis = Kpi::whereHas('user', function ($q) use ($request) {
                $q->where('position_id', $request->position_id);
            })
                ->orderBy('date', 'DESC')
                ->get();
        }

        // BU VEGA
        if (auth()->user()->role_id == 5 && auth()->user()->divisi_id == 3) {
            $positionsToCopy = Position::whereHas('user', function ($q) {
                $q->whereIn('role_id', [4, 5, 3, 2])
                    ->where('divisi_id', auth()->user()->divisi_id);
            })
                ->get();
            // KALAU ROLE MANAGER BUAT KPI UNTUK ROLE COORDINATOR & MANAGER
        } else if (auth()->user()->role_id == 5 && auth()->user()->divisi_id != 3) {
            $positionsToCopy = Position::whereHas('user', function ($q) {
                $q->whereIn('role_id', [4, 5])
                    ->where('divisi_id', auth()->user()->divisi_id);
            })
                ->get();
            // KALAU ROLE COORDINATOR BUAT KPI UNTUK ROLE COORDINATOR & TEAM LEADER & STAFF
        } else if (auth()->user()->role_id == 4) {
            $positionsToCopy = Position::whereHas('user', function ($q) {
                $q->whereIn('role_id', [4, 3, 2])
                    ->where('divisi_id', auth()->user()->divisi_id);
            })
                ->get();
        } else {
            $positionsToCopy = Position::with('user')->get();
        }

        return view('kpi.kpi.index', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpis' => $kpis,
            'positions' => $positions,
            'kpicategories' => KpiCategory::all(),
            'kpitypes' => KpiType::all(),
            'divisis' => Divisi::all(),
            'positionsToCopy' => $positionsToCopy,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // VALIDASI WAKTU
        if (auth()->user()->role_id != 1) {
            // $today = Carbon::createFromFormat('d/m/Y', '01/01/2023');
            $today = Carbon::now();

            // if ($today > $today->copy()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfMonth()->addDay(6)) {
            //     return redirect('kpi')->with(['error' => 'Can not add KPI, now already more than 5 days since start of month !']);
            // }
        }

        // BU VEGA
        if (auth()->user()->role_id == 5 && auth()->user()->divisi_id == 3) {
            $positions = Position::whereHas('user', function ($q) {
                $q->whereIn('role_id', [4, 5, 3, 2])
                    ->where('divisi_id', auth()->user()->divisi_id);
            })
                ->get();
            // KALAU ROLE MANAGER BUAT KPI UNTUK ROLE COORDINATOR & MANAGER
        } else if (auth()->user()->role_id == 5 && auth()->user()->divisi_id != 3) {
            $positions = Position::whereHas('user', function ($q) {
                $q->whereIn('role_id', [4, 5])
                    ->where('divisi_id', auth()->user()->divisi_id);
            })
                ->get();
            // KALAU ROLE COORDINATOR BUAT KPI UNTUK ROLE COORDINATOR & TEAM LEADER & STAFF
        } else if (auth()->user()->role_id == 4) {
            $positions = Position::whereHas('user', function ($q) {
                $q->whereIn('role_id', [4, 3, 2])
                    ->where('divisi_id', auth()->user()->divisi_id);
            })
                ->get();
        } else {
            $positions = Position::with('user')->get();
        }

        $kpidescs = KpiDescription::all();

        return view('kpi.kpi.create', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpicategories' => KpiCategory::all(),
            'kpitypes' => KpiType::all(),
            'kpidescs' => $kpidescs,
            'positions' => $positions,
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
            // dd($request->all());
            if ($request->percentageMain + $request->percentageAdm + $request->percentageRep != 100) {
                return redirect()->back()->with(['error' => 'Total percentage must be 100 !']);
            }

            $kpiDescriptionsAdm = [];
            $kpiDescriptionsRep = [];
            $kpiDescriptionsMain = [];

            // KPI ADMINISTRATION
            for ($i = 0; $i < count($request->get('kpis')); $i++) {
                $temp = [
                    'kpi_category_id' => $request->kpi_category_id,
                    'description' => $request->get('kpis')[$i],
                ];
                $kpiDescriptionAdm = KpiDescription::create($temp);
                $kpiDescriptionsAdm[$i] = $kpiDescriptionAdm;
            }

            // KPI REPORTING
            for ($i = 0; $i < count($request->get('kpisRep')); $i++) {
                $temp = [
                    'kpi_category_id' => $request->kpi_category_id,
                    'description' => $request->get('kpisRep')[$i],
                ];
                $kpiDescriptionRep = KpiDescription::create($temp);
                $kpiDescriptionsRep[$i] = $kpiDescriptionRep;
            }

            // KPI MAIN JOB
            for ($i = 0; $i < count($request->get('kpisMain')); $i++) {
                $temp = [
                    'kpi_category_id' => $request->kpi_category_id,
                    'description' => $request->get('kpisMain')[$i],
                ];
                $kpiDescriptionMain = KpiDescription::create($temp);
                $kpiDescriptionsMain[$i] = $kpiDescriptionMain;
            }

            $users = User::where('position_id', $request->position_id)->get();

            foreach ($users as $user) {
                // KPI ADMINISTRATION
                $kpiAdm = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => 1,
                    'date' => Carbon::createFromFormat('m/Y', $request->date)->format('Y-m-d'),
                    'percentage' => $request->percentageAdm,
                ]);

                for ($i = 0; $i < count($request->get('kpis')); $i++) {
                    $temp = [
                        'kpi_id' => $kpiAdm->id,
                        'kpi_description_id' => $kpiDescriptionsAdm[$i]->id,
                        'count_type' => $request->get('count_type')[$i],
                        'value_plan' => $request->get('value_plan')[$i] ?? null,
                        'value_result' => 0,
                        'start' => $request->get('start')[$i] == null ? null : Carbon::createFromFormat('d/m/Y', $request->get('start')[$i])->format('Y-m-d'),
                        'end' => $request->get('end')[$i] == null ? null : Carbon::createFromFormat('d/m/Y', $request->get('end')[$i])->format('Y-m-d'),
                    ];

                    KpiDetail::create($temp);
                }

                // KPI REPORTING
                $kpiAdm = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => 2,
                    'date' => Carbon::createFromFormat('m/Y', $request->date)->format('Y-m-d'),
                    'percentage' => $request->percentageRep,
                ]);

                for ($i = 0; $i < count($request->get('kpisRep')); $i++) {
                    $temp = [
                        'kpi_id' => $kpiAdm->id,
                        'kpi_description_id' => $kpiDescriptionsRep[$i]->id,
                        'count_type' => $request->get('count_typeRep')[$i],
                        'value_plan' => $request->get('value_planRep')[$i] ?? null,
                        'value_result' => 0,
                        'start' => $request->get('startRep')[$i] == null ? null : Carbon::createFromFormat('d/m/Y', $request->get('startRep')[$i])->format('Y-m-d'),
                        'end' => $request->get('endRep')[$i] == null ? null : Carbon::createFromFormat('d/m/Y', $request->get('endRep')[$i])->format('Y-m-d'),
                    ];

                    KpiDetail::create($temp);
                }

                // KPI MAIN JOB
                $kpiMain = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => 3,
                    'date' => Carbon::createFromFormat('m/Y', $request->date)->format('Y-m-d'),
                    'percentage' => $request->percentageMain,
                ]);

                for ($i = 0; $i < count($request->get('kpisMain')); $i++) {
                    $temp = [
                        'kpi_id' => $kpiMain->id,
                        'kpi_description_id' => $kpiDescriptionsMain[$i]->id,
                        'count_type' => $request->get('count_typeMain')[$i],
                        'value_plan' => $request->get('value_planMain')[$i] ?? null,
                        'value_result' => 0,
                        'start' => $request->get('startMain')[$i] == null ? null : Carbon::createFromFormat('d/m/Y', $request->get('startMain')[$i])->format('Y-m-d'),
                        'end' => $request->get('endMain')[$i] == null ? null : Carbon::createFromFormat('d/m/Y', $request->get('endMain')[$i])->format('Y-m-d'),
                    ];

                    KpiDetail::create($temp);
                }
            }

            return redirect('kpi')->with('success', 'Data Added !');
        } catch (Exception $e) {
            return redirect('kpi')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Kpi $kpi)
    {
        return view('kpi.kpi.show', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpi' => $kpi,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Kpi $kpi)
    {
        // KALAU COORDINATOR GABISA CHANGE KPI PUNYA SENDIRI KECUALI SCM-MAJU (Alicia)
        // if (auth()->user()->role_id == 4 && auth()->user()->divisi_id != 28) {
        //     if ($kpi->user_id == auth()->user()->id) {
        //         return redirect()->back()->with(['error' => 'Request your supervisor to change KPI !']);
        //     }
        // }
        $kpidescs = KpiDescription::all();

        return view('kpi.kpi.edit', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpi' => $kpi,
            'kpicategories' => KpiCategory::all(),
            'kpitypes' => KpiType::all(),
            'kpidescs' => $kpidescs,
            'positions' => Position::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kpi $kpi)
    {
        try {
            $kpi->kpi_type_id = $request->kpi_type_id;
            $kpi->kpi_category_id = $request->kpi_category_id;
            $kpi->date = Carbon::createFromFormat('m/Y', $request->date)->format('Y-m-d');
            $kpi->percentage = $request->percentage;
            $kpi->save();

            $kpi = Kpi::with('kpi_detail')->find($kpi->id);

            // Get the IDs of KpiDescriptions associated with the Kpi before the update
            $existingKpiDescriptionsBeforeUpdate = $kpi->kpi_detail ? $kpi->kpi_detail->pluck('kpi_description_id')->toArray() : [];

            // Loop through the new request data
            for ($i = 0; $i < count($request->get('kpis')); $i++) {
                $kpiDescription = $request->get('kpis')[$i];
                $kpiDescriptionId = isset($request->get('kpi_description_id')[$i]) ? $request->get('kpi_description_id')[$i] : null;

                $kpiDetailData = [
                    'kpi_id' => $kpi->id,
                    'count_type' => $request->get('count_type')[$i],
                    'value_plan' => $request->get('value_plan')[$i] ?? null,
                    'start' => $request->get('start')[$i] == null ? null : Carbon::createFromFormat('d/m/Y', $request->get('start')[$i])->format('Y-m-d'),
                    'end' => $request->get('end')[$i] == null ? null : Carbon::createFromFormat('d/m/Y', $request->get('end')[$i])->format('Y-m-d'),
                ];

                // Check if $kpiDescriptionId exists in the list of existing KpiDescriptions before the update
                if (in_array($kpiDescriptionId, $existingKpiDescriptionsBeforeUpdate)) {
                    KpiDetail::where('kpi_description_id', $kpiDescriptionId)
                        ->where('kpi_id', $kpi->id)
                        ->update($kpiDetailData);

                    KpiDescription::where('id', $kpiDescriptionId)
                        ->update(['description' => $kpiDescription]);
                } else {
                    // Find or create the KpiDescription
                    $newKpiDescription = KpiDescription::create(
                        [
                            'description' => $kpiDescription,
                            'kpi_category_id' => $request->kpi_category_id
                        ]
                    );

                    // KpiDescription doesn't exist, create a new KpiDetail record
                    $newKpiDetail = new KpiDetail([
                        'kpi_id' => $kpi->id,
                        'kpi_description_id' => $newKpiDescription->id,
                        'count_type' => $kpiDetailData['count_type'],
                        'value_plan' => $kpiDetailData['value_plan'] ?? null,
                        'start' => $kpiDetailData['start'],
                        'end' => $kpiDetailData['end'],
                    ]);

                    // Associate the new KpiDetail record with the existing Kpi
                    $kpi->kpi_detail()->save($newKpiDetail);

                    // Associate the new KpiDetail record with the existing KpiDescription
                    // $newKpiDetail->kpi_description()->save($newKpiDescription);
                }
            }

            // Get the current IDs of KpiDescriptions associated with the Kpi after the update
            $existingKpiDescriptionsAfterUpdate = $request->input('kpi_description_id', []);

            // Find the IDs of KpiDescriptions that were associated with the Kpi before but not after the update
            $removedKpiDescriptions = array_diff($existingKpiDescriptionsBeforeUpdate, $existingKpiDescriptionsAfterUpdate);

            // Delete the KpiDetail records with the obtained IDs
            if (!empty($removedKpiDescriptions)) {
                KpiDetail::whereIn('kpi_description_id', $removedKpiDescriptions)
                    ->where('kpi_id', $kpi->id)
                    ->delete();
            }

            return redirect('kpi/' . $kpi->id . '/show')->with('success', 'Data Updated !');
        } catch (Exception $e) {
            return redirect('kpi/' . $kpi->id . '/show')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kpi $kpi)
    {
        try {
            $kpi->delete($kpi);

            return redirect('kpi')->with('success', 'Successfully Deleted !');
        } catch (Exception $e) {
            return redirect('kpi')->with(['error' => $e->getMessage()]);
        }
    }

    public function getKpiDetail($kpiId)
    {
        $kpi = Kpi::with('kpi_detail', 'kpi_detail.kpi_description')->find($kpiId);

        return response()->json($kpi->kpi_detail);
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move(public_path('import'), $namaFile);

        try {
            $users = User::where('position_id', $request->position_id)->get();
            $kpiIds = [];

            foreach ($users as $user) {
                $kpi = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => $request->kpi_category_id,
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'percentage' => $request->percentage,
                ]);

                $kpiIds[] = $kpi->id;
            }

            Excel::import(new KpiImport($kpiIds), public_path('/import/' . $namaFile));
        } catch (Exception $e) {
            return redirect('kpi')->with(['error' => $e->getMessage()]);
        }
        return redirect('kpi')->with(['success' => 'Successfully Uploaded !']);
    }

    public function exportMonthly(Request $request)
    {
        $divisi = Divisi::where('id', $request->divisi_id)->first();

        return Excel::download(new KpiMonthlyExport($request->date, $request->divisi_id ?? auth()->user()->divisi_id), 'KPI_' . (auth()->user()->role_id == 1 ? $divisi->name : auth()->user()->divisi->name) . '_' . $request->date . '.xlsx');
    }

    public function exportPerDivision(Request $request)
    {
        $divisi = Divisi::where('id', $request->divisi_id)->first();

        return Excel::download(new KpiPerDivisionExport($request->month, $request->divisi_id ?? auth()->user()->divisi_id), 'KPI_' . (auth()->user()->role_id == 1 ? $divisi->name : auth()->user()->divisi->name) . '_' . $request->month . '.xlsx');
    }

    public function copyKpi(Request $request)
    {
        $fromDate = Carbon::createFromFormat('m/Y', $request->fromDate)->startOfMonth();
        $toDate = Carbon::createFromFormat('m/Y', $request->toDate)->startOfMonth();
        $users = User::where('position_id', $request->copy_position_id)->pluck('id');

        $kpis = Kpi::whereMonth('date', $fromDate->month)
            ->whereYear('date', $fromDate->year)
            ->whereIn('user_id', $users)
            ->get();

        foreach ($kpis as $kpi) {
            $newKpi = $kpi->replicate();

            $newKpi->date = $toDate;

            $newKpi->save();

            foreach ($kpi->kpi_detail as $kpiDetail) {
                $newKpiDetail = $kpiDetail->replicate();
                $newKpiDetail->kpi_id = $newKpi->id;
                $newKpiDetail->start = null;
                $newKpiDetail->end = null;
                $newKpiDetail->save();
            }
        }

        return redirect('kpi')->with(['success' => 'Copying success !']);
    }

    public function storeExtraTask(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:kpi_details,id',
            'description' => 'required|string|max:255',
            'count_type' => 'required|in:NON,RESULT',
            'value_actual' => 'nullable|numeric|min:0',
        ]);

        try {
            // Ambil detail parent KPI
            $parentKpiDetail = KpiDetail::findOrFail($request->parent_id);

            // Buat deskripsi baru untuk ekstra task
            $kpiDescription = KpiDescription::create([
                'description' => $request->description,
                'kpi_category_id' => $parentKpiDetail->kpi->kpi_category_id,
            ]);

            // Buat detail KPI untuk ekstra task
            $extraTask = KpiDetail::create([
                'parent_id' => $parentKpiDetail->id,
                'is_extra_task' => 1,
                'value_actual' => $request->count_type === 'RESULT' ? $request->value_actual : null,
                'count_type' => $request->count_type,
                'kpi_description_id' => $kpiDescription->id,
                'kpi_id' => $parentKpiDetail->kpi_id,
            ]);

            // Update nilai Value_Result pada parent
            if ($request->count_type === 'NON') {
                // Jika NON, langsung set Value_Result parent menjadi 1
                $parentKpiDetail->update([
                    'value_result' => 1,
                ]);
            } elseif ($request->count_type === 'RESULT') {
                // Jika RESULT, hitung Value_Result baru berdasarkan nilai actual
                $parentPlanValue = $parentKpiDetail->value_plan ?? 1; // Hindari pembagian dengan 0
                $newActualValue = $parentKpiDetail->value_actual + $request->value_actual;

                $newValueResult = min($newActualValue / $parentPlanValue, 1); // Maksimum 1
                $parentKpiDetail->update([
                    'value_actual' => $newActualValue,
                    'value_result' => $newValueResult,
                ]);
            }

            // Flash message ke session
            session()->flash('success', 'Ekstra task berhasil ditambahkan dan nilai parent diperbarui.');

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Flash error ke session
            session()->flash('error', 'Terjadi kesalahan saat menyimpan ekstra task.');

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyExtraTask($id)
    {
        try {
            // Cari data extra task berdasarkan ID
            $extraTask = KpiDetail::findOrFail($id);

            // Pastikan data yang dihapus adalah ekstra task
            if ($extraTask->is_extra_task == 1) {
                // Ambil parent dari extra task
                $parentKpiDetail = KpiDetail::findOrFail($extraTask->parent_id);

                // Kurangi nilai actual parent jika tipe extra task adalah RESULT
                if ($extraTask->count_type === 'RESULT') {
                    $parentActualValue = $parentKpiDetail->value_actual - ($extraTask->value_actual ?? 0);
                    $parentActualValue = max($parentActualValue, 0); // Pastikan tidak negatif
                } else {
                    $parentActualValue = $parentKpiDetail->value_actual; // Tidak ada perubahan untuk NON
                }

                // Hitung ulang value_result untuk parent
                $parentPlanValue = $parentKpiDetail->value_plan ?? 1; // Hindari pembagian dengan 0
                $newValueResult = min($parentActualValue / $parentPlanValue, 1); // Maksimum 1

                // Update parent KPI detail
                $parentKpiDetail->update([
                    'value_actual' => $parentActualValue,
                    'value_result' => $newValueResult,
                ]);

                // Hapus kpi_description terkait, jika ada
                if ($extraTask->kpi_description_id) {
                    $extraTask->kpi_description->delete(); // Hapus kpi_description
                }

                // Hapus data extra task
                $extraTask->delete();

                session()->flash('success', 'Ekstra task berhasil dihapus dan nilai parent diperbarui.');

                return response()->json(['success' => true, 'message' => 'Ekstra task dan deskripsi berhasil dihapus.']);
            }

            return response()->json(['success' => false, 'message' => 'Data bukan ekstra task.'], 400);
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menghapus ekstra task.');
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus.'], 500);
        }
    }

}