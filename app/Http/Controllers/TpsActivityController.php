<?php

namespace App\Http\Controllers;

use App\Models\TpsActivity;
use Illuminate\Http\Request;

class TpsActivityController extends Controller
{
    // Ambil semua activity TPS
    public function index()
    {
        $activities = TpsActivity::with('containers')->get();
        return response()->json($activities);
    }

    // Simpan activity TPS
    public function store(Request $request)
    {
        try {
            //code...
              $request->validate([
            // 'container_id' => 'required|exists:containers,id',
            'container_no' => [
                'required',
                'exists:containers,nomor_container',
                'unique:tps_activities,container_no' // <== tambahkan ini
            ],
            'masuk' => 'required|date',
            'keluar' => 'nullable|date',
            'foto_masuk_depan' => 'required|image|mimes:jpg,jpeg,png|max:2048',

            'foto_masuk_belakang' => 'required|image|mimes:jpg,jpeg,png|max:2048',

            'foto_masuk_kiri' => 'required|image|mimes:jpg,jpeg,png|max:2048',

            'foto_masuk_kanan' => 'required|image|mimes:jpg,jpeg,png|max:2048',

        ]);
         $alreadyIn = TpsActivity::where('container_no', $request->container_no_plat)
            ->whereNull('keluar') // artinya masih ada di dalam
            ->exists();

        if ($alreadyIn) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan dengan plat ' . $request->container_no_plat . ' sudah masuk dan belum keluar.',
            ], 422);
        }

       
    $data = $request->except(['foto_masuk_depan','foto_masuk_belakang','foto_masuk_kiri','foto_masuk_kanan',]);

        if ($request->hasFile('foto_masuk_depan')) {
            $path = $request->file('foto_masuk_depan')->store('tps', 'public');
            $data['foto_masuk_depan'] = url('storage/' . $path);
        }


        if ($request->hasFile('foto_masuk_belakang')) {
            $path = $request->file('foto_masuk_belakang')->store('tps', 'public');
            $data['foto_masuk_belakang'] = url('storage/' . $path);
        }
     
        if ($request->hasFile('foto_masuk_kiri')) {
            $path = $request->file('foto_masuk_kiri')->store('tps', 'public');
            $data['foto_masuk_kiri'] = url('storage/' . $path);
        }
      
        if ($request->hasFile('foto_masuk_kanan')) {
            $path = $request->file('foto_masuk_kanan')->store('tps', 'public');
            $data['foto_masuk_kanan'] = url('storage/' . $path);
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
        $activity = TpsActivity::with('containers')->findOrFail($id);
        return response()->json($activity);
    }

    // Update activity TPS
   
public function update(Request $request, $nomor_container)
{
    try {
        // $activity = TpsActivity::findOrFail($id);
  // $activity = TerminalActivity::findOrFail($id);
         $activity = TpsActivity::where('container_no', $nomor_container)->firstOrFail();
        // Validasi
        $request->validate([
            'masuk' => 'required|date',
            'keluar' => 'nullable|date',
        ]);

        // Ambil hanya field jam masuk / keluar
        $data = $request->only(['masuk', 'keluar']);

        // Update hanya field yang dikirim
        $activity->update($data);

        return response()->json([
            'success' => true,
            'message' => 'TPS activity berhasil diperbarui',
            'data' => $activity
        ]);

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => $th->getMessage(),
        ], 400);
    }
}

  public function updateByPlat(Request $request, $nomor_container)
{
    try {
        $activity = TpsActivity::where('container_no', $nomor_container)->firstOrFail();

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
        $alreadyRecorded = TpsActivity::where('container_no', $nomor_container)
            ->whereNotNull('masuk')
            ->whereNotNull('keluar')
            ->exists();

        if ($alreadyRecorded) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan dengan nomor container ' . $nomor_container . ' sudah tercatat masuk & keluar, tidak bisa disimpan lagi.',
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
                $path = $request->file($field)->store('tps', 'public');
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
            'message' => 'TPS activity berhasil diperbarui berdasarkan plat nomor',
            'data' => $activity
        ]);

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => $th->getMessage(),
        ], 400);
    }
}


    // Hapus activity TPS
  
    public function destroy($nomor_container)
{
    $activity = TpsActivity::where('container_no', $nomor_container)->firstOrFail();
    $activity->delete();

    return response()->json([
        'message' => 'TPS Activity dengan container ' . $nomor_container . ' berhasil dihapus'
    ]);
}
}
