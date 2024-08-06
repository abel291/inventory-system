<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SaleResource;
use App\Filament\Resources\SaleResource\Pages\ViewSale;
use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestSalesWidget extends BaseWidget
{
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Ultimas Ventas';
    public function table(Table $table): Table
    {

        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        return $table
            ->query(Sale::query()
                ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->withCount('saleProducts')->latest())
            ->defaultPaginationPageOption(8)
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('code')->label('Codigo'),
                TextColumn::make('location.nameType')->label('Ubicacion'),
                TextColumn::make('client.name')->label('Cliente'),
                TextColumn::make('sale_products_count')->label('Productos'),
                TextColumn::make('status')->label('Estado')->badge(),
                TextColumn::make('payment_type')->label('Tipo de pago')->badge(),
                TextColumn::make('total')->label('Total')->money(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('Ver')
                    ->url(fn (Sale $record): string => ViewSale::getUrl(['record' => $record->id]))
            ]);
    }
}
