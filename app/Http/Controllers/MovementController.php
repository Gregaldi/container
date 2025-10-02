<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\ContainerMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
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

            ContainerMovement::create([
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

            ContainerMovement::create([
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
    public function index($container_number)
    {
        $container = Container::where('container_number', $container_number)
            ->with('movements')
            ->firstOrFail();

        return response()->json($container->movements);
    }
}
