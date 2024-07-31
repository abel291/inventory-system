<?php

namespace App\Filament\Resources\SaleResource\Widgets;

use App\Enums\SaleStatuEnum;
use App\Models\Sale;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SaleStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $sales = Sale::
            where("status", SaleStatuEnum::ACCEPTED)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->get();

        $chartSalesMonth = $sales->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function ($item) {
            return $item->count();
        })->values()->toArray();

        return [
            Stat::make('Ventas de ' . now()->isoFormat('MMMM'), $sales->count())
                ->chart($chartSalesMonth),
            Stat::make('total ingresos', Number::currency($sales->sum('total'))),
            Stat::make('Venta promedio', Number::currency($sales->average('total'))),
        ];
    }
}
