<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $categorias = Categoria::all();

        return view('categoria.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categoria = new Categoria();
        $categoriasPadre = $this->obtenerCategoriasPadre();

        return view('categoria.create', compact('categoria', 'categoriasPadre'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoriaRequest $request): RedirectResponse
    {
        $data = $this->prepareCategoriaPayload($request->validated());
        Categoria::create($data);

        return Redirect::route('categorias.index')
            ->with('success', 'Subcategoría creada satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $categoria = Categoria::find($id);

        return view('categoria.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $categoria = Categoria::find($id);
        $categoriasPadre = $this->obtenerCategoriasPadre();

        return view('categoria.edit', compact('categoria', 'categoriasPadre'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $data = $this->prepareCategoriaPayload($request->validated());
        $categoria->update($data);

        return Redirect::route('categorias.index')
            ->with('success', 'Subcategoría actualizada satisfactoriamente.');
    }

    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:255'],
        ]);

        $data = $this->prepareCategoriaPayload($validated);
        $categoria = Categoria::create($data);

        return response()->json([
            'id' => $categoria->id_categoria,
            'nombre' => $categoria->nombre,
            'categoria' => $categoria->categoria,
        ]);
    }

    private function prepareCategoriaPayload(array $data): array
    {
        $nombre = trim($data['nombre']);
        $categoria = trim($data['categoria'] ?? '') ?: $nombre;

        return [
            'nombre' => $nombre,
            'categoria' => $categoria,
        ];
    }

    private function obtenerCategoriasPadre(): array
    {
        return Categoria::query()
            ->whereNotNull('categoria')
            ->pluck('categoria')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function destroy($id): RedirectResponse
    {
        Categoria::find($id)->delete();

        return Redirect::route('categorias.index')
            ->with('success', 'Subcategoría eliminada satisfactoriamente.');
    }
}
