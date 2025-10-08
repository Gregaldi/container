<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\ContainerMovements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ContainerMovements::with('container');

            if ($request->filled('container_id')) {
                $query->where('container_id', $request->container_id);
            }

            if ($request->filled('direction')) {
                $query->where('direction', strtoupper($request->direction));
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('timestamp', [$request->start_date, $request->end_date]);
            }

            $movements = $query->orderBy('timestamp', 'desc')->get();

            // Transform photos ke URL publik
            $movements->transform(function ($movement) {
                if (is_array($movement->photos)) {
                    $movement->photos = array_map(fn($path) => url($path), $movement->photos);
                }
                return $movement;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Container movement history retrieved successfully',
                'data' => $movements,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve container movements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeIn(Request $request)
    {
        try {
            $request->validate([
                'container_number' => 'required|string',
                'truck_plate'      => 'required|string',
                'seal_ship'        => 'required|string',
                'front'            => 'required|image',
                'left'             => 'required|image',
                'right'            => 'required|image',
                'rear'             => 'required|image',
            ]);

            return DB::transaction(function () use ($request) {
                // 🔍 Cari container, kalau tidak ada buat baru
                $container = Container::firstOrCreate(
                    ['container_number' => $request->container_number],
                    ['status' => 'out']
                );

                // ❌ Jika sudah 'in', tolak
                if ($container->status === 'in') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Container sudah berada di TPS, tidak bisa masuk lagi',
                    ], 422);
                }

                // 📁 Buat folder dan simpan foto
                $ts = now()->format('YmdHis');
                $basePath = "uploads/containers/{$container->container_number}/in/{$ts}";
                $publicPath = public_path($basePath);
                if (!file_exists($publicPath)) mkdir($publicPath, 0775, true);

                $photos = [];
                foreach (['front', 'left', 'right', 'rear'] as $key) {
                    $file = $request->file($key);
                    $fileName = $key . '.' . $file->getClientOriginalExtension();
                    $file->move($publicPath, $fileName);
                    $photos[$key] = $basePath . '/' . $fileName;
                }

                // 🧾 Catat pergerakan 'in'
                ContainerMovements::create([
                    'container_id' => $container->id,
                    'direction'    => 'in',
                    'truck_plate'  => $request->truck_plate,
                    'seal_ship'    => $request->seal_ship,
                    'seal_tps'     => $request->seal_tps,
                    'photos'       => $photos,
                    'notes'        => $request->notes,
                    'timestamp'    => now(),
                ]);

                // 🚦 Update status container menjadi in
                $container->update(['status' => 'in']);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Container masuk TPS berhasil',
                ], 201);
            });
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencatat container masuk',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeOut(Request $request)
    {
        try {
            $request->validate([
                'container_number' => 'required|string',
                'truck_plate_out'  => 'required|string',
                'seal_ship'        => 'required|string',
                'seal_tps'         => 'required|string',
                'front'            => 'required|image',
                'left'             => 'required|image',
                'right'            => 'required|image',
                'rear'             => 'required|image',
            ]);

            return DB::transaction(function () use ($request) {
                // 🔍 Pastikan container ada
                $container = Container::where('container_number', $request->container_number)->first();

                if (!$container) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Container tidak ditemukan',
                    ], 404);
                }

                // ❌ Pastikan status terakhir adalah 'in'
                if ($container->status !== 'in') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Container tidak berada di TPS',
                    ], 422);
                }

                // 📁 Simpan foto
                $ts = now()->format('YmdHis');
                $basePath = "uploads/containers/{$container->container_number}/out/{$ts}";
                $publicPath = public_path($basePath);
                if (!file_exists($publicPath)) mkdir($publicPath, 0775, true);

                $photos = [];
                foreach (['front', 'left', 'right', 'rear'] as $key) {
                    $file = $request->file($key);
                    $fileName = $key . '.' . $file->getClientOriginalExtension();
                    $file->move($publicPath, $fileName);
                    $photos[$key] = $basePath . '/' . $fileName;
                }

                // 🧾 Catat pergerakan keluar
                ContainerMovements::create([
                    'container_id'     => $container->id,
                    'direction'        => 'out',
                    'truck_plate_out'  => $request->truck_plate_out,
                    'seal_ship'        => $request->seal_ship,
                    'seal_tps'         => $request->seal_tps,
                    'photos'           => $photos,
                    'notes'            => $request->notes,
                    'timestamp'        => now(),
                ]);

                // 🚦 Update status menjadi 'out'
                $container->update(['status' => 'out']);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Container keluar TPS berhasil',
                ], 201);
            });
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Container tidak ditemukan',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencatat container keluar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function detailindex($container_number)
    {
        try {
            $container = Container::where('container_number', $container_number)
                ->with('movements')
                ->firstOrFail();

            $container->movements->transform(function ($movement) {
                if (is_array($movement->photos)) {
                    $movement->photos = array_map(fn($path) => url($path), $movement->photos);
                }
                return $movement;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Container movement detail retrieved successfully',
                'data' => $container->movements,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Container tidak ditemukan',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail container',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
