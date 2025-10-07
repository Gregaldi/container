<?php

namespace App\Http\Controllers;

use App\Models\Container;
use Illuminate\Http\Request;

class ContainerController extends Controller
{

    // POST /api/containers
    public function store(Request $request)
    {
        $request->validate([
            'container_number' => 'required|string|unique:containers,container_number',
            'size' => 'nullable|string',
        ]);

        $container = Container::create([
            'container_number' => $request->container_number,
            'size' => $request->size,
            'status' => 'out', // default di luar TPS
        ]);

        return response()->json($container, 201);
    }

    // GET /api/containers/{container_number}
    public function show($container_number)
    {
        $container = Container::where('container_number', $container_number)
            ->with('movements')
            ->firstOrFail();

        return response()->json($container);
    }
}
