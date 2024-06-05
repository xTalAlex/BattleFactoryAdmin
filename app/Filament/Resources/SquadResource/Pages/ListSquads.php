<?php

namespace App\Filament\Resources\SquadResource\Pages;

use Filament\Actions;
use App\Filament\Resources\SquadResource;
use Filament\Resources\Pages\ListRecords;

class ListSquads extends ListRecords
{
    protected static string $resource = SquadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
