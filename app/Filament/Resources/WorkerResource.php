<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Filament\Resources\WorkerResource\Pages\ManageWorkerPayrolls;
use App\Filament\Resources\WorkerResource\RelationManagers;
use App\Filament\Resources\WorkerResource\RelationManagers\PayrollsRelationManager;
use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    public static ?string $label = 'Empleado';
    protected static ?string $pluralModelLabel = 'Empleados';
    protected static ?string $navigationGroup = 'Empleados';
    protected static ?string $navigationIcon = 'icon-worker';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                TextInput::make('name')->label('Nombre')->required(),
                TextInput::make('nit')->label('Nit/Cedula')->prefixIcon('heroicon-s-identification')->required(),
                TextInput::make('email')->prefixIcon('heroicon-s-envelope')->email()->required(),
                TextInput::make('phone')->label('Telefono')->prefixIcon('heroicon-s-phone')->tel(),
                TextInput::make('address')->label('Direccion')->columnSpanFull()->required(),
                TextInput::make('description')->label('Pequeña descripción')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->description(fn (Worker $record) => $record->email)->searchable(),
                Tables\Columns\TextColumn::make('nit')->numeric()->label('Nit/Cedula')->searchable(),
                Tables\Columns\TextColumn::make('address')->wrap()->label('Direccion')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')->label('Telefono')->searchable(),
                Tables\Columns\TextColumn::make('payroll_last.amount')->label('Ultima nomina')->money()
                    ->description(function ($record) {

                        return  $record->payroll_last ? $record->payroll_last->payment_at->isoFormat('MMMM Y') : null;
                    })->placeholder('No tiene nomina')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de creacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')->label('Fecha de modificacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()->label('Ver nomina'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            PayrollsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkers::route('/'),
            // 'create' => Pages\CreateWorker::route('/create'),
            'view' => Pages\ViewWorker::route('/{record}'),
            // 'payrolls' => Pages\ManageWorkerPayrolls::route('/{record}/payrolls'),
            // 'edit' => Pages\EditWorker::route('/{record}/edit'),
        ];
    }
    public function viewAny(): bool
    {
        return false;
    }
}
