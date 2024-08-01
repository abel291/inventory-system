<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use App\Models\Payroll;
use DateTime;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component as Livewire;

class ManageWorkerPayrolls extends ManageRelatedRecords
{
    protected static string $resource = WorkerResource::class;

    protected static string $relationship = 'payrolls';

    public function getTitle(): string
    {
        return "Nomina de " . $this->record->name;
    }


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Nomina';
    }

    public function form(Form $form): Form
    {
        return $form

            ->schema([
                Grid::make(3)
                    ->columnSpanFull()->schema([
                            Placeholder::make('worker.name')->label('Nombre')->content(fn() => $this->getOwnerRecord()->name),
                            Placeholder::make('worker.phone')->label('Telefono')->content(fn() => $this->getOwnerRecord()->phone),
                            Placeholder::make('worker.email')->label('Email')->content(fn() => $this->getOwnerRecord()->email),
                        ]),

                TextInput::make('id')->label('N° de pago')
                    ->visible(fn($operation) => $operation == 'view')
                    ->disabled(),
                TextInput::make('amount')->label('Pago')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$')
                    ->required(),
                DateTimePicker::make('payment_at')->label('Fecha de pago')

                    ->native(false)->default(now())->seconds(false)->displayFormat('M j, Y h:i a'),
                Textarea::make('note')->label('Obsevacion')->columnSpanFull()

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('payment_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('N° de pago')->searchable(),

                Tables\Columns\TextColumn::make('worker.name')->label('Nombre')->badge(),
                Tables\Columns\TextColumn::make('amount')->label('Monto')->money(),

                Tables\Columns\TextColumn::make('payment_at')->label('Fecha de pago')
                    ->description(fn(Payroll $record) => $record->payment_at->diffForHumans())
                    ->searchable()->dateTime()->sortable(),

                Tables\Columns\TextColumn::make('responsible.name')->label('Responsable')->searchable()->sortable(),

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
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Crear Pago')
                    ->label(fn(Livewire $livewire) => 'Crear Pago a ' . $this->getOwnerRecord()->name)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
                // Tables\Actions\AssociateAction::make(),
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
