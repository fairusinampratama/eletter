<?php

namespace App\Http\Controllers\AdminKemahasiswaan;

use App\Http\Controllers\AdminKemahasiswaanController;
use App\Models\User;
use App\Models\Role;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenggunaController extends AdminKemahasiswaanController
{
    public $title = 'User';

    public function index(Request $request)
    {
        $roles = Role::whereNotIn('id', [4, 5])->get(); // Exclude Ketua Panitia and Sekretaris Panitia
        $institutions = Institution::all();

        return view('dashboard.admin-kemahasiswaan.pengguna.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'roles' => $roles,
            'institutions' => $institutions,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'fullname' => ['required', 'string', 'max:255'],
                'password' => ['required', Rules\Password::defaults()],
                'role_id' => ['required', 'exists:roles,id'],
                'institution_id' => ['required', 'exists:institutions,id'],
                'year' => ['required', 'integer'],
                'is_active' => ['required', 'boolean'],
            ]);

            // Check for existing active user with same role and institution
            if ($request->is_active && in_array($request->role_id, [2, 3, 6])) {
                $existingActive = User::where('institution_id', $request->institution_id)
                    ->where('role_id', $request->role_id)
                    ->where('is_active', true)
                    ->exists();

                if ($existingActive) {
                    $roleName = match($request->role_id) {
                        "2" => 'Ketua Umum',
                        "3" => 'Sekretaris Umum',
                        "6" => 'Pembina',
                        default => 'Unknown'
                    };
                    throw ValidationException::withMessages([
                        'role_id' => "Sudah ada $roleName aktif untuk institusi ini"
                    ]);
                }
            }

            User::create([
                'username' => $request->username,
                'fullname' => $request->fullname,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'institution_id' => $request->institution_id,
                'year' => $request->year,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('admin-kemahasiswaan.pengguna.index')
                ->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan pengguna: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'username' => 'required|string|max:255|unique:users,username,' . $id,
                'fullname' => 'required|string|max:255',
                'role_id' => 'required|exists:roles,id',
                'institution_id' => 'required|exists:institutions,id',
                'year' => 'required|integer',
                'is_active' => 'required|boolean',
                'password' => 'nullable|string|min:8',
            ]);

            // Check for existing active user with same role and institution
            if ($request->is_active && in_array($request->role_id, [2, 3, 6])) {
                $existingActive = User::where('institution_id', $request->institution_id)
                    ->where('role_id', $request->role_id)
                    ->where('is_active', true)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($existingActive) {
                    $roleName = match($request->role_id) {
                        "2" => 'Ketua Umum',
                        "3" => 'Sekretaris Umum',
                        "6" => 'Pembina',
                        default => 'Unknown'
                    };
                    throw ValidationException::withMessages([
                        'role_id' => "Sudah ada $roleName aktif untuk institusi ini"
                    ]);
                }
            }

            $user->username = $validated['username'];
            $user->fullname = $validated['fullname'];
            $user->role_id = $validated['role_id'];
            $user->institution_id = $validated['institution_id'];
            $user->year = $validated['year'];
            $user->is_active = $validated['is_active'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return redirect()->route('admin-kemahasiswaan.pengguna.index')
                ->with('success', 'Pengguna berhasil diperbarui.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Pengguna tidak ditemukan.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pengguna: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $pengguna = null)
    {
        try {
            if ($pengguna === 'selected') {
                $data = json_decode($request->ids, true);
                $ids = $data['ids'] ?? [];

                if (empty($ids)) {
                    return redirect()->back()
                        ->with('error', 'Tidak ada pengguna yang dipilih untuk dihapus.');
                }

                $count = User::whereIn('id', $ids)->count();
                if ($count !== count($ids)) {
                    return redirect()->back()
                        ->with('error', 'Beberapa pengguna tidak ditemukan.');
                }

                User::whereIn('id', $ids)->delete();
                return redirect()->route('admin-kemahasiswaan.pengguna.index')
                    ->with('success', $count . ' pengguna berhasil dihapus.');
            } else {
                $user = User::findOrFail($pengguna);
                $user->delete();
                return redirect()->route('admin-kemahasiswaan.pengguna.index')
                    ->with('success', 'Pengguna berhasil dihapus.');
            }
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Pengguna tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }
}
