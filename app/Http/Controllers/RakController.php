<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Rack;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class RakController extends Controller
{
    /**
     * Display the digital rack map.
     */
    public function index()
    {
        $racks = Rack::with(['category', 'products' => function($q) {
            $q->where('is_active', true)
              ->select('product_id', 'product_name', 'stock', 'rack_id');
        }])
        ->withCount(['products as active_products_count' => function($q) {
            $q->where('is_active', true);
        }])
        ->orderBy('rack_code')
        ->get()
        ->map(fn($r) => [
            'id'          => $r->rack_id,
            'code'        => $r->rack_code,
            'category'    => $r->is_custom_box ? 'Kotak Kustom' : ($r->category?->category_name ?? 'Kosong'),
            'category_id' => $r->category_id,
            'capacity'    => $r->capacity,
            'count'       => $r->is_custom_box ? 0 : $r->active_products_count,
            'row'         => $r->row_position,
            'col'         => $r->col_position,
            'is_box'      => (bool) $r->is_custom_box,
            'products'    => $r->is_custom_box ? [] : $r->products->map(fn($p) => [
                'id'    => $p->product_id,
                'name'  => $p->product_name,
                'stock' => $p->stock,
            ])->toArray(),
        ]);
        
        $categories = Category::orderBy('category_name')->get();
        
        return view('rak.index', compact('racks', 'categories'));
    }

    /**
     * Save the updated interactive grid map layout.
     */
    public function saveLayout(Request $request)
    {
        $validated = $request->validate([
            'layout' => 'required|array',
            'layout.*.id' => 'nullable|integer',
            'layout.*.code' => 'required|string|max:20',
            'layout.*.category_id' => 'nullable|integer|exists:categories,category_id',
            'layout.*.capacity' => 'nullable|integer|min:0',
            'layout.*.row' => 'required|integer|between:1,6',
            'layout.*.col' => 'required|integer|between:1,6',
            'layout.*.is_box' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $layout = $validated['layout'];
            $incomingIds = collect($layout)->pluck('id')->filter()->toArray();

            // Delete racks/boxes that are not in the layout anymore
            Rack::whereNotIn('rack_id', $incomingIds)->delete();

            // We do a two-pass save to prevent unique constraint conflicts on rack_code.
            // Pass 1: save/update everything with temporary codes
            $racksToUpdate = [];
            $tempIndex = 0;
            foreach ($layout as $item) {
                $tempIndex++;
                $tempCode = 't_' . $tempIndex;

                if (!empty($item['id'])) {
                    $rack = Rack::find($item['id']);
                    if ($rack) {
                        // Assign a temporary code to avoid code collisions
                        $rack->rack_code = $tempCode;
                        $rack->category_id = $item['is_box'] ? null : $item['category_id'];
                        $rack->capacity = $item['is_box'] ? 0 : ($item['capacity'] ?? 50);
                        $rack->row_position = $item['row'];
                        $rack->col_position = $item['col'];
                        $rack->is_custom_box = $item['is_box'];
                        $rack->save();

                        $racksToUpdate[] = [
                            'rack' => $rack,
                            'final_code' => $item['code']
                        ];
                    }
                } else {
                    // New Rack or Custom Box
                    $rack = new Rack();
                    $rack->rack_code = $tempCode;
                    $rack->category_id = $item['is_box'] ? null : $item['category_id'];
                    $rack->capacity = $item['is_box'] ? 0 : ($item['capacity'] ?? 50);
                    $rack->row_position = $item['row'];
                    $rack->col_position = $item['col'];
                    $rack->is_custom_box = $item['is_box'];
                    $rack->save();

                    $racksToUpdate[] = [
                        'rack' => $rack,
                        'final_code' => $item['code']
                    ];
                }
            }

            // Pass 2: apply the final rack/box code
            foreach ($racksToUpdate as $update) {
                $rack = $update['rack'];
                $rack->rack_code = $update['final_code'];
                $rack->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tata letak berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan tata letak: ' . $e->getMessage()], 500);
        }
    }
}
