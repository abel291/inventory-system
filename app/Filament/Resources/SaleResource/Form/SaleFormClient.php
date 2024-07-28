<?php

namespace App\Filament\Resources\SaleResource\Form;

use App\Enums\ContactTypesEnum;
use App\Models\Contact;
use App\Models\Location;
use App\Services\SaleService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

class SaleFormClient
{
    public static function form()
    {
        return [
            TextInput::make('code')->label('Codigo')
                ->default(SaleService::generateCode())
                ->disabled()
                ->dehydrated(),
            Select::make('contact_id')
                ->label('Cliente')
                ->searchable(['name', 'email', 'nit', 'phone'])
                ->getOptionLabelFromRecordUsing(fn (Contact $record) => "{$record->name} - nit {$record->nit}")
                ->relationship(
                    'client',
                    'name',
                    modifyQueryUsing: fn (Builder $query) => $query->whereIn('type', [
                        ContactTypesEnum::CLIENT, ContactTypesEnum::CLIENT_PROVIDER
                    ])->orderBy('id', 'desc'),
                )
                ->default(fn () => Contact::where('nit', '222222222')->first()->id)
                ->preload(),

            DateTimePicker::make('created_at')->disabled()->native(false)->default(now())->secondsStep(false)->label('Fecha'),

            Select::make('location_id')->label('Ubicacion')
                ->disabled(fn (Get $get) => ($get('saleProducts')))
                ->live()
                ->relationship('location', 'name', fn ($query) => ($query->active()))
                ->getOptionLabelFromRecordUsing(fn (Location $record) => "{$record->nameType}")
                ->default(1)
                ->selectablePlaceholder(false)
                ->preload()
                ->afterStateUpdated(function (Set $set) {
                    $set('saleProducts', []);
                    $set('discount_percent', null);
                    $set('discount_amount', null);
                })
                ->required(),
        ];
    }
}
