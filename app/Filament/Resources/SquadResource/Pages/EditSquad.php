<?php

namespace App\Filament\Resources\SquadResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SquadResource;

class EditSquad extends EditRecord
{
    protected static string $resource = SquadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
