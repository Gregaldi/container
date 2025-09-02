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
                'container_no_plat' => 'required|exists:containers,no_plat',
                'masuk' => 'required|date',
                'keluar' => 'nullable|date',
                'foto_masuk_depan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                // 'foto_keluar_depan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'foto_masuk_belakang' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                // 'foto_keluar_belakang' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'foto_masuk_kiri' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                // 'foto_keluar_kiri' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'foto_masuk_kanan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                // 'foto_keluar_kanan' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);
             // ✅ Cek apakah plat ini sudah ada di activity yang belum keluar
        $alreadyIn = TerminalActivity::where('container_no_plat', $request->container_no_plat)
            ->whereNull('keluar') // artinya masih ada di dalam
            ->exists();

        if ($alreadyIn) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan dengan plat ' . $request->container_no_plat . ' sudah masuk dan belum keluar.',
            ], 422);
        }

    $data = $request->except(['foto_masuk_depan','foto_masuk_belakang','foto_masuk_kiri','foto_masuk_kanan']);

        if ($request->hasFile('foto_masuk_depan')) {
            $path = $request->file('foto_masuk_depan')->store('terminal', 'public');
            $data['foto_masuk_depan'] = url('storage/' . $path);
        }


        if ($request->hasFile('foto_masuk_belakang')) {
            $path = $request->file('foto_masuk_belakang')->store('terminal', 'public');
            $data['foto_masuk_belakang'] = url('storage/' . $path);
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
            $path = $request->file('foto_masuk_kanan')->store('terminal', 'public');
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
  public function updateByPlat(Request $request, $no_plat)
{
    try {
        $activity = TerminalActivity::where('container_no_plat', $no_plat)->firstOrFail();

        // Validasi
        $request->validate([
            'masuk'               => 'nullable|date',
            'keluar'              => 'required|date',
            'foto_keluar_depan'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_belakang'=> 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_kiri'    => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar_kanan'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
           // ✅ Cek apakah plat ini sudah pernah masuk DAN keluar
        $alreadyRecorded = TerminalActivity::where('container_no_plat', $request->container_no_plat)
            ->whereNotNull('masuk')
            ->whereNotNull('keluar')
            ->exists();

        if ($alreadyRecorded) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan dengan plat ' . $request->container_no_plat . ' sudah tercatat masuk & keluar, tidak bisa disimpan lagi.',
            ], 422);
        }

        $data = $request->only(['masuk', 'keluar']);

        // Upload semua foto keluar
        foreach (['foto_keluar_depan','foto_keluar_belakang','foto_keluar_kiri','foto_keluar_kanan'] as $field) {
            if ($request->hasFile($field)) {
                // hapus lama jika ada
                if ($activity->$field && \Storage::exists('public/'.$activity->$field)) {
                    \Storage::delete('public/'.$activity->$field);
                }
                // simpan baru
                  if ($request->hasFile($field)) {
                        $path = $request->file($field)->store('terminal', 'public');
                        $data[$field] = url('storage/' . $path); // simpan URL penuh
                    }
            }
        }

        $activity->update($data);

        // response dengan full url
        // $activity->foto_keluar_depan    = $activity->foto_keluar_depan ? url('storage/'.$activity->foto_keluar_depan) : null;
        // $activity->foto_keluar_belakang = $activity->foto_keluar_belakang ? url('storage/'.$activity->foto_keluar_belakang) : null;
        // $activity->foto_keluar_kiri     = $activity->foto_keluar_kiri ? url('storage/'.$activity->foto_keluar_kiri) : null;
        // $activity->foto_keluar_kanan    = $activity->foto_keluar_kanan ? url('storage/'.$activity->foto_keluar_kanan) : null;

        return response()->json([
            'success' => true,
            'message' => 'Terminal activity berhasil diperbarui berdasarkan plat nomor',
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
