<?php

namespace App\Http\Controllers;

use App\Models\Container;
use Illuminate\Http\Request;

class ContainerController extends Controller
{
    /**
     * GET /api/containers
     * Menampilkan semua container
     */
    public function index()
    {
        $containers = Container::with('movements')->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Container list retrieved successfully',
            'data' => $containers,
        ]);
    }

    /**
     * POST /api/containers
     * Menyimpan data container baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'container_number' => 'required|string|max:255|unique:containers,container_number',
            'size' => 'nullable|string|max:50',
        ]);

        $container = Container::create([
            'container_number' => $validated['container_number'],
            'size' => $validated['size'] ?? null,
            'status' => 'OUT', // default status awal
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Container created successfully',
            'data' => $container,
        ], 201);
    }

    /**
     * GET /api/containers/{container_number}
     * Menampilkan detail container berdasarkan nomor
     */
    public function show($container_number)
    {
        $container = Container::where('container_number', $container_number)
            ->with('movements')
            ->first();

        if (!$container) {
            return response()->json([
                'status' => 'error',
                'message' => 'Container not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $container,
        ]);
    }
}
