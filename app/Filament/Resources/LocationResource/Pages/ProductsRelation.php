<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelation extends ManageRelatedRecords
{
    protected static string $resource = LocationResource::class;

    protected static string $relationship = 'products';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public function getTitle(): string
    {
        return 'Bodega ' . $this->record->name;
    }

    public static function getNavigationLabel(): string
    {
        return 'Products';
    }

    public function table(Table $table): Table
    {
        return $table

            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('img'),
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Codigo de barra')->prefix('#'),
                Tables\Columns\TextColumn::make('name')->label('Nombre')->wrap()
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('COP', locale: 'ES')->label('Precio'),
                Tables\Columns\TextColumn::make('pivot.stock')->label('Cantidad existente'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\AttachAction::make()
                //     ->label('Agregar producto')
                //     ->recordSelectSearchColumns(['name', 'barcode'])
                //     ->form(fn (AttachAction $action): array => [
                //         $action->getRecordSelect(),
                //         Forms\Components\TextInput::make('stock')->required(),
                //         Forms\Components\TextInput::make('security_stock')->required(),
                //     ]),
            ])
            ->actions([
                // Tables\Actions\EditAction::make()->icon(null),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->heading('Productos')
            ->description('El ingreso de mercancia se gestiona desde la seccion "Mercancias"')
            ->emptyStateDescription('El ingreso de mercancia se gestiona desde la seccion "Mercancias"')
            ->searchPlaceholder('Codigo de barra o nombre del producto')
            ->defaultSort('id', 'desc');;
    }
}
