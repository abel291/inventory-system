<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ProductStats extends BaseWidget
{

    protected function getTablePage(): string
    {
        return ListProducts::class;
    }
    protected function getStats(): array
    {
        $products = Product::select('id', 'price', 'active')->get();
        return [
            // Stat::make('Total Producto en existencia', Number::format($products->sum('stock'), locale: 'es')),
            Stat::make('Producto Activos', Number::format($products->where('active', 1)->count())),
            Stat::make('Precio medio', Number::currency($products->avg('price'), 'COP', locale: 'es')),
        ];
    }
}
