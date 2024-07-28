<?php

namespace App\Filament\Resources\SaleResource\Form;

use App\Enums\ContactTypesEnum;
use App\Enums\SalePaymentTypeEnum;
use App\Models\Contact;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Services\SaleService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;

class SaleFormPayment
{
    public static function form()
    {
        return [
            Radio::make('payment_type')->label("Tipo de pago")
                ->options(SalePaymentTypeEnum::class)
                ->default('cash')
                ->live()
                ->inline()
                ->inlineLabel(false),
            Grid::make(2)
                ->columnSpan(2)
                ->visible(fn (Get $get) => $get('payment_type') == 'cash')
                ->schema(
                    [
                        Select::make('payment.payment_method_id')->label("Metodo de pago")
                            ->requiredIf('payment_type', 'cash')
                            ->options(PaymentMethod::all()->pluck('name', 'id')),

                        TextInput::make('payment.reference')->label('Referencia')
                            ->requiredIf('payment_type', 'cash'),

                        Textarea::make('payment.note')->label('Observacion')
                            // ->requiredIf('payment_type', 'cash')
                            ->columnSpanFull()
                    ]
                ),
            Placeholder::make('info')
                ->visible(fn (Get $get) => $get('payment_type') != 'cash')
                ->label('')
                ->content(fn (): string => 'Luego de crear la venta podras gestionar los pagos en la seccion de financiacion')
        ];
    }
}
