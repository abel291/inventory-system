<?php

namespace App\Filament\Resources\PayrollResource\Widgets;

use App\Enums\SaleStatuEnum;
use App\Models\Payroll;
use App\Models\Sale;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PayrollStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $payrolls = Payroll::
            whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->get();

        $chartSalesMonth = $payrolls->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function ($item) {
            return $item->count();
        })->values()->toArray();

        return [
            Stat::make('Nominas de ' . now()->isoFormat('MMMM'), $payrolls->count())
                ->chart($chartSalesMonth),
            Stat::make('Total pagos de nominas', Number::currency($payrolls->sum('amount'))),
            // Stat::make('Venta promedio', Number::currency($payrolls->average('total'))),
        ];
    }
}
