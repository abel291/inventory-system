<?php

namespace App\Filament\Resources;

use App\Enums\LocationTypeEnum;
use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Filament\Resources\LocationResource\RelationManagers\ProductsRelationManager;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static ?string $label = 'Ubicacion';
    protected static ?string $pluralModelLabel  = 'Ubicaciones';
    protected static ?string $navigationGroup  = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('nombre')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->label('Telefono')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->label('Direccion')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Tipo de ubicacion')
                    ->options(LocationTypeEnum::class)
                    ->required(),
                Forms\Components\Toggle::make('active')
                    ->label('Activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->badge()
                    ->label('Tipo de ubicacion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->description(fn (Location $record): string => $record->address)
                    ->wrap()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('phone')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Productos'),

                Tables\Columns\TextColumn::make('products')
                    ->label('Total unidades')
                    ->getStateUsing(function (Location $record) {
                        return $record->products->sum('pivot.stock');
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),


                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de modificacion')
                    ->since()
                    ->sortable()

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('products')
                    ->label('Ver Productos')
                    ->url(fn (Location $record): string => route('filament.admin.resources.locations.products', $record)),

                Tables\Actions\EditAction::make()->color('info')->icon(null),
                Tables\Actions\DeleteAction::make()->icon(null),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->description('El ingreso de mercancia se gestiona desde la seccion "Mercancias"');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
            'products' => Pages\ProductsRelation::route('/{record}/products'),

        ];
    }
}
