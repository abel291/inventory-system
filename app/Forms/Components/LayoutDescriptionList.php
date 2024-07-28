<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Component;

class LayoutDescriptionList extends Component
{
    protected string $view = 'forms.components.layout-description-list';

    public static function make(): static
    {
        return app(static::class);
    }
}
