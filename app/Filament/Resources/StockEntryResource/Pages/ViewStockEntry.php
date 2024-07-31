<?php

namespace App\Filament\Resources\StockEntryResource\Pages;

use App\Enums\StockStatuEnum;
use App\Filament\Resources\StockEntryResource;
use App\Models\StockEntry;
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

class ViewStockEntry extends ViewRecord
{
    protected static string $resource = StockEntryResource::class;

    public function getTitle(): string
    {
        return 'Entrada de mercancia ' . $this->record->created_at->format('M j, Y H:i');
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
                        ->visible(fn($record) => ($record->status == StockStatuEnum::PENDING && $userCanChangeStatus))
                        ->label('Aceptar mercancia')
                        ->requiresConfirmation()
                        ->modalIcon('heroicon-o-check')
                        ->action(function (StockEntry $record) {
                            $record->status = StockStatuEnum::ACCEPTED;
                            $record->status_at = now();
                            $record->save();
                            StockService::stockEntryAddition($record);
                            Notification::make()
                                ->title('La mercancia fue ' . strtolower($record->status->getLabel()))
                                ->success()
                                ->send();
                        }),
                    Action::make('status-rejected')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->visible(fn($record) => ($record->status == StockStatuEnum::PENDING && $userCanChangeStatus))
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

                ])->alignment(Alignment::End)->columnSpanFull(),
                Split::make([
                    Section::make([
                        TextEntry::make('user.name')->label('Responsable'),
                        TextEntry::make('location.nameType')->label('Ubicacion')->columnSpan(2),
                        TextEntry::make('status')->label('Estado')->badge(),
                        // TextEntry::make('status_at')->label('Fecha de cambio de estado')->dateTime()
                        ViewEntry::make('products')->columnSpanFull()->view('filament.infolists.stock-entry-product-list')
                    ])->columns(4),

                    Section::make([
                        TextEntry::make('created_at')->label('Fecha de creacion')->dateTime(),
                        // TextEntry::make('status_at')->label('Fecha de cambio de estado')->dateTime()

                    ])->grow(false),
                ])->from('md'),
            ])
            ->columns(1);
    }
}
