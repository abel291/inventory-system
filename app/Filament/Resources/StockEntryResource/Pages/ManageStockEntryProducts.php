<?php

namespace App\Filament\Resources\StockEntryResource\Pages;

use App\Filament\Resources\StockEntryResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageStockEntryProducts extends ManageRelatedRecords
{
    protected static string $resource = StockEntryResource::class;

    protected static string $relationship = 'products';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // protected static ?string $title = 'Entrada de mercancia - productos';

    public function getTitle(): string
    {
        return 'Entrada de mercancia ' . $this->record->created_at->format('d-m-Y H:m');
    }


    public static function getNavigationLabel(): string
    {
        return 'Products';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('img')->label('Imagen'),
                Tables\Columns\TextColumn::make('barcode')->wrap()->label('Codigo')->searchable(),
                Tables\Columns\TextColumn::make('name')->wrap()->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('pivot.cost')->money('COP', locale: 'ES')->label('Costo'),
                // Tables\Columns\TextColumn::make('price')->money('COP', locale: 'ES')->label('Precio'),
                Tables\Columns\TextColumn::make('pivot.quantity')->label('Cantidad'),

            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([

                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DissociateAction::make(),
                // Tables\Actions\DeleteAction::make(),
                // Tables\Actions\ForceDeleteAction::make(),
                // Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DissociateBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                // SoftDeletingScope::class,
            ]))
        ;
    }
}
