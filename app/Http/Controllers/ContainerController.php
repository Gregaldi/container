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
        $request->validate([
            'nomor_container' => 'required|string|unique:containers',
            'size' => 'required|string',
            'asal' => 'required|string',
            'no_plat' => 'required|string',
            'no_seal' => 'required|string',
        ]);
        dd($request);

        $container = Container::create($request->all());
        return response()->json($container, 201);
    }

    // Detail container
    public function show($id)
    {
        $container = Container::with(['terminalActivities', 'tpsActivities'])->findOrFail($id);
        return response()->json($container);
    }

    // Update container
    public function update(Request $request, $id)
    {
        $container = Container::findOrFail($id);

        $request->validate([
            'nomor_container' => 'sometimes|string|unique:containers,nomor_container,' . $id,
            'size' => 'sometimes|string',
            'asal' => 'sometimes|string',
            'no_plat' => 'sometimes|string',
            'no_seal' => 'sometimes|string',
        ]);

        $container->update($request->all());
        return response()->json($container);
    }

    // Hapus container
    public function destroy($id)
    {
        $container = Container::findOrFail($id);
        $container->delete();

        return response()->json(['message' => 'Container deleted successfully']);
    }
}
