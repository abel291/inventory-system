<?php

namespace App\Filament\Widgets;

use App\Enums\SaleStatuEnum;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use Dashboard;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class CategorySalesChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Ventas por categoria';

    protected function getData(): array
    {

        $filterMonth = Dashboard::filterDateSelected($this->filters['select_month']);

        $categories = Category::with(['products' => function ($query) use ($filterMonth) {
            $query
                ->withCount(['sales' => function (Builder $query2) use ($filterMonth) {
                    $query2->where('sales.status', SaleStatuEnum::ACCEPTED)
                        ->when($filterMonth, fn (Builder $query) => $query->whereDate('sales.created_at', '>=', $filterMonth));
                }]);
        }])->get()->mapWithKeys(function ($category, int $key) {
            return [$category->name => $category->products->sum('sales_count')];
        });

        // $sales = Sale::with('products.category')
        //     ->where('status', SaleStatuEnum::ACCEPTED)
        //     ->whereDate('sales.created_at', '>=', $filterMonth)->get();
        // dd($sales->pluck('products')->collapse()->groupBy('category.name'));

        self::$heading = self::$heading . " (" . Number::format($categories->sum()) . " productos vendidos)";
        return [
            'datasets' => [
                [
                    'label' => 'Ventas',
                    'data' => $categories->values()->toArray(),

                ],
            ],
            'labels' => $categories->keys()->toArray(),
        ];
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
