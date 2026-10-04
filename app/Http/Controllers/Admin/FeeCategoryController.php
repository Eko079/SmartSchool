<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeCategory::withCount('bills')
            ->withSum('bills as total_collected', 'paid_amount');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        if ($request->input('status') === 'aktif') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'nonaktif') {
            $query->where('is_active', false);
        }

        // ---------- Sorting via klik header ----------
        $allowedSorts = ['code', 'name', 'amount', 'type', 'status'];
        $sort = $request->input('sort');
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = null;
        }
        $dir = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        if ($sort === 'amount') {
            $query->orderBy('default_amount', $dir);
        } elseif ($sort === 'status') {
            $query->orderBy('is_active', $dir);
        } elseif ($sort) {
            $query->orderBy($sort, $dir);
        } else {
            $query->orderBy('id');
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        // Pagination hanya bila data melebihi per_page — tabel kecil tampil penuh.
        $total = (clone $query)->count();
        $categories = $total > $perPage
            ? $query->paginate($perPage)->withQueryString()
            : $query->get();

        $stats = [
            'total_categories' => FeeCategory::count(),
            'active_categories' => FeeCategory::where('is_active', true)->count(),
            'total_bills' => \App\Models\Bill::count(),
            'total_revenue' => \App\Models\Bill::sum('paid_amount') ?? 0,
        ];

        return view('admin.kategori-tagihan.index', compact('categories', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:fee_categories,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:bulanan,sekali,bebas',
            'default_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        FeeCategory::create($validated);

        return redirect()->route('admin.kategori-tagihan')->with('success', 'Kategori tagihan berhasil ditambahkan.');
    }

    public function toggle(FeeCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return redirect()->route('admin.kategori-tagihan')->with('success', 'Status kategori berhasil diubah.');
    }

    public function update(Request $request, FeeCategory $category)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:fee_categories,code,' . $category->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:bulanan,sekali,bebas',
            'default_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ], [
            'code.unique' => 'Kode sudah dipakai kategori lain.',
        ]);

        $category->update($validated);

        return redirect()->route('admin.kategori-tagihan')->with('success', 'Kategori tagihan berhasil diperbarui.');
    }
}

