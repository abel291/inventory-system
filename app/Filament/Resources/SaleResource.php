<?php

namespace App\Filament\Resources;

use App\Enums\ContactTypesEnum;
use App\Enums\SalePaymentTypeEnum;
use App\Filament\Resources\SaleResource\Form\SaleFormClient;
use App\Filament\Resources\SaleResource\Form\SaleFormDiscount;
use App\Filament\Resources\SaleResource\Form\SaleFormItemProduct;
use App\Filament\Resources\SaleResource\Form\SaleFormPayment;
use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\Pages\ViewSale;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Filament\Resources\SaleResource\RelationManagers\PaymentsRelationManager;
use App\Forms\Components\ItemDescriptionList;
use App\Forms\Components\LayoutDescriptionList;
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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

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
                        ...SaleFormClient::form(),
                    ]),
                Section::make('Productos')
                    ->columns(4)
                    ->icon('heroicon-o-shopping-cart')
                    ->schema(SaleFormItemProduct::products()),

                Section::make('Descuentos y envio')
                    ->columns(6)
                    ->icon('heroicon-o-receipt-percent')
                    ->schema([...SaleFormDiscount::form()]),
                Section::make('Resumen')

                    ->columnStart(3)
                    ->schema([
                        ...self::formSectionTotal(),
                    ]),
                Section::make('Pago')
                    ->columns(3)
                    ->columnSpanFull()
                    ->icon('heroicon-o-credit-card')
                    ->schema(SaleFormPayment::form()),
                Hidden::make('subtotal')->disabled()->default(0),
                Hidden::make('discount.amount')->disabled(),
                Hidden::make('total')->disabled()->default(0),


            ]);
    }
    public static function formSectionTotal()
    {
        return [

            Grid::make(1)
                ->extraAttributes(['class' => 'label-total'])
                ->columnSpanFull()
                ->schema([
                    Placeholder::make('label-sub-total')->label('sub total')->content(fn (Get $get) => "$ " . Number::format($get('subtotal'))),
                    Placeholder::make('labe-discount.amount')->label(fn (Get $get) => "Descuento ({$get('discount.percent')}%)")
                        ->default(0)
                        ->visible(fn (Get $get) => $get('discount.percent'))
                        ->content(fn (Get $get) => "-$ " . Number::format($get('discount.amount'))),
                    Placeholder::make('label-delivery')->label('Envio')
                        ->default(0)
                        ->content(fn (Get $get) => "$ " . Number::format($get('delivery'))),
                    Placeholder::make('label-total')->label('Total')->content(fn (Get $get) => "$ " . Number::format($get('total'))),
                ])
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Numero')->searchable(),
                TextColumn::make('client.name')->searchable(),
                TextColumn::make('sale_products_count')->label('Productos')->counts('saleProducts'),
                TextColumn::make('delivery')->label('Costo de envio')->numeric()->money(locale: 'de'),
                TextColumn::make('total')->label('Precio Total')->numeric()->money(locale: 'de'),
                TextColumn::make('status')->badge(),
                TextColumn::make('payment_type')->label('Tipo de pago')->badge(),
                TextColumn::make('created_at')->label('Fecha de la venta')->dateTime(),

            ])
            ->filters([
                SelectFilter::make('payment_type')->label('Tipo de pago')->options(SalePaymentTypeEnum::class),


            ])
            ->actions([
                // Tables\Actions\EditAction::make()->icon(null),
                Tables\Actions\ViewAction::make()->icon(null)->label('Ver venta'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filtros'),
            );;
    }

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
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
