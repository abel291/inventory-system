<?php

namespace App\Providers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        EditAction::configureUsing(function (EditAction $action): void {
            $action->color('info')->icon(false);
        });
        CreateAction::configureUsing(function (CreateAction $action): void {
            $action->icon(false);
        });
        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action->icon(false);
        });
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
