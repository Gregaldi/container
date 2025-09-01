<?php

namespace App\Http\Controllers;

use App\Models\Container;
use Illuminate\Http\Request;

class ContainerController extends Controller
{
    // Ambil semua container
    public function index()
    {
        $containers = Container::with(['terminalActivities', 'tpsActivities'])->get();
        return response()->json($containers);
    }

    // Simpan data container baru
   public function store(Request $request)
{
    try {
        $request->validate([
        'nomor_container'      => 'required|string|unique:containers',
        'size'                 => 'required|string',
        'asal'                 => 'required|string',
        'no_plat'              => 'required|string|unique:containers,no_plat',
        'no_seal'              => 'required|string',
        'foto_no_plat'         => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'foto_no_seal'         => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'foto_nomor_container' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ambil semua data kecuali file foto
        $data = $request->except(['foto_no_plat','foto_no_seal','foto_nomor_container']);

        // buat folder khusus per container biar rapi
        $folder = 'containers/'.$request->nomor_container;

        

        if ($request->hasFile('foto_no_plat')) {
            $path = $request->file('foto_no_plat')->store($folder, 'public');
            $data['foto_no_plat'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_no_seal')) {
            $path = $request->file('foto_no_seal')->store($folder, 'public');
            $data['foto_no_seal'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_nomor_container')) {
            $path = $request->file('foto_nomor_container')->store($folder, 'public');
            $data['foto_nomor_container'] = url('storage/' . $path);
        }

        $container = Container::create($data);

        return response()->json([
            'success'   => true,
            'message'   => 'Data container berhasil disimpan',
            'data'      => $container
        ], 201);

    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => $th->getMessage(),
        ], 400);
    }
}

    // Detail container
    public function show($id)
    {
        $container = Container::with(['terminalActivities', 'tpsActivities'])->findOrFail($id);
        return response()->json($container);
    }

    // Update container
    public function update(Request $request, $no_plat)
    {
        try {
            // Cari container berdasarkan no_plat
            $container = Container::where('no_plat', $no_plat)->firstOrFail();

            $request->validate([
                // 'nomor_container' => 'sometimes|string|unique:containers,nomor_container,' . $container->id,
                'size' => 'sometimes|string',
                'asal' => 'sometimes|string',
                'no_plat' => 'sometimes|string|unique:containers,no_plat,' . $container->id,
                'no_seal' => 'sometimes|string',
            ]);

            $container->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Container berhasil diupdate',
                'data'    => $container,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }


    // Hapus container
    public function destroy($no_plat)
    {
        try {
            $container = Container::where('no_plat', $no_plat)->firstOrFail();
            $container->delete();

            return response()->json([
                'success' => true,
                'message' => 'Container deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
    }

}
