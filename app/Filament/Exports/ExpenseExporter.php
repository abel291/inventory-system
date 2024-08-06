<?php

namespace App\Filament\Exports;

use App\Models\Expense;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ExpenseExporter extends Exporter
{
    protected static ?string $model = Expense::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('Codigo'),
            ExportColumn::make('reason')->label('Motivo'),
            ExportColumn::make('amount')->label('Monto'),
            ExportColumn::make('note')->label('Observacion'),
            ExportColumn::make('expenseType.name')->label('Tipo de gasto'),
            ExportColumn::make('created_at')->label('Fecha de creacion'),
            ExportColumn::make('updated_at')->label('Fecha de modificacion'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your expense export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
