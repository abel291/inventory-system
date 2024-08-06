<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Filament\Resources\ExpenseResource\Pages\ListExpenses;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Expense;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ExpenseStats extends BaseWidget
{

    protected function getTablePage(): string
    {
        return ListExpenses::class;
    }
    protected function getStats(): array
    {
        $lastDays = now()->subDays(30);
        $expense = Expense::select('id', 'amount')
            ->whereDate('created_at', '>=', $lastDays)
            ->orderBy('date', 'desc')
            ->get();

        return [
            Stat::make(
                'Total Gastos los ultimos 30 dias',
                Number::currency($expense->sum('amount'))
            )->chart($expense->pluck('amount')->toArray()),
            Stat::make('Gastos producidos', Number::format($expense->count())),
            // Stat::make('Precio medio', Number::currency($products->avg('price'), 'COP', locale: 'es')),
        ];
    }
}
