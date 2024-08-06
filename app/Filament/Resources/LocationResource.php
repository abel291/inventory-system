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
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static ?string $label = 'Ubicacion';
    protected static ?string $pluralModelLabel = 'Ubicaciones';
    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
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
                    ->translateLabel()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('nameType')
                    ->translateLabel()
                    ->description(fn (Location $record): string => $record->address)
                    ->wrap()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('phone')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->numeric()
                    ->label('Productos'),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Total unidades')
                    ->numeric()
                    ->getStateUsing(function (Location $record) {
                        return $record->stock->sum('quantity');
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->translateLabel()
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de modificacion')
                    ->since()
                    ->sortable()

            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('stock', 'products');
                // ->where('author_id', auth()->id());
            })
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\Action::make('products')
                //     ->label('Ver Productos')
                //     ->url(fn (Location $record): string => route('filament.admin.resources.locations.products', $record)),

                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::ExtraLarge),
                Tables\Actions\DeleteAction::make()->icon(null),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
            // 'create' => Pages\CreateLocation::route('/create'),
            // 'edit' => Pages\EditLocation::route('/{record}/edit'),
            // 'products' => Pages\LocationProductsRelation::route('/{record}/products'),
            'products' => Pages\ManageLocationProducts::route('/{record}/products'),


        ];
    }
}
