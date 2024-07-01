<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    public static ?string $label = 'Usuario';
    protected static ?string $pluralModelLabel  = 'Usuarios';
    protected static ?string $navigationGroup  = 'Usuarios';

    protected static ?string $navigationIcon = 'heroicon-s-user';

    protected static ?int $navigationSort = -2;

    public static function mutateDataPassword(array $data): array
    {
        if (array_key_exists('password', $data) || filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        unset($data["password_confirmation"]);
        return $data;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->maxLength(255),

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Role $role) => __("roles.$role->name"))
                    ->preload()
                    ->required()
                    ->multiple()
                    ->searchable(),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->nullable()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->rule(Password::default()),

                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->same('password')
                    ->requiredWith('password')
                    ->rule(Password::default()),



            ])->columns(1)
            // ->modalWidth(MaxWidth::Small)
        ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('phone')->label('Telefono'),
                Tables\Columns\TextColumn::make('roles.name')->label('Rol')->badge()->placeholder('Sin Role')
                    ->formatStateUsing(fn (string $state): string   => __("roles.$state")),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modificado')
                    ->since(),
            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon(false)
                    ->color('primary')
                    ->modalWidth(MaxWidth::Medium)
                    ->mutateFormDataUsing(function (array $data): array {

                        return self::mutateDataPassword($data);
                    }),
                Tables\Actions\DeleteAction::make()->icon(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
