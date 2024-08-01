<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use App\Models\Payroll;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageWorkerPayrolls extends ManageRelatedRecords
{
    protected static string $resource = WorkerResource::class;

    protected static string $relationship = 'payrolls';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Payrolls';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('N° de pago')->searchable(),
                Tables\Columns\TextColumn::make('responsible.name')->label('Empleado')->searchable()->sortable(),
                // Tables\Columns\TextColumn::make('worker.name')->label('Responsable')->searchable()->sortable(),
                // Tables\Columns\TextColumn::make('worker.nit')->label('Nit/Cedula')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('payment_at')->label('Fecha de pago')
                    ->description(fn (Payroll $record) => $record->payment_at->diffForHumans())
                    ->searchable()->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Monto')->money(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha creacion del registro')->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DissociateAction::make(),
                // Tables\Actions\DeleteAction::make(),
                // Tables\Actions\ForceDeleteAction::make(),
                // Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DissociateBulkAction::make(),
                //     Tables\Actions\DeleteBulkAction::make(),
                //     Tables\Actions\RestoreBulkAction::make(),
                //     Tables\Actions\ForceDeleteBulkAction::make(),
                // ]),
            ]);
        // ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
        //     SoftDeletingScope::class,
        // ]));
    }
}
