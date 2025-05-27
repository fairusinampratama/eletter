<?php

namespace App\Http\Controllers\SekretarisUmum;

use App\Http\Controllers\SekretarisUmumController;
use App\Models\Committee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class KepanitiaanController extends SekretarisUmumController
{
    public $title = 'Kepanitiaan';

    public function index()
    {
        $kepanitiaan = Committee::with(['chairman', 'secretary'])->get();
        return view('dashboard.sekretaris-umum.kepanitiaan.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'kepanitiaan' => $kepanitiaan,
        ]);
    }

    public function create()
    {
        return view('dashboard.sekretaris-umum.kepanitiaan.create', [
            'menuItems' => $this->menuItems,
            'title' => 'Tambah ' . $this->title,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9\s]+$/', 'unique:committees,name'],
                'year' => ['required', 'integer'],
                'is_active' => ['required', 'boolean'],
                'chairman_username' => ['required', 'string', 'min:8', 'max:12', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username'],
                'chairman_fullname' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z\s.,]+$/'],
                'chairman_password' => ['required', 'string', 'min:8', 'max:12'],
                'secretary_username' => ['required', 'string', 'min:8', 'max:12', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username'],
                'secretary_fullname' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z\s.,]+$/'],
                'secretary_password' => ['required', 'string', 'min:8', 'max:12'],  
            ]);

            DB::transaction(function () use ($request) {
                // Create chairman
                $chairman = User::create([
                    'username' => $request->chairman_username,
                    'fullname' => $request->chairman_fullname,
                    'password' => Hash::make($request->chairman_password),
                    'role_id' => 4, // Chairman role
                    'institution_id' => auth()->user()->institution_id,
                    'year' => $request->year,
                    'is_active' => $request->is_active,
                ]);

                // Create secretary
                $secretary = User::create([
                    'username' => $request->secretary_username,
                    'fullname' => $request->secretary_fullname,
                    'password' => Hash::make($request->secretary_password),
                    'role_id' => 5, // Secretary role
                    'institution_id' => auth()->user()->institution_id,
                    'year' => $request->year,
                    'is_active' => $request->is_active,
                ]);

                // Create committee
                Committee::create([
                    'name' => $request->name,
                    'institution_id' => auth()->user()->institution_id,
                    'chairman_id' => $chairman->id,
                    'secretary_id' => $secretary->id,
                ]);
            });

            return redirect()->route('sekretaris-umum.kepanitiaan.index')
                ->with('success', 'Kepanitiaan berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambah kepanitiaan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $committee = Committee::findOrFail($id);
        $users = User::where('institution_id', auth()->user()->institution_id)
            ->whereIn('role_id', [4, 5])
            ->get();

        return view('dashboard.sekretaris-umum.kepanitiaan.edit', [
            'menuItems' => $this->menuItems,
            'title' => 'Edit ' . $this->title,
            'committee' => $committee,
            'users' => $users,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $committee = Committee::with(['chairman', 'secretary'])->findOrFail($id);

            $request->validate([
                'name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9\s-]+$/', 'unique:committees,name,' . $committee->id],
                'chairman_year' => ['required', 'integer'],
                'chairman_is_active' => ['required', 'boolean'],    
                'chairman_username' => ['required', 'string', 'min:8', 'max:12', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username,' . $committee->chairman_id],
                'chairman_fullname' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z\s.,]+$/'],
                'chairman_password' => ['nullable', 'string', 'min:8', 'max:12'],
                'secretary_username' => ['required', 'string', 'min:8', 'max:12', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username,' . $committee->secretary_id],
                'secretary_fullname' => ['required', 'string', 'max:255'],
                'secretary_password' => ['nullable', 'string', 'min:8'],
            ]);

            DB::transaction(function () use ($request, $committee) {
                // Update chairman
                $chairman = $committee->chairman;
                $chairman->username = $request->chairman_username;
                $chairman->fullname = $request->chairman_fullname;
                $chairman->year = $request->chairman_year;
                $chairman->is_active = $request->chairman_is_active;
                if ($request->filled('chairman_password')) {
                    $chairman->password = Hash::make($request->chairman_password);
                }
                $chairman->save();

                // Update secretary with same year and is_active as chairman
                $secretary = $committee->secretary;
                $secretary->username = $request->secretary_username;
                $secretary->fullname = $request->secretary_fullname;
                $secretary->year = $request->chairman_year;
                $secretary->is_active = $request->chairman_is_active;
                if ($request->filled('secretary_password')) {
                    $secretary->password = Hash::make($request->secretary_password);
                }
                $secretary->save();

                // Update committee
                $committee->update([
                    'name' => $request->name,
                ]);
            });

            return redirect()->route('sekretaris-umum.kepanitiaan.index')
                ->with('success', 'Kepanitiaan berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kepanitiaan: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $kepanitiaan = null)
    {
        try {
            // Bulk delete
            if ($kepanitiaan === 'selected') {
                $data = json_decode($request->ids, true);
                $ids = $data['ids'] ?? [];

                if (empty($ids)) {
                    return redirect()->back()
                        ->with('error', 'Tidak ada kepanitiaan yang dipilih untuk dihapus.');
                }

                $count = Committee::whereIn('id', $ids)->count();
                if ($count !== count($ids)) {
                    return redirect()->back()
                        ->with('error', 'Beberapa kepanitiaan tidak ditemukan.');
                }

                Committee::whereIn('id', $ids)->delete();
                return redirect()->route('sekretaris-umum.kepanitiaan.index')
                    ->with('success', $count . ' kepanitiaan berhasil dihapus.');
            } else {
                // Single delete
                $committee = Committee::findOrFail($kepanitiaan);
                $committee->delete();
                return redirect()->route('sekretaris-umum.kepanitiaan.index')
                    ->with('success', 'Kepanitiaan berhasil dihapus.');
            }
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Kepanitiaan tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kepanitiaan: ' . $e->getMessage());
        }
    }
}
