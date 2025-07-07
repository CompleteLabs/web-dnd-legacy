<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\PasswordValidationRules;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function fetch()
    {
        try {
            $user = User::with('area', 'divisi', 'role')->where('id', Auth::user()->id)->first();
            return ResponseFormatter::success($user, 'Selamat datang kembali ' . Auth::user()->nama_lengkap);
        } catch (Exception $e) {
            return ResponseFormatter::error(null, 'Gagal login');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        /**
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         * @throws \Exception
         */
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $credentials = request(['username', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Gagal login, cek kembali username dan password anda', 500);
            }

            $user = User::where('username', $request->username)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new Exception('Invalid Credentials');
            }
            $user->id_notif = $request->id_notif;
            $user->update();

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Selamat datang kembali ' . Auth::user()->nama_lengkap);
        } catch (Exception $error) {
            return ResponseFormatter::error(null, $error->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success(null, 'Sampai jumpa kembali ' . Auth::user()->nama_lengkap);
    }

    public function tag(Request $request)
    {
        try {
            $user = User::with('area', 'role', 'divisi')->where('role_id', '<=', Auth::user()->role_id)->orderBy('nama_lengkap')->get()->except([Auth::id(), 1]);
            return ResponseFormatter::success($user, 'berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function profilepicture(Request $request)
    {
        try {
            $user = User::findOrFail(Auth::id());
            if ($user->profile_picture) {
                $file_path = storage_path('app/public/' . $user->profile_picture);
                unlink($file_path);
            }
            ##buat nama gambar
            $imageName = Auth::user()->username . date('ymdHis') . '.' . $request->image->extension();

            $request->image->move(storage_path('app/public/'), $imageName);
            $user->profile_picture = $imageName;
            $user->save();
            return ResponseFormatter::success(null, 'Berhasil mengganti gambar');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }

    public function team(Request $request)
    {
        try {
            switch (Auth::id()) {
                case 2:
                    $user = User::with('area', 'role', 'divisi')->where('area_id', Auth::user()->area_id)->orderBy('nama_lengkap')->get()->except([Auth::id()]);
                    break;
                case 1320:
                    $user = User::with('area', 'role', 'divisi')->whereIn('divisi_id', [Auth::user()->divisi_id, 13])->orderBy('nama_lengkap')->get()->except([Auth::id()]);
                    break;
                case 1480:
                    $user = User::with('area', 'role', 'divisi')->whereIn('divisi_id', [Auth::user()->divisi_id, 13])->orderBy('nama_lengkap')->get()->except([Auth::id()]);
                    break;
                case 1341:
                    $user = User::with('area', 'role', 'divisi')->where('divisi_id', 14)->orWhere('divisi_id', 15)->orderBy('nama_lengkap')->get();
                    break;
                case 3:
                    $user = User::with('area', 'role', 'divisi')->whereIn('divisi_id', [auth()->user()->divisi_id, 10])->orderBy('nama_lengkap')->get();
                    break;
                default:
                    $user = User::with('area', 'role', 'divisi')->where('divisi_id', Auth::user()->divisi_id)->orderBy('nama_lengkap')->get()->except([Auth::id(), 1]);
                    break;
            }

            return ResponseFormatter::success($user, 'berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error(null, $e->getMessage());
        }
    }
}
