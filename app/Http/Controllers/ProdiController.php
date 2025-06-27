<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class ProdiController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view prodi', only: ['index']),
            new Middleware('permission:edit prodi', only: ['edit']),
            new Middleware('permission:create prodi', only: ['create']),
            new Middleware('permission:delete prodi', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prodis = Prodi::all();
        return view('prodi.index', compact('prodis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('prodi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $data = $request->validate([
            'name' => 'required|unique:prodis,name',
            'jenjang' => 'required',
            'kaprodi' => 'required',
        ]);

        Prodi::create($data);
        return redirect()->route('prodis.index')->with('success', 'Prodi created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $prodi = Prodi::findOrFail($id);
        return view('prodi.edit', compact('prodi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prodi = Prodi::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|unique:prodis,name,' . $prodi->id,
            'jenjang' => 'required',
            'kaprodi' => 'required',
        ]);

        $prodi->update($data);

        return redirect()->route('prodis.index')->with('success', 'Prodi updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prodi = Prodi::findOrFail($id);
        $prodi->delete();

        return redirect()->route('prodis.index')->with('success', 'Prodi deleted successfully.');
    }
}
