<?php

namespace App\Filament\Resources\StockTransferResource\Pages;

use App\Filament\Resources\StockTransferResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateStockTransfer extends CreateRecord
{
    protected static string $resource = StockTransferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_request_id'] = auth()->id();
        return $data;
    }
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->extraAttributes(['type' => 'button', 'wire:click' => 'create']);
    }
    // protected function getSaveFormAction(): Action
    // {
    //     return parent::getSaveFormAction()->extraAttributes(['type' => 'button', 'wire:click' => 'save']);
    // }
}
