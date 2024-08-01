<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    public static ?string $label = 'Nomina';
    protected static ?string $pluralModelLabel  = 'Nominas';
    protected static ?string $navigationGroup  = 'Empleados';
    protected static ?string $navigationIcon = 'icon-hand-coins';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('note')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('amount')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('payment_at')
                    ->required(),
                Forms\Components\Select::make('user_id')->relationship('user', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('N° de pago')->searchable(),
                Tables\Columns\TextColumn::make('responsible.name')->label('Responsable')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('worker.name')->label('Empleado- Nit/Cedula')
                    ->description(fn (Payroll $record) => Number::format($record->worker->nit))
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('payment_at')->label('Fecha de pago')
                    ->description(fn (Payroll $record) => $record->payment_at->diffForHumans())
                    ->searchable()->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Monto')->money(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha del registro')->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')->label('Fecha del actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Empleado')
                    ->relationship('worker', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayrolls::route('/'),
        ];
    }
}
