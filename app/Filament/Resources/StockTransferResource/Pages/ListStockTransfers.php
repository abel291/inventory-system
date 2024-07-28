<?php

namespace App\Filament\Resources\StockTransferResource\Pages;

use App\Enums\StockStatuEnum;
use App\Filament\Resources\StockTransferResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStockTransfers extends ListRecords
{
    protected static string $resource = StockTransferResource::class;

    public function getTabs(): array
    {
        $stockStatu = [];
        foreach (StockStatuEnum::cases() as $key => $case) {
            $stockStatu[$case->value] = Tab::make($case->getLabel())
                ->icon($case->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $case->value));
        }
        return [

            'all' => Tab::make('Todos los traslados'),
            ...$stockStatu
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
