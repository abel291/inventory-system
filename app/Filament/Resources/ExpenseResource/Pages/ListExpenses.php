<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\ProductResource\Widgets\ExpenseStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth(MaxWidth::TwoExtraLarge),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseStats::class,
        ];
    }
}
