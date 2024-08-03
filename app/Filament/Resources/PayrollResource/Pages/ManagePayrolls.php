<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Filament\Resources\PayrollResource\Widgets\PayrollStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\MaxWidth;

class ManagePayrolls extends ManageRecords
{
    protected static string $resource = PayrollResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth(MaxWidth::TwoExtraLarge)
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();
                    return $data;
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PayrollStatsOverview::class,
        ];
    }
}
