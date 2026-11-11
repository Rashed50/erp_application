<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * Invoice totals of a sale or purchase, worked out from its line items and
 * its overall discount / VAT the same way LineItemTotalCalculator does:
 *
 * - total_amount: every line's qty x unit price
 * - discount_amount: the lines' discounts plus the overall discount
 * - vat_amount: the lines' VAT plus the overall VAT
 * - net_total: total_amount - discount_amount + vat_amount
 *
 * A list query adds the line sums with withLineTotals(); otherwise they come
 * from the loaded items, or one query on the items.
 */
trait HasLineItemTotals
{
    /**
     * The line items' table and its foreign key to this model.
     *
     * @return array{0: string, 1: string}
     */
    abstract protected static function lineItemsTable(): array;

    /**
     * Sum of one line-item expression for the outer row, in SQL.
     */
    protected static function lineSumSql(string $expression): string
    {
        [$items, $foreignKey] = static::lineItemsTable();
        $table = (new static)->getTable();

        return "(select coalesce(sum({$expression}), 0) from {$items} where {$items}.{$foreignKey} = {$table}.id)";
    }

    /**
     * The net total of the outer row, for SQL sums and filters.
     */
    public static function netTotalSql(): Expression
    {
        $table = (new static)->getTable();

        return DB::raw('('.static::lineSumSql('qty * unit_price').' - '.static::lineSumSql('discount')." - {$table}.overall_discount + "
            .static::lineSumSql('vat')." + {$table}.overall_vat)");
    }

    public function scopeWithLineTotals(Builder $query): Builder
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select($this->getTable().'.*');
        }

        return $query->selectRaw(static::lineSumSql('qty * unit_price').' as line_gross, '
            .static::lineSumSql('discount').' as line_discount, '
            .static::lineSumSql('vat').' as line_vat');
    }

    /**
     * @return array{gross: float, discount: float, vat: float}
     */
    public function lineSums(): array
    {
        if (array_key_exists('line_gross', $this->attributes)) {
            return [
                'gross' => (float) $this->attributes['line_gross'],
                'discount' => (float) $this->attributes['line_discount'],
                'vat' => (float) $this->attributes['line_vat'],
            ];
        }

        if ($this->relationLoaded('items')) {
            return [
                'gross' => (float) $this->items->sum(fn ($item) => (float) $item->qty * (float) $item->unit_price),
                'discount' => (float) $this->items->sum('discount'),
                'vat' => (float) $this->items->sum('vat'),
            ];
        }

        $sums = $this->items()->toBase()
            ->selectRaw('coalesce(sum(qty * unit_price), 0) as gross, coalesce(sum(discount), 0) as discount, coalesce(sum(vat), 0) as vat')
            ->first();

        return ['gross' => (float) $sums->gross, 'discount' => (float) $sums->discount, 'vat' => (float) $sums->vat];
    }

    protected function totalAmount(): Attribute
    {
        return Attribute::get(fn () => round($this->lineSums()['gross'], 2));
    }

    protected function discountAmount(): Attribute
    {
        return Attribute::get(fn () => round($this->lineSums()['discount'] + (float) $this->overall_discount, 2));
    }

    protected function vatAmount(): Attribute
    {
        return Attribute::get(fn () => round($this->lineSums()['vat'] + (float) $this->overall_vat, 2));
    }

    protected function netTotal(): Attribute
    {
        return Attribute::get(function () {
            $sums = $this->lineSums();

            return round($sums['gross'] - $sums['discount'] - (float) $this->overall_discount + $sums['vat'] + (float) $this->overall_vat, 2);
        });
    }
}
