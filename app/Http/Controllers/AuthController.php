<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'no_telepon' => 'required|string|max:20',
                'username' => 'required|string|unique:users,username',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                ],
            ], [
                'password.min' => 'Password minimal 8 karakter',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol',
            ]);

            $passwordHash = Hash::make($request->password);

            $user = User::create([
                'name' => $request->nama_lengkap,
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'no_telepon' => $request->no_telepon,
                'username' => $request->username,
                'password' => $passwordHash,
                'role' => 'anggota',
            ]);

            $nomorAnggota = 'AGT' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

            $anggota = Anggota::create([
                'user_id' => $user->id,
                'nomor_anggota' => $nomorAnggota,
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'no_telepon' => $request->no_telepon,
                'username' => $request->username,
                'password' => $passwordHash,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Akun anggota berhasil dibuat',
                'data' => [
                    'id' => $user->id,
                    'nomor_anggota' => $nomorAnggota,
                    'nama_lengkap' => $user->nama_lengkap,
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'alamat' => $user->alamat,
                    'email' => $user->email,
                    'no_telepon' => $user->no_telepon,
                    'username' => $user->username,
                    'role' => $user->role,
                    'foto' => $user->foto,
                ],
                'anggota' => $anggota,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required|string',
                'password' => 'required|string',
                'role' => 'required|in:admin,anggota',
            ]);

            $user = User::where('email', $request->login)
                ->orWhere('username', $request->login)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Username atau email tidak ditemukan',
                ], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password salah',
                ], 401);
            }

            if ($user->role !== $request->role) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role login tidak sesuai',
                ], 403);
            }

            $nomorAnggota = null;

            if ($user->role === 'anggota') {
                $anggota = Anggota::where('user_id', $user->id)->first();
                $nomorAnggota = $anggota?->nomor_anggota;
            }

            return response()->json([
                'status' => true,
                'message' => 'Login berhasil sebagai ' . $user->role,
                'data' => [
                    'id' => $user->id,
                    'nomor_anggota' => $nomorAnggota,
                    'nama_lengkap' => $user->nama_lengkap,
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'email' => $user->email,
                    'username' => $user->username,
                    'alamat' => $user->alamat,
                    'no_telepon' => $user->no_telepon,
                    'foto' => $user->foto,
                    'jabatan' => $user->role === 'admin' ? 'Pustakawan' : null,
                    'role' => $user->role,
                    'redirect' => $user->role === 'admin'
                        ? '/profileadmin'
                        : '/dashboardanggota',
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfilAdmin(Request $request, int $id)
    {
        try {
            $user = User::where('id', $id)
                ->where('role', 'admin')
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Admin tidak ditemukan',
                ], 404);
            }

            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            ]);

            $fotoName = $user->foto;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');

                if (!$file->isValid()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Foto gagal diupload.',
                    ], 422);
                }

                $folderPath = public_path('uploads/profile');

                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }

                if ($fotoName && file_exists($folderPath . '/' . $fotoName)) {
                    unlink($folderPath . '/' . $fotoName);
                }

                $extension = $file->getClientOriginalExtension();
                $fotoName = time() . '_' . uniqid() . '.' . $extension;

                $file->move($folderPath, $fotoName);
            }

            $user->name = $request->nama_lengkap;
            $user->nama_lengkap = $request->nama_lengkap;
            $user->foto = $fotoName;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profil admin berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email' => $user->email,
                    'username' => $user->username,
                    'foto' => $user->foto,
                    'jabatan' => 'Pustakawan',
                    'role' => $user->role,
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfilAnggota(Request $request, int $id)
    {
        try {
            $user = User::where('id', $id)
                ->where('role', 'anggota')
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anggota tidak ditemukan',
                ], 404);
            }

            $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'username' => 'required|string|unique:users,username,' . $id,
                'alamat' => 'required|string',
                'no_telepon' => 'required|string|max:20',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
            ]);

            $anggota = Anggota::where('user_id', $user->id)->first();

            $fotoName = $user->foto;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');

                if (!$file->isValid()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Foto gagal diupload.',
                    ], 422);
                }

                $folderPath = public_path('uploads/profile');

                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }

                if ($fotoName && file_exists($folderPath . '/' . $fotoName)) {
                    unlink($folderPath . '/' . $fotoName);
                }

                $extension = $file->getClientOriginalExtension();
                $fotoName = time() . '_' . uniqid() . '.' . $extension;

                $file->move($folderPath, $fotoName);
            }

            $user->update([
                'name' => $request->nama_lengkap,
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir ?: $user->tanggal_lahir,
                'username' => $request->username,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
                'foto' => $fotoName,
            ]);

            if ($anggota) {
                $anggota->update([
                    'nama_lengkap' => $request->nama_lengkap,
                    'tanggal_lahir' => $request->tanggal_lahir ?: $anggota->tanggal_lahir,
                    'username' => $request->username,
                    'alamat' => $request->alamat,
                    'no_telepon' => $request->no_telepon,
                    'foto' => $fotoName,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Profil anggota berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'nomor_anggota' => $anggota?->nomor_anggota,
                    'nama_lengkap' => $user->nama_lengkap,
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'email' => $user->email,
                    'username' => $user->username,
                    'alamat' => $user->alamat,
                    'no_telepon' => $user->no_telepon,
                    'foto' => $fotoName,
                    'role' => $user->role,
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil',
            'redirect' => '/register',
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required|string',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                ],
            ], [
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol',
            ]);

            $user = User::where('email', $request->login)
                ->orWhere('username', $request->login)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan',
                ], 404);
            }

            $passwordHash = Hash::make($request->password);

            $user->update([
                'password' => $passwordHash,
            ]);

            if ($user->role === 'anggota') {
                Anggota::where('user_id', $user->id)->update([
                    'password' => $passwordHash,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Password berhasil diubah',
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}