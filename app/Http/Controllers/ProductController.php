<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('nombre')->paginate(20);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'nullable|string',
            'precio_costo'  => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'imagen'        => 'nullable|image|max:2048',
            'activo'        => 'boolean',
        ]);

        if ($request->hasFile('imagen')) {
            $data['image_path'] = $this->guardarImagen($request->file('imagen'));
        }

        unset($data['imagen']);
        $data['activo'] = $request->boolean('activo', true);
        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', '✅ Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // ── PATCH rápido desde el frontend (fetch JSON) ────────────
        // Solo actualiza precio_venta si viene como petición JSON/AJAX
        if ($request->expectsJson() || $request->wantsJson()) {
            // Acepta precio_venta, stock, o ambos en una misma llamada PATCH
            $data = $request->validate([
                'precio_venta' => 'sometimes|numeric|min:0',
                'stock'        => 'sometimes|integer|min:0',
            ]);

            $product->update($data);

            return response()->json([
                'success'      => true,
                'precio_venta' => $product->fresh()->precio_venta,
                'stock'        => $product->fresh()->stock,
                'mensaje'      => "Producto \"{$product->nombre}\" actualizado.",
            ]);
        }

        // ── PUT completo desde el formulario de edición ────────────
        $data = $request->validate([
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'nullable|string',
            'precio_costo'  => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'imagen'        => 'nullable|image|max:2048',
            'activo'        => 'boolean',
        ]);

        if ($request->hasFile('imagen')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $this->guardarImagen($request->file('imagen'));
        }

        unset($data['imagen']);
        $data['activo'] = $request->boolean('activo', false);
        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', '✅ Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', "🗑️ \"{$product->nombre}\" desactivado.");
    }

    // ─── Helpers ──────────────────────────────────────────────────
    private function guardarImagen($file): string
    {
        $nombre = uniqid('prod_') . '.jpg';
        $file->storeAs('productos', $nombre, 'public');
        return 'productos/' . $nombre;
    }
}
