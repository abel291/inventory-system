<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SaleResource;
use App\Filament\Resources\SaleResource\Pages\ViewSale;
use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSalesWidget extends BaseWidget
{

    protected static ?string $heading = 'Ultimas Ventas';
    public function table(Table $table): Table
    {
        return $table
            ->query(Sale::query()->withCount('saleProducts')->latest())
            ->defaultPaginationPageOption(8)
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Codigo'),
                Tables\Columns\TextColumn::make('sale_products_count')->label('Productos'),
                Tables\Columns\TextColumn::make('status')->label('Estado')->badge(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('Ver')
                    ->url(fn(Sale $record): string => ViewSale::getUrl(['record' => $record->id]))
            ])
        ;
    }
}
