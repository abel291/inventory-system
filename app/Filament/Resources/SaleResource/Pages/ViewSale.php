<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Enums\SalePaymentTypeEnum;
use App\Enums\SaleStatuEnum;
use App\Enums\StockStatuEnum;
use App\Filament\Resources\SaleResource;
use App\Filament\Resources\SaleResource\RelationManagers\PaymentsRelationManager;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Sale;
use Filament\Actions;

use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;
use Filament\Infolists\Components\Actions as ComponentsActions;
use Filament\Infolists\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use PhpParser\Node\Stmt\Label;

class ViewSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    public function getTitle(): string
    {
        return "Venta {$this->record->code}";
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Grid::make(3)
                    ->schema([
                        ComponentsActions::make([
                            Action::make('status-change')->label('Devolucion')
                                ->color('danger')
                                ->link()
                                ->icon('heroicon-o-receipt-refund')
                                ->visible(fn($record) => ($record->status == SaleStatuEnum::ACCEPTED))

                                ->requiresConfirmation()
                                ->modalIcon('heroicon-o-check')
                                ->action(function (Sale $record) {
                                    $record->status = SaleStatuEnum::CANCELLED;
                                    $record->refund_at = now();
                                    $record->save();
                                    Notification::make()
                                        ->title("La venta {$record->code} fue cancelada")
                                        ->success()
                                        ->send();
                                }),

                        ])->columns(4)->columnStart(2)->alignment(Alignment::End),
                        Section::make([
                            TextEntry::make('client.name')->label('Cliente'),
                            TextEntry::make('client.phone')->label('Telefono'),
                            TextEntry::make('client.email')->label('Email'),
                            TextEntry::make('client.nit')->label('Nit'),
                            TextEntry::make('location.nameType')->label('Ubicacion'),
                            TextEntry::make('user.name')->label('Vendedor'),
                            TextEntry::make('status')->label('Estado')->badge(),
                            TextEntry::make('payment_type')->label('Tipo de pago')->badge(),
                            ViewEntry::make('products')->columnSpanFull()->view('filament.infolists.sales-view')
                        ])
                            ->columnSpan(2)
                            ->columns(4),

                        Grid::make(1)

                            ->columnSpan(1)
                            ->schema([
                                Section::make([
                                    TextEntry::make('created_at')->label('Fecha de  la venta')->dateTime(),
                                    TextEntry::make('refund_at')->visible(fn($state) => $state)->label('Fecha de  la venta')->dateTime(),

                                ])->columnSpan(1),
                                Section::make('Pago')
                                    ->visible(fn($record) => $record->payment_type == SalePaymentTypeEnum::CASH)
                                    ->schema([
                                        TextEntry::make('payment.paymentMethod.name')->label('Metodo de pago'),
                                        TextEntry::make('payment.reference')->label('Referencia'),
                                        TextEntry::make('payment.amount')->label('Monto')
                                            ->formatStateUsing(fn($state) => "$ " . Number::format($state)),
                                        TextEntry::make('payment.note')->label('Observacion')->default('- sin observacion')



                                    ])
                            ])
                    ])

            ]);
    }

    // public  function getRelationManagers(): array
    // {
    //     return [
    //         PaymentsRelationManager::class,
    //     ];
    // }
}
