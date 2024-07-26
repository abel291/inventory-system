<?php

namespace App\Filament\Resources\StockTransferResource\Pages;

use App\Enums\StockStatuEnum;
use App\Filament\Resources\StockTransferResource;
use App\Models\StockEntry;
use App\Models\StockTransfer;
use App\Services\StockService;
use Filament\Actions;

use Filament\Infolists\Components\Actions as ComponentsActions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;

class ViewStockTransfer extends ViewRecord
{
    protected static string $resource = StockTransferResource::class;

    public function getTitle(): string
    {
        return "Traslado {$this->record->locationTo->name} -> {$this->record->locationFrom->name}";
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $userCanChangeStatus = auth()->user()->can('change_status_stock::entry');
        return $infolist

            ->schema([
                ComponentsActions::make([
                    Action::make('status-accepted')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->visible(fn ($record) => ($record->status == StockStatuEnum::PENDING && $userCanChangeStatus))
                        ->label('Aceptar mercancia')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-check')
                        ->action(function (StockTransfer $record) {
                            $record->status = StockStatuEnum::ACCEPTED;
                            $record->status_at = now();
                            $record->user_approve_id = auth()->id();
                            $record->save();

                            StockService::stockTransfer($record);

                            Notification::make()
                                ->title('La mercancia fue ' . strtolower($record->status->getLabel()))
                                ->success()
                                ->send();
                        }),
                    Action::make('status-rejected')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->visible(fn ($record) => ($record->status == StockStatuEnum::PENDING && $userCanChangeStatus))
                        ->label('Rechazar mercancia')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-x-mark')
                        ->action(function (StockEntry $record) {
                            $record->status = StockStatuEnum::REJECTED;
                            $record->status_at = now();
                            $record->save();
                            Notification::make()
                                ->title('La mercancia fue ' . strtolower($record->status->getLabel()))
                                ->danger()
                                ->send();
                        })

                ])->alignment(Alignment::End),
                Split::make([
                    Section::make([
                        TextEntry::make('locationFrom.nameType')->label('Origen')->columnSpan(2),
                        TextEntry::make('locationTo.nameType')->label('Destino')->columnSpan(2),
                        TextEntry::make('userRequest.name')->label('Quien Solicita'),
                        TextEntry::make('userApprove.name')->label('Quien Aprueba')->default('--'),


                        TextEntry::make('status')->label('Estado')->badge(),
                        ViewEntry::make('products')->columnSpanFull()->view('filament.infolists.stock-transfer-product-list')
                    ])->columns(4),
                    Section::make([
                        TextEntry::make('created_at')->label('Fecha de creacion')->dateTime(),
                        TextEntry::make('status_at')->label('Fecha de cambio de estado')

                            ->dateTime()

                    ])->grow(false),
                ])->from('md'),

            ])
            ->columns(1);
    }
}
