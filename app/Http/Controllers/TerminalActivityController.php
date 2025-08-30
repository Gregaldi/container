<?php

namespace App\Http\Controllers;

use App\Models\TerminalActivity;
use Illuminate\Http\Request;

class TerminalActivityController extends Controller
{
    // Ambil semua activity Terminal
    public function index()
    {
        $activities = TerminalActivity::with('container')->get();
        return response()->json($activities);
    }

    // Simpan activity Terminal
    public function store(Request $request)
    {
          try {
            //code...
        $request->validate([
            'container_id' => 'required|exists:containers,id',
            'masuk' => 'required|date',
            'keluar' => 'nullable|date',
            'foto_masuk_depan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_depan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_belakang' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_belakang' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_kiri' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_kiri' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_kanan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_kanan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

    $data = $request->except(['foto_masuk_depan','foto_keluar_depan','foto_masuk_belakang','foto_keluar_belakang','foto_masuk_kiri','foto_keluar_kiri','foto_masuk_kanan','foto_keluar_kanan']);

        if ($request->hasFile('foto_masuk_depan')) {
            $path = $request->file('foto_masuk_depan')->store('terminal', 'public');
            $data['foto_masuk_depan'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_keluar_depan')) {
            $path = $request->file('foto_keluar_depan')->store('terminal', 'public');
            $data['foto_keluar_depan'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_masuk_belakang')) {
            $path = $request->file('foto_masuk_belakang')->store('terminal', 'public');
            $data['foto_masuk_belakang'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_keluar_belakang')) {
            $path = $request->file('foto_keluar_belakang')->store('terminal', 'public');
            $data['foto_keluar_belakang'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_masuk_kiri')) {
            $path = $request->file('foto_masuk_kiri')->store('terminal', 'public');
            $data['foto_masuk_kiri'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_keluar_kiri')) {
            $path = $request->file('foto_keluar_kiri')->store('terminal', 'public');
            $data['foto_keluar_kiri'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_masuk_kanan')) {
            $path = $request->file('foto_masuk_kanan')->store('terminal         ', 'public');
            $data['foto_masuk_kanan'] = url('storage/' . $path);
        }
        if ($request->hasFile('foto_keluar_kanan')) {
            $path = $request->file('foto_keluar_kanan')->store('terminal', 'public');
            $data['foto_keluar_kanan'] = url('storage/' . $path);
        }       
        $activity = TerminalActivity::create($data);
        return response()->json($activity, 201);
        } catch (\Throwable $th) {
            return response()->json([
                        'success' => false,
                        'message' => $th->getMessage(),
                    ], 400);

        }
       
    }

    // Detail activity Terminal
    public function show($id)
    {
        $activity = TerminalActivity::with('container')->findOrFail($id);
        return response()->json($activity);
    }

    // Update activity Terminal
  public function update(Request $request, $id)
{
    try {
        $activity = TerminalActivity::findOrFail($id);

        $request->validate([
            'masuk' => 'nullable|date',
            'keluar' => 'required|date',
            'foto_masuk_depan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_depan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_belakang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_belakang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_kiri' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_kiri' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_masuk_kanan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_kanan' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        dd($request);

        // Ambil semua data kecuali file foto
        $data = $request->except([
            'masuk','keluar',
            'foto_masuk_depan',
            'foto_keluar_depan',
            'foto_masuk_belakang',
            'foto_keluar_belakang',
            'foto_masuk_kiri',
            'foto_keluar_kiri',
            'foto_masuk_kanan',
            'foto_keluar_kanan'
        ]);


        // List field foto untuk looping
        $fotoFields = [
            'foto_masuk_depan',
            'foto_keluar_depan',
            'foto_masuk_belakang',
            'foto_keluar_belakang',
            'foto_masuk_kiri',
            'foto_keluar_kiri',
            'foto_masuk_kanan',
            'foto_keluar_kanan',
        ];

        foreach ($fotoFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('terminal', 'public');
                $data[$field] = url('storage/' . $path);
            }
        }

        // $activity->update($data);
         $activity->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Terminal activity berhasil diperbarui',
            'data' => $activity
        ]);

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => $th->getMessage(),
        ], 400);
    }
}


    // Hapus activity Terminal
    public function destroy($id)
    {
        $activity = TerminalActivity::findOrFail($id);
        $activity->delete();

        return response()->json(['message' => 'Terminal Activity deleted successfully']);
    }
}
