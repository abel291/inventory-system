<?php

namespace App\Filament\Resources\SaleResource\RelationManagers;

use App\Enums\SalePaymentTypeEnum;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Livewire\Component;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static ?string $title = 'Pagos';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        // dd(123);
        return $ownerRecord->payment_type == SalePaymentTypeEnum::CREDIT;
    }

    public function isReadOnly(): bool
    {
        return false;
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('payment_method_id')
                    ->label('Metodo de pagos')
                    ->relationship('paymentMethod', 'name')
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->label('Referencia')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->maxValue(function (RelationManager $livewire) {
                        return $livewire->getOwnerRecord()->pendingPayments();
                    })
                    ->validationMessages([
                        'max' => 'El pago no puede suéra el saldo restante.',
                    ])
                    ->helperText(function (RelationManager $livewire) {
                        return "Monto max " . Number::currency($livewire->getOwnerRecord()->pendingPayments());
                    })
                    ->prefix('$')
                    ->hintAction(
                        ActionsAction::make('PagarTodo')
                            ->action(function (Set $set, $state, RelationManager $livewire) {
                                $amount = $livewire->getOwnerRecord()->pendingPayments();
                                $set('amount', $amount);
                            })
                    )
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('note')
                    ->label('Nota')
                    ->maxLength(255),
            ]);
    }


    public function table(Table $table): Table
    {
        return $table

            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('reference')->label('Referencia'),
                Tables\Columns\TextColumn::make('paymentMethod.name')->badge()->label('Metodo de pago'),

                Tables\Columns\TextColumn::make('amount')->money(locale: 'de')->label('Monto')
                    ->summarize(
                        Sum::make()->label('Total abonos')->money(locale: 'de')
                    ),
                Tables\Columns\TextColumn::make('note')->label('Observacion')
                    ->placeholder('- sin observacion'),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de pago')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Crear Abono')
                    ->visible(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->pendingPayments() > 0)
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshViewSale');
                    }),
                // ->authorize(true)
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshViewSale');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshViewSale');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('Agregar abono')
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->paginated(false);
    }
}
