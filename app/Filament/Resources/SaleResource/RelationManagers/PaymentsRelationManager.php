<?php

namespace App\Filament\Resources\SaleResource\RelationManagers;

use App\Enums\SalePaymentTypeEnum;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

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
                Tables\Columns\TextColumn::make('amount')->money('USD', locale: 'nl')->label('Monto')
                    ->summarize(
                        Sum::make()->money(locale: 'nl')->label('Total abonos')
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Crear Abono')
                // ->authorize(true)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
        ;
    }
}
