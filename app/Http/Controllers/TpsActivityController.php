<?php

namespace App\Http\Controllers;

use App\Models\TpsActivity;
use Illuminate\Http\Request;

class TpsActivityController extends Controller
{
    // Ambil semua activity TPS
    public function index()
    {
        $activities = TpsActivity::with('container')->get();
        return response()->json($activities);
    }

    // Simpan activity TPS
    public function store(Request $request)
    {
        try {
            //code...
              $request->validate([
            'container_id' => 'required|exists:containers,id',
            'masuk' => 'nullable|date',
            'keluar' => 'nullable|date',
            'foto_masuk_depan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_depan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_belakang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_belakang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_kiri' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_kiri' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_kanan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_kanan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

       
    $data = $request->except(['foto_masuk_depan','foto_keluar_depan','foto_masuk_belakang','foto_keluar_belakang','foto_masuk_kiri','foto_keluar_kiri','foto_masuk_kanan','foto_keluar_kanan']);

        if ($request->hasFile('foto_masuk_depan')) {
            $path = $request->file('foto_masuk_depan')->store('tps', 'public');
            $data['foto_masuk_depan'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_keluar_depan')) {
            $path = $request->file('foto_keluar_depan')->store('tps', 'public');
            $data['foto_keluar_depan'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_masuk_belakang')) {
            $path = $request->file('foto_masuk_belakang')->store('tps', 'public');
            $data['foto_masuk_belakang'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_keluar_belakang')) {
            $path = $request->file('foto_keluar_belakang')->store('tps', 'public');
            $data['foto_keluar_belakang'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_masuk_kiri')) {
            $path = $request->file('foto_masuk_kiri')->store('tps', 'public');
            $data['foto_masuk_kiri'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_keluar_kiri')) {
            $path = $request->file('foto_keluar_kiri')->store('tps', 'public');
            $data['foto_keluar_kiri'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_masuk_kanan')) {
            $path = $request->file('foto_masuk_kanan')->store('tps         ', 'public');
            $data['foto_masuk_kanan'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_keluar_kanan')) {
            $path = $request->file('foto_keluar_kanan')->store('tps', 'public');
            $data['foto_keluar_kanan'] = url('storage/' . $path);
        }       

        $activity = TpsActivity::create($data);
        return response()->json($activity, 201);
        } catch (\Throwable $th) {
            //throw $th;
             return response()->json([
                        'success' => false,
                        'message' => $th->getMessage(),
                    ], 400);
        }
      
    }

    // Detail activity
    public function show($id)
    {
        $activity = TpsActivity::with('container')->findOrFail($id);
        return response()->json($activity);
    }

    // Update activity TPS
    public function update(Request $request, $id)
    {
        try {
            $activity = TpsActivity::findOrFail($id);

            // validasi fleksibel (semua field boleh kosong)
            $request->validate([
                'masuk' => 'date',
                'keluar' => 'date',
                'foto_masuk_depan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'foto_keluar_depan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'foto_masuk_belakang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'foto_keluar_belakang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'foto_masuk_kiri' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'foto_keluar_kiri' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'foto_masuk_kanan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'foto_keluar_kanan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // ambil semua data kecuali file
            $data = $request->except([
                'foto_masuk_depan','foto_keluar_depan',
                'foto_masuk_belakang','foto_keluar_belakang',
                'foto_masuk_kiri','foto_keluar_kiri',
                'foto_masuk_kanan','foto_keluar_kanan'
            ]);

            // upload file kalau ada
            foreach ([
                'foto_masuk_depan','foto_keluar_depan',
                'foto_masuk_belakang','foto_keluar_belakang',
                'foto_masuk_kiri','foto_keluar_kiri',
                'foto_masuk_kanan','foto_keluar_kanan'
            ] as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('tps', 'public');
                    $data[$field] = url('storage/' . $path);
                }
            }

            $activity->update($data);
            dd($activity);

            return response()->json($activity);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }


    // Hapus activity TPS
    public function destroy($id)
    {
        $activity = TpsActivity::findOrFail($id);
        $activity->delete();

        return response()->json(['message' => 'TPS Activity deleted successfully']);
    }
}
