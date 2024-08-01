<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Filament\Resources\WorkerResource\RelationManagers;
use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    public static ?string $label = 'Empleado';
    protected static ?string $pluralModelLabel  = 'Empleados';
    protected static ?string $navigationGroup  = 'Empleados';
    protected static ?string $navigationIcon = 'icon-worker';

    public static function form(Form $form): Form
    {
        return $form
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
                Tables\Columns\TextColumn::make('name')->label('Nombre')
                    ->description(fn (Worker $record) => $record->email)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nit')->numeric()->label('Nit/Cedula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')->wrap()->label('Direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de creacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('payrolls')
                    ->url(fn (Worker $record): string => route('filament.admin.resources.payrolls.index'))

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function getRecordSubNavigation(Page $page): array
    // {
    //     return $page->generateNavigationItems([

    //         Pages\ManageWorkerPayrolls::class,
    //     ]);
    // }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWorkers::route('/'),
            'payrolls' => Pages\ManageWorkerPayrolls::route('/{record}/payrolls'),
        ];
    }
}
