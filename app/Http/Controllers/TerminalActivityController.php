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
        $request->validate([
            'container_id' => 'required|exists:containers,id',
            'masuk' => 'nullable|date',
            'keluar' => 'nullable|date',
            'foto_masuk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto_masuk','foto_keluar']);

        // Upload foto masuk
        if ($request->hasFile('foto_masuk')) {
            $path = $request->file('foto_masuk')->store('terminal', 'public');
            $data['foto_masuk'] = url('storage/' . $path);
        }

        // Upload foto keluar
        if ($request->hasFile('foto_keluar')) {
            $path = $request->file('foto_keluar')->store('terminal', 'public');
            $data['foto_keluar'] = url('storage/' . $path);
        }

        $activity = TerminalActivity::create($data);
        return response()->json($activity, 201);
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
        $activity = TerminalActivity::findOrFail($id);

        $request->validate([
            'masuk' => 'nullable|date',
            'keluar' => 'nullable|date',
            'foto_masuk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'foto_keluar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['foto_masuk','foto_keluar']);

        if ($request->hasFile('foto_masuk')) {
            $path = $request->file('foto_masuk')->store('terminal', 'public');
            $data['foto_masuk'] = url('storage/' . $path);
        }

        if ($request->hasFile('foto_keluar')) {
            $path = $request->file('foto_keluar')->store('terminal', 'public');
            $data['foto_keluar'] = url('storage/' . $path);
        }

        $activity->update($data);
        return response()->json($activity);
    }

    // Hapus activity Terminal
    public function destroy($id)
    {
        $activity = TerminalActivity::findOrFail($id);
        $activity->delete();

        return response()->json(['message' => 'Terminal Activity deleted successfully']);
    }
}
