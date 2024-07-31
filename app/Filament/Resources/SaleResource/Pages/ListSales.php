<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Enums\SaleStatuEnum;
use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    public function getTabs(): array
    {
        $stockStatu = [];
        foreach (SaleStatuEnum::cases() as $key => $case) {
            $stockStatu[$case->value] = Tab::make($case->getLabel())
                ->icon($case->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $case->value));
        }
        return [

            'all' => Tab::make('Todos las ventas'),
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
