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
    /**
     * GET /api/movements
     * Menampilkan daftar riwayat pergerakan kontainer
     */
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
            $container = Container::firstOrCreate(
                ['container_number' => $request->container_number],
                ['status' => 'out']
            );

            // CEK STATUS
            if ($container->status === 'in') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Container sudah berada di TPS, tidak bisa masuk lagi',
                ], 422);
            }

            $ts = now()->format('YmdHis');
            $basePath = "uploads/containers/{$container->container_number}/in/{$ts}";
            $publicPath = public_path($basePath);

            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0775, true);
            }

            $photos = [];
            foreach (['front', 'left', 'right', 'rear'] as $key) {
                $file = $request->file($key);
                $fileName = $key . '.' . $file->getClientOriginalExtension();
                $file->move($publicPath, $fileName);
                $photos[$key] = $basePath . '/' . $fileName;
            }

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
            'front'            => 'required|image',
            'left'             => 'required|image',
            'right'            => 'required|image',
            'rear'             => 'required|image',
        ]);

        return DB::transaction(function () use ($request) {
            $container = Container::where('container_number', $request->container_number)->firstOrFail();

            // CEK STATUS
            if ($container->status !== 'in') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Container tidak berada di TPS, tidak bisa keluar',
                ], 422);
            }

            $ts = now()->format('YmdHis');
            $basePath = "uploads/containers/{$container->container_number}/out/{$ts}";
            $publicPath = public_path($basePath);

            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0775, true);
            }

            $photos = [];
            foreach (['front', 'left', 'right', 'rear'] as $key) {
                $file = $request->file($key);
                $fileName = $key . '.' . $file->getClientOriginalExtension();
                $file->move($publicPath, $fileName);
                $photos[$key] = $basePath . '/' . $fileName;
            }

            ContainerMovements::create([
                'container_id'     => $container->id,
                'direction'        => 'out',
                'truck_plate_out'  => $request->truck_plate_out,
                'seal_ship'        => $request->seal_ship,
                'photos'           => $photos,
                'notes'            => $request->notes,
                'timestamp'        => now(),
            ]);

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

}
