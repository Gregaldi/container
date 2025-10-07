<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\ContainerMovements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $query = ContainerMovements::with('container');

        // Filter berdasarkan container_id
        if ($request->filled('container_id')) {
            $query->where('container_id', $request->container_id);
        }

        // Filter berdasarkan arah (IN / OUT)
        if ($request->filled('direction')) {
            $query->where('direction', strtoupper($request->direction));
        }

        // Filter berdasarkan tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('timestamp', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        $movements = $query->orderBy('timestamp', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Container movement history retrieved successfully',
            'data' => $movements,
        ]);
    }
    // POST /api/movements/in
    public function storeIn(Request $request)
    {
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

            // upload foto
            $ts = now()->format('YmdHis');
            $basePath = "containers/{$container->container_number}/in/{$ts}";
            $photos = [];

            foreach (['front', 'left', 'right', 'rear'] as $key) {
                $file = $request->file($key);
                $path = $file->storeAs("public/{$basePath}", $key . '.' . $file->getClientOriginalExtension());
                $photos[$key] = str_replace('public/', '', $path);
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

            return response()->json(['message' => 'Container masuk TPS berhasil']);
        });
    }

    // POST /api/movements/out
    public function storeOut(Request $request)
    {
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

            if ($container->status !== 'in') {
                return response()->json(['error' => 'Container tidak berada di TPS'], 422);
            }

            // upload foto
            $ts = now()->format('YmdHis');
            $basePath = "containers/{$container->container_number}/out/{$ts}";
            $photos = [];

            foreach (['front', 'left', 'right', 'rear'] as $key) {
                $file = $request->file($key);
                $path = $file->storeAs("public/{$basePath}", $key . '.' . $file->getClientOriginalExtension());
                $photos[$key] = str_replace('public/', '', $path);
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

            return response()->json(['message' => 'Container keluar TPS berhasil']);
        });
    }

    // GET /api/movements/{container_number}
    public function detailindex($container_number)
    {
        $container = Container::where('container_number', $container_number)
            ->with('movements')
            ->firstOrFail();

        return response()->json($container->movements);
    }
}
