<?php

namespace App\Filament\Resources;

use App\Enums\ContactTypesEnum;
use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\Pages\ViewSale;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Filament\Resources\SaleResource\Section\SaleFormSection;
use App\Filament\Resources\SaleResource\Section\SectionForm;
use App\Models\Contact;
use App\Models\Location;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Services\SaleService;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Pest\Laravel\options;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static ?string $label = 'Venta';

    protected static ?string $pluralModelLabel = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Informacion de la venta')
                    ->columns(4)
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('code')->label('Codigo')
                            ->default(SaleService::generateCode())
                            ->disabled()
                            ->dehydrated(),
                        Select::make('contact_id')
                            ->label('Cliente')
                            ->searchable(['name', 'email', 'nit', 'phone'])
                            ->getOptionLabelFromRecordUsing(fn (Contact $record) => "{$record->name} - nit {$record->nit}")
                            ->relationship(
                                'client',
                                'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereIn('type', [
                                    ContactTypesEnum::CLIENT, ContactTypesEnum::CLIENT_PROVIDER
                                ])->orderBy('id', 'desc'),
                            )
                            ->default(fn () => Contact::where('nit', '222222222')->first()->id)
                            ->preload(),

                        DateTimePicker::make('created_at')->disabled()->native(false)->default(now())->secondsStep(false)->label('Fecha'),

                        Select::make('location_id')->label('Ubicacion')
                            ->disabled(fn (Get $get) => ($get('saleProducts')))
                            ->live()
                            ->relationship('location', 'name', fn ($query) => ($query->active()))
                            ->getOptionLabelFromRecordUsing(fn (Location $record) => "{$record->nameType}")
                            ->default(1)
                            ->selectablePlaceholder(false)
                            ->preload()
                            ->afterStateUpdated(function (Set $set) {
                                $set('saleProducts', []);
                            })
                            ->required(),

                    ]),

                Section::make('Productos')
                    ->columns(3)
                    ->icon('heroicon-o-shopping-cart')
                    ->schema(
                        SaleFormSection::products()
                    ),

                Section::make('Resumen')
                    ->columnStart(3)
                    ->columnSpan(1)
                    ->columns(1)
                    ->icon('heroicon-o-currency-dollar')
                    ->live(debounce: 500)
                    ->schema([
                        TextInput::make('sub_total')->label('Sub total')
                            ->disabled()
                            ->dehydrated()
                            ->stripCharacters('.')
                            ->prefix('$'),

                        TextInput::make('delivery')->label('Envio')
                            ->default(0)
                            ->minValue(0)
                            ->numeric()
                            ->afterStateUpdated(fn (Get $get, Set $set) => (SaleFormSection::updateTotals($get, $set)))
                            ->prefix('$')
                            ->debounce(500),

                        TextInput::make('total')->label('Total')
                            ->disabled()
                            ->dehydrated()
                            ->stripCharacters('.')
                            ->prefix('$'),
                    ]),
                // Section::make('Descuentos')

            ]);
    }
    public static function formSectionTotal()
    {
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Numero'),
                TextColumn::make('client.name'),
                TextColumn::make('sale_products_count')->label('Productos')
                    ->counts('saleProducts'),
                TextColumn::make('delivery')->label('Costo de envio')->numeric()->prefix('$'),
                TextColumn::make('total')->label('Precio Total')->numeric()->prefix('$'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->label('Fecha de la venta')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make()->icon(null),
                Tables\Actions\ViewAction::make()->icon(null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'view' => ViewSale::route('/{record}'),
            // 'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
