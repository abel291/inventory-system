<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ExpenseExporter;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Filament\Resources\ProductResource\Widgets\ExpenseStats;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    public static ?string $label = 'Gasto';
    protected static ?string $pluralModelLabel = 'Gastos';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reason')->label('Motivo')->required(),
                Forms\Components\TextInput::make('amount')->label('Monto')->required()->numeric()->minValue(1),
                Forms\Components\TextInput::make('note')->label('Observacion'),
                Forms\Components\DatePicker::make('date')->label('Fecha')->required(),

                Forms\Components\Select::make('expenseType.name')->label('Tipo de gasto')
                    ->relationship('expenseType', 'name')
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->label('Nombre')
                            ->required(),
                    ])->columnSpanFull()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reason')->label('Motivo')->wrap()->searchable(),
                Tables\Columns\TextColumn::make('expenseType.name')->label('Tipo de gasto')->sortable(),
                Tables\Columns\TextColumn::make('amount')->label('Monto')->money()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de registro')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('expenseType')
                    ->label('Tipo de gasto')
                    ->relationship('expenseType', 'name')->preload(),
                SaleResource::filtersDate()
            ], layout: FiltersLayout::Dropdown)
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::TwoExtraLarge),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ExpenseExporter::class)
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getWidgets(): array
    {
        return [
            ExpenseStats::class,
        ];
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
            'index' => Pages\ListExpenses::route('/'),
            // 'create' => Pages\CreateExpense::route('/create'),
            // 'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
