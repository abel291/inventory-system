<?php

namespace App\Providers;

use App\Listeners\DisableForeignKeyMigrations;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Number::useLocale('de');

        Event::listen(
            DisableForeignKeyMigrations::class,
        );

        Model::unguard();

        EditAction::configureUsing(function (EditAction $action): void {
            $action->icon(false);
        }, isImportant: true);

        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action->icon(false);
        }, isImportant: true);
        ViewAction::configureUsing(function (ViewAction $action,): void {
            $action->icon(false)->label('Ver');
        }, isImportant: true);

        // TextEntry::configureUsing(function (TextEntry $entry): void {
        //     $entry->defaulT;
        // });

        Table::configureUsing(function (Table $table): void {
            $table->defaultPaginationPageOption(10)->defaultSort('id', 'desc');
            // $table->filtersLayout(FiltersLayout::AboveContent);
            $table->searchDebounce('400ms');
        });

        Infolist::$defaultDateTimeDisplayFormat = 'M j, Y h:i a';

        Table::$defaultDateTimeDisplayFormat = 'M j, Y h:i a';
        DateTimePicker::$defaultDateTimeDisplayFormat = 'M j, Y h:i a';

        Select::configureUsing(function (Select $component): void {
            $component->native(false);
        });

        SelectFilter::configureUsing(function (SelectFilter $component): void {
            $component->native(false);
        });
    }
}
