<?php

namespace App\Filament\Resources\StockEntryResource\Pages;

use App\Enums\StockStatuEnum;
use App\Filament\Resources\StockEntryResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStockEntry extends ListRecords
{
    protected static string $resource = StockEntryResource::class;
    public function getTabs(): array
    {
        $stockStatu = [];
        foreach (StockStatuEnum::cases() as $key => $case) {
            $stockStatu[$case->value] = Tab::make($case->getLabel())
                ->icon($case->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $case->value));
        }
        return [

            'all' => Tab::make('Todas las entradas'),
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
