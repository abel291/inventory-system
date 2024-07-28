<?php

namespace App\Filament\Resources\SaleResource\Form;

use App\Enums\ContactTypesEnum;
use App\Models\Contact;
use App\Models\Location;
use App\Services\SaleService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

class SaleFormDiscount
{
    public static function form()
    {
        return [
            TextInput::make('discount.percent')->label("Descuento")
                ->placeholder('Sin descuento')
                ->live()
                ->maxValue(100)
                ->minValue(0)
                ->suffix('%')
                ->numeric()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    SaleFormItemProduct::updateTotals($get, $set);
                }),

            TextInput::make('delivery')->label('Envio')
                ->live(debounce: 500)
                ->default(0)
                ->afterStateUpdated(function (Get $get, Set $set) {
                    SaleFormItemProduct::updateTotals($get, $set);
                })
                ->numeric()
                ->prefix('$')
        ];
    }
}
