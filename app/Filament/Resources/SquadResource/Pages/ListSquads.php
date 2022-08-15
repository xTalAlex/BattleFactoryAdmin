<?php

namespace App\Filament\Resources\SquadResource\Pages;

use App\Filament\Resources\SquadResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSquads extends ListRecords
{
    protected static string $resource = SquadResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
