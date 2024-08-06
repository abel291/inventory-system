<?php

namespace App\Filament\Widgets;

use App\Enums\ContactTypesEnum;
use App\Enums\SaleStatuEnum;
use App\Models\Contact;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\Sale;
use Dashboard;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;


    protected static ?int $sort = 0;
    protected function getStats(): array
    {

        $filterMonth = Dashboard::filterDateSelected($this->filters['select_month']);


        $sales = Sale::select('id', 'status', 'created_at', 'total')
            ->where('status', SaleStatuEnum::ACCEPTED)
            ->when($filterMonth, fn (Builder $query) => $query->whereDate('created_at', '>=', $filterMonth))
            ->orderBy('created_at', 'desc')->get();

        $salesPerDays = $sales->groupBy(function ($sale) {
            return (int) $sale->created_at->format('d');
        })->map(function ($item) {
            return $item->count();
        });

        $payrolls = Payroll::select('id', 'amount', 'created_at')
            ->when($filterMonth, fn (Builder $query) => $query->whereDate('created_at', '>=', $filterMonth))
            ->orderBy('created_at', 'desc')->get();

        $payrollsPerDays = $payrolls->groupBy(function ($payroll) {
            return (int) $payroll->created_at->format('d');
        })->map(function ($item) {
            return $item->count();
        });

        $productBestSeller = Product::select('id', 'name', 'price')
            ->withCount(['sales' => function (Builder $query) use ($filterMonth) {
                $query->where('sales.status', SaleStatuEnum::ACCEPTED)
                    ->when($filterMonth, fn (Builder $query) => $query->whereDate('sales.created_at', '>=', $filterMonth));
            }])
            ->whereHas('sales', function (Builder $query) use ($filterMonth) {
                $query->where('sales.status', SaleStatuEnum::ACCEPTED)
                    ->when($filterMonth, fn (Builder $query) => $query->whereDate('sales.created_at', '>=', $filterMonth));
            })->orderBy('sales_count', 'desc')->first();


        return [
            Stat::make('Ventas', $sales->count() . ' ventas')
                ->description(Number::currency($sales->sum('total')))
                ->chart($salesPerDays->toArray())->color('success'),

            Stat::make('Pago de Nominas',  $payrolls->count() . ' pagos')
                ->color('danger')
                ->description(Number::currency($payrolls->sum('amount')))
                ->chart($payrollsPerDays->toArray()),

            Stat::make('Producto mas vendido', $productBestSeller->sales_count . ' ventas')
                ->description($productBestSeller->name . ' ' . Number::currency($productBestSeller->price)),

        ];
    }
}
