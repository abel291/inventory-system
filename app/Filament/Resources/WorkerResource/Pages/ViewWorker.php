<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;

class ViewWorker extends ViewRecord
{
    protected static string $resource = WorkerResource::class;
    public function getHeading(): string
    {
        return $this->record->name;
    }



    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist

            ->schema([
                Section::make()
                    ->columns(3)->schema([
                        TextEntry::make('name')->label('Nombre'),
                        TextEntry::make('nit')->label('Nit/Cedula')->numeric()->icon('heroicon-o-identification'),
                        TextEntry::make('phone')->label('Telefono')->icon('heroicon-o-phone'),
                        TextEntry::make('email')->label('Email')->icon('heroicon-o-envelope'),
                        TextEntry::make('address')->label('Direccion')->icon('heroicon-o-map'),
                        TextEntry::make('description')->label('Descripcion'),
                    ])
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->modalWidth(MaxWidth::TwoExtraLarge),
        ];
    }
}
