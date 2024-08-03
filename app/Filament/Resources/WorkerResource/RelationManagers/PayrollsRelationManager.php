<?php

namespace App\Filament\Resources\WorkerResource\RelationManagers;

use App\Filament\Resources\PayrollResource;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollsRelationManager extends RelationManager
{
    protected static string $relationship = 'payrolls';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form

            ->schema([
                Grid::make(3)
                    ->columnSpanFull()->schema([
                        Placeholder::make('worker.name')->label('Nombre')->content(fn () => $this->getOwnerRecord()->name),
                        Placeholder::make('worker.phone')->label('Telefono')->content(fn () => $this->getOwnerRecord()->phone),
                        Placeholder::make('worker.email')->label('Email')->content(fn () => $this->getOwnerRecord()->email),
                    ]),
                ...PayrollResource::formWorker(),



            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Nominas')
            ->recordTitleAttribute('id')
            ->defaultSort('payment_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('N° de pago')->searchable()->sortable(),
                ...PayrollResource::tableColumns()
            ])
            ->filters([
                ...PayrollResource::tableFilter()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth(MaxWidth::TwoExtraLarge)
                    ->modalHeading('Crear Pago')
                    ->label(fn () => 'Crear Pago')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth(MaxWidth::TwoExtraLarge)->modalHeading('Ver Pago'),
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::TwoExtraLarge)->modalHeading('Editar Pago'),
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
