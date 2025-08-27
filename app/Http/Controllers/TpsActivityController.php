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
        $request->validate([
            'container_id' => 'required|exists:containers,id',
            'masuk' => 'nullable|date',
            'keluar' => 'nullable|date',
            'foto_masuk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto_masuk','foto_keluar']);

        if ($request->hasFile('foto_masuk')) {
            $path = $request->file('foto_masuk')->store('tps', 'public');
            $data['foto_masuk'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_keluar')) {
            $path = $request->file('foto_keluar')->store('tps', 'public');
            $data['foto_keluar'] = url('storage/' . $path);
        }

        $activity = TpsActivity::create($data);
        return response()->json($activity, 201);
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
        $activity = TpsActivity::findOrFail($id);

        $request->validate([
            'masuk' => 'nullable|date',
            'keluar' => 'nullable|date',
            'foto_masuk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto_masuk','foto_keluar']);

        if ($request->hasFile('foto_masuk')) {
            $path = $request->file('foto_masuk')->store('tps', 'public');
            $data['foto_masuk'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_keluar')) {
            $path = $request->file('foto_keluar')->store('tps', 'public');
            $data['foto_keluar'] = url('storage/' . $path);
        }

        $activity->update($data);
        return response()->json($activity);
    }

    // Hapus activity TPS
    public function destroy($id)
    {
        $activity = TpsActivity::findOrFail($id);
        $activity->delete();

        return response()->json(['message' => 'TPS Activity deleted successfully']);
    }
}
