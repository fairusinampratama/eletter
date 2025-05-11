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
        $roles = Role::all();
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
            ]);

            User::create([
                'username' => $request->username,
                'fullname' => $request->fullname,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'institution_id' => $request->institution_id,
            ]);

            return redirect()->route('admin-kemahasiswaan.pengguna.index')
                ->with('success', 'User added successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validation failed: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add user: ' . $e->getMessage());
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
                'password' => 'nullable|string|min:8',
            ]);

            $user->username = $validated['username'];
            $user->fullname = $validated['fullname'];
            $user->role_id = $validated['role_id'];
            $user->institution_id = $validated['institution_id'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return redirect()->route('admin-kemahasiswaan.pengguna.index')
                ->with('success', 'User updated successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'User not found.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validation failed: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
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
                        ->with('error', 'No users selected for deletion.');
                }

                $count = User::whereIn('id', $ids)->count();
                if ($count !== count($ids)) {
                    return redirect()->back()
                        ->with('error', 'Some users not found.');
                }

                User::whereIn('id', $ids)->delete();
                return redirect()->route('admin-kemahasiswaan.pengguna.index')
                    ->with('success', $count . ' users deleted successfully.');
            } else {
                $user = User::findOrFail($pengguna);
                $user->delete();
                return redirect()->route('admin-kemahasiswaan.pengguna.index')
                    ->with('success', 'User deleted successfully.');
            }
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'User not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
