<?php

namespace App\Http\Controllers;

use App\Exports\TemplateExport;
use App\Models\Area;
use App\Models\Divisi;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\Position;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.index')
            ->with(
                [
                    'users' => User::with(['area', 'role', 'divisi', 'approval', 'position'])->orderBy('nama_lengkap')->withTrashed()->filter()->simplePaginate(100),
                    'title' => 'User',
                    'active' => 'user',
                    'approvals' => User::whereIn('role_id', [3, 4, 5, 6])->get(),
                ]
            );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = Area::all();
        $roles = Role::all()->except(1);
        $divisis = Divisi::all();
        return view('user.create')->with([
            'roles' => $roles,
            'divisis' => $divisis,
            'areas' => $areas,
            'title' => 'Create User',
            'active' => 'user',
            'positions' => Position::orderBy('name')->get(),
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
            $request->validate([
                'username' => 'required||min:3',
                'password' => 'required||min:3',
                'nama_lengkap' => 'required||min:3',
            ]);
            $existing = User::where('username', $request->username)->get();
            if (count($existing)) {
                return redirect('/user')->with(['error' => "Gagal menambahkan user baru, username sudah di pakai"]);
            }
            User::create([
                'username' => strtolower(preg_replace('/\s+/', '', $request->username)),
                'password' => bcrypt($request->password),
                'nama_lengkap' => strtoupper($request->nama_lengkap),
                'role_id' => (int) $request->role_id,
                'area_id' => (int) $request->area_id,
                'divisi_id' => (int) $request->divisi_id,
                'dr' => $request->dr,
                'wn' => $request->wn,
                'wr' => $request->wr,
                'mn' => $request->mn,
                'mr' => $request->mn,
                'approval_id' => $request->approval_id,
                'position_id' => $request->position_id,
            ]);

            return redirect('/user')->with(['success' => "Berhasil menambahkan user baru."]);
        } catch (Exception $e) {
            return redirect('/user')->with(['error' => "Gagal menambahkan user baru," . $e->getMessage()]);
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
    public function edit($id)
    {
        $user = User::withTrashed()->find($id);
        return view('user.edit')->with([
            'user' => $user,
            'title' => 'User',
            'active' => 'user',
            'divisis' => Divisi::orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
            'approvals' => User::whereIn('role_id', [3, 4, 5, 6])->withTrashed()->get(),
            'positions' => Position::orderBy('name')->get(),
        ]);
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
        try {
            $token = PersonalAccessToken::where('tokenable_id', $id)->get()->first;
            if ($token) {
                $token->delete();
            }
            $user = User::withTrashed()->find($id);

            $request->validate([
                'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            ]);

            $data = $request->all();
            $data['password'] = $user->password;
            if ($request->password) {
                $data['password'] = bcrypt($request->password);
            }

            $data['nama_lengkap'] = strtoupper($request->nama_lengkap);
            $data['area_id'] = Divisi::with('area')->find($request->divisi_id)->area->id;
            $user->update($data);
            return redirect('user')->with(['success' => 'berhasil edit user']);
        } catch (Exception $e) {
            return redirect('user')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $token = PersonalAccessToken::where('tokenable_id', $id)->get()->first;
        if ($token) {
            $token->delete();
        }
        $user->delete();
        return redirect('/user')->with(['success' => "Berhasil menghapus user " . $user->nama_lengkap]);
    }

    public function active(Request $request, $id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->deleted_at = null;
        $user->save();
        return redirect('/user')->with(['success' => "Berhasil mengatifkan kembali user " . $user->nama_lengkap]);
    }

    public function getdivisi(Request $request, $id)
    {
        $divisi = Divisi::where('area_id', $id)->orderBy('name')->get();
        return response()->json($divisi);
    }

    public function getapproval(Request $request)
    {
        switch ($request->divisiid) {
            //TK ANAN
            // case 15:
            //     $approval = User::where('id', 2)->get();
            //     break;

            //CMULIA
            // case 14:
            //     $approval = User::where('id', 2)->get();

            //     break;
            default:
                $approval = User::where('role_id', 6)->where('area_id', $request->areaid)
                    ->orWhereIn('role_id', [3, 4, 5])->where('divisi_id', $request->divisiid)->orderBy('nama_lengkap')->get();
                break;
        }

        return response()->json($approval);
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'user.xlsx');
    }

    public function template()
    {
        return Excel::download(new TemplateExport, 'user_template.xlsx');
    }

    public function import(Request $request)
    {
        try {
            $file = $request->file('file');
            $namaFile = $file->getClientOriginalName();
            $file->move(public_path('import'), $namaFile);

            Excel::import(new UsersImport, public_path('/import/' . $namaFile));
            return redirect('user')->with(['success' => 'berhasil import user']);
        } catch (Exception $e) {
            return redirect('user')->with(['error' => $e->getMessage()]);
        }
    }

    public function getuserresult(Request $request)
    {
        switch ($request->result) {
            case 1:
                $user = User::where('wr', 1)
                ->where('divisi_id', $request->divisi_id)
                ->get();
                break;

            default:
                $user = User::where('wn', 1)
                ->where('divisi_id', $request->divisi_id)
                ->get();
                break;
        }

        return response()->json($user);
    }
}
