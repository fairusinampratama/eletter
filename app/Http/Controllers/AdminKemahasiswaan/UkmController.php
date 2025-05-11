<?php

namespace App\Http\Controllers\AdminKemahasiswaan;

use App\Http\Controllers\AdminKemahasiswaanController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UkmController extends AdminKemahasiswaanController
{
    public $title = 'UKM';

    public function index(Request $request)
    {
        $institutions = Institution::all();
        $institution = null;

        return view('dashboard.admin-kemahasiswaan.ukm.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'institutions' => $institutions,
            'institution' => $institution,
        ]);
    }

    public function edit($id)
    {
        $institution = Institution::findOrFail($id);
        $institutions = Institution::all();

        return view('dashboard.admin-kemahasiswaan.ukm.index', [
            'menuItems' => $this->menuItems,
            'title' => $this->title,
            'institutions' => $institutions,
            'institution' => $institution,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:institutions'],
            ]);

            Institution::create([
                'name' => $request->name,
            ]);

            return redirect()->route('admin-kemahasiswaan.ukm.index')
                ->with('success', 'Organization added successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validation failed: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add organization: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $institution = Institution::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:institutions,name,' . $id,
            ]);

            $institution->name = $validated['name'];
            $institution->save();

            return redirect()->route('admin-kemahasiswaan.ukm.index')
                ->with('success', 'Organization updated successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Organization not found.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('error', 'Validation failed: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update organization: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $ukm = null)
    {
        try {
            if ($ukm === 'selected') {
                $data = json_decode($request->ids, true);
                $ids = $data['ids'] ?? [];

                if (empty($ids)) {
                    return redirect()->back()
                        ->with('error', 'No organizations selected for deletion.');
                }

                $count = Institution::whereIn('id', $ids)->count();
                if ($count !== count($ids)) {
                    return redirect()->back()
                        ->with('error', 'Some organizations not found.');
                }

                Institution::whereIn('id', $ids)->delete();
                return redirect()->route('admin-kemahasiswaan.ukm.index')
                    ->with('success', $count . ' organizations deleted successfully.');
            } else {
                $institution = Institution::findOrFail($ukm);
                $institution->delete();
                return redirect()->route('admin-kemahasiswaan.ukm.index')
                    ->with('success', 'Organization deleted successfully.');
            }
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Organization not found.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete organization: ' . $e->getMessage());
        }
    }
}
