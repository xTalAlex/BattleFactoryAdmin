<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Squad;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SquadsRelationManager extends RelationManager
{
    protected static string $relationship = 'squads';

    protected static ?string $inverseRelationship = 'user';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(['md' => 2])
                    ->columnSpan('full'),
                Forms\Components\RichEditor::make('description')
                    ->disableToolbarButtons([
                        'attachFiles',
                    ])
                    ->maxLength(300)
                    ->columnSpan('full'),
                \Filament\Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('link')
                            ->maxLength(255)
                            ->columnSpan(['md'=>2]),
                        Forms\Components\Select::make('country')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => 
                                collect(countries())
                                    ->filter( fn($country) => 
                                        Str::contains(Str::lower($country['name']), Str::lower($search)) 
                                        || Str::contains(Str::lower($country['iso_3166_1_alpha2']), Str::lower($search))
                                        || Str::contains(Str::lower($country['iso_3166_1_alpha3']), Str::lower($search))
                                    )
                                    ->mapWithKeys( fn($item,$key) => [ $key => $item['name'] ])
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => country($value)->getName()),
                    ])
                    ->columns([
                        'md' => 3
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->url(fn (Squad $record): string =>
                        route('filament.resources.squads.edit', $record )
                    )
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(20)
                    ->wrap()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\BooleanColumn::make('link')
                    ->trueIcon('heroicon-o-external-link')
                    ->url(fn (Squad $record): string => ($record->link ?? ''))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('country')
                    ->formatStateUsing(fn (?string $state): string =>
                        $state ? country($state)->getName() : ''
                    )
                    ->description(fn (Squad $record): string => $record->country ?? '', position: 'above')
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->defaultSort('created_at','desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DissociateBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
