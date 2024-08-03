<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

use function PHPSTORM_META\type;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    public static ?string $label = 'Nomina';
    protected static ?string $pluralModelLabel  = 'Nominas';
    protected static ?string $navigationGroup  = 'Empleados';
    protected static ?string $navigationIcon = 'icon-hand-coins';


    public static function formWorker(): array
    {
        return [
            TextInput::make('id')->label('N° de pago')
                ->visible(fn ($operation) => $operation == 'edit' || $operation == 'view')
                ->disabled(fn ($operation) => $operation == 'edit'),

            DateTimePicker::make('payment_at')->label('Fecha de pago')->columnStart(1)
                ->native(false)->default(now())->seconds(false)->displayFormat('M j, Y h:i a'),
            TextInput::make('amount')->label('Pago')
                ->numeric()
                ->minValue(0)
                ->prefix('$')
                ->required(),
            Textarea::make('note')->label('Obsevacion')->columnSpanFull()
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('worker_id')->label('Trabajador')
                    ->relationship('worker', 'name')
                    ->searchable(['name', 'nit'])
                    ->required(),

                ...self::formWorker()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('N° de pago')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('worker.name')->label('Empleado Nit/Cedula')
                    ->description(fn (Payroll $record) => Number::format($record->worker->nit))
                    ->searchable()->sortable(),
                ...self::tableColumns(),
            ])
            ->filters([
                SelectFilter::make('Empleado')
                    ->relationship('worker', 'name')
                    ->searchable(),
                ...self::tableFilter()
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth(MaxWidth::TwoExtraLarge),
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::TwoExtraLarge),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function tableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('payment_at')->label('Fecha de pago')
                ->formatStateUsing(fn ($state) => $state->isoFormat('MMMM Y'))
                ->searchable()->sortable(),
            Tables\Columns\TextColumn::make('amount')->label('Monto')->money()->sortable(),
            Tables\Columns\TextColumn::make('responsible.name')->label('Responsable')->searchable()
                ->badge()
                ->toggleable(isToggledHiddenByDefault: false),

            Tables\Columns\TextColumn::make('created_at')->label('Fecha del registro')->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            Tables\Columns\TextColumn::make('updated_at')->label('Fecha del actualización')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function tableFilter(): array
    {
        return [
            Filter::make('created_at')
                ->form([
                    DatePicker::make('created_at')
                        ->time(false)->native(false)->label('Fecha de creacion'),

                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_at'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                        );
                }),
            Filter::make('payment_at')
                ->form([
                    TextInput::make('payment_at')->type('month')->label('Mes de pago'),

                ])->query(function (Builder $query, array $data): Builder {

                    return $query
                        ->when(
                            $data['payment_at'],
                            function (Builder $query, $date) {
                                list($year, $month) = explode("-", $date);
                                return  $query->whereYear('payment_at', $year)->whereMonth('payment_at', $month);
                            }


                        );
                })
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayrolls::route('/'),
        ];
    }
}
