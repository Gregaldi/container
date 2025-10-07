<?php

namespace App\Http\Controllers;

use App\Models\Container;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ContainerController extends Controller
{
    /**
     * GET /api/containers
     * Menampilkan semua container
     */
    public function index()
    {
        try {
            $containers = Container::with('movements')->latest()->get();

            // Transform photos ke URL publik
            $containers->transform(function ($container) {
                $container->movements->transform(function ($movement) {
                    if (is_array($movement->photos)) {
                        $movement->photos = array_map(fn($path) => url($path), $movement->photos);
                    }
                    return $movement;
                });
                return $container;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Container list retrieved successfully',
                'data' => $containers,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve containers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/containers
     * Menyimpan data container baru
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'container_number' => 'required|string|max:255|unique:containers,container_number',
                'size' => 'required|string|max:50',
                'asal' => 'required|string|max:100',
            ]);

            $container = Container::create([
                'container_number' => $validated['container_number'],
                'size' => $validated['size'] ?? null,
                'asal' => $validated['asal'] ?? null,
                'status' => 'notfound', // default status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Container created successfully',
                'data' => $container,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create container',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/containers/{container_number}
     * Menampilkan detail container berdasarkan nomor
     */
    public function show($container_number)
    {
        try {
            $container = Container::where('container_number', $container_number)
                ->with('movements')
                ->firstOrFail();

            // Transform photos ke URL publik
            $container->movements->transform(function ($movement) {
                if (is_array($movement->photos)) {
                    $movement->photos = array_map(fn($path) => url($path), $movement->photos);
                }
                return $movement;
            });

            return response()->json([
                'status' => 'success',
                'data' => $container,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Container not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve container',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
