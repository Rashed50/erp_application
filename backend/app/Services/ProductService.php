<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    /**
     * @param  bool|null  $active  Filter by active status, or null for all products.
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?bool $active = null): LengthAwarePaginator
    {
        return Product::query()
            ->when($search, fn ($query) => $query->search($search))
            ->when(! is_null($active), fn ($query) => $query->where('active_status', $active))
            ->latest()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array{name: string, code?: ?string, active_status?: ?bool, branch_office_id?: ?int}  $data
     */
    public function create(array $data): Product
    {
        return Product::create([
            ...$data,
            // Like the payroll module, a code is generated when none is given.
            'code' => $data['code'] ?? $this->generateCode(),
            'active_status' => $data['active_status'] ?? true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->fill([...$data, 'updated_by' => Auth::id()]);
        $product->save();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    /**
     * Soft deleted products keep their code, so they are included in the check.
     */
    private function generateCode(): string
    {
        do {
            $code = 'P-'.random_int(10000, 99999);
        } while (Product::withTrashed()->where('code', $code)->exists());

        return $code;
    }
}
