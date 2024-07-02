<?php

namespace App\Filament\Resources;

use App\Enums\ContactTypesEnum;
use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers;
use App\Models\Contact;
use Contact\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProviderResource extends Resource
{
    protected static ?string $model = Contact::class;
    protected static ?string $slug = 'providers';
    public static ?string $label = 'Proveedor';
    protected static ?string $pluralModelLabel  = 'Proveedores';
    protected static ?string $navigationGroup  = 'Contactos';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('nit')
                    ->label('NIF,CIF')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Correo electronico')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefono')
                    ->tel(),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->label('Direccion'),

                Forms\Components\Select::make('type')
                    ->label('Tipo de contacto')
                    ->options(ContactTypesEnum::class),

                Forms\Components\TextInput::make('note')
                    ->label('Nota')->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->description(fn (Contact $record): string => $record->email)
                    ->searchable(),
                // Tables\Columns\TextColumn::make('nit')
                //     ->label('identificacion')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modificado')
                    ->since(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->icon(false),
                Tables\Actions\DeleteAction::make()->icon(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(fn (Builder $query) => $query->whereIn('type', [ContactTypesEnum::PROVIDER, ContactTypesEnum::CLIENT_PROVIDER]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProviders::route('/'),
        ];
    }
}
