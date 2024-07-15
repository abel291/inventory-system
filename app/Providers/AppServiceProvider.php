<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();
        EditAction::configureUsing(function (EditAction $action): void {
            $action->color('info')->icon(false);
        }, isImportant: true);
        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action->icon(false);
        }, isImportant: true);
        Table::configureUsing(function (Table $table): void {
            $table->defaultPaginationPageOption(25)->defaultSort('id', 'desc');
            $table->filtersLayout(FiltersLayout::AboveContent);
        });
        Select::configureUsing(function (Select $component): void {
            $component->native(false);
        });
        SelectFilter::configureUsing(function (SelectFilter $component): void {
            $component->native(false);
        });
    }
}
