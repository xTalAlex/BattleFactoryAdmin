<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Squad;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SquadResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SquadResource\RelationManagers;

class SquadResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $model = Squad::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                    'lg' => 3,
                ])
                ->schema([
                    \Filament\Forms\Components\Group::make()
                        ->schema([
                            \Filament\Forms\Components\Card::make()
                                ->schema([
                                    \Filament\Forms\Components\Group::make()
                                        ->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('code')
                                                ->required()
                                                ->maxLength(255),
                                        ])->columns(['md' => 2]),
                                    Forms\Components\RichEditor::make('description')
                                        ->disableToolbarButtons([
                                            'attachFiles',
                                        ])
                                        ->maxLength(300),
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
                                        ])->columns([
                                            'md' => 3
                                        ]),

                                ])
                        ])->columnSpan(['lg' => 2]),
                    \Filament\Forms\Components\Group::make()
                        ->schema([
                            \Filament\Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Select::make('user_id')
                                        ->relationship('user','name'),
                                ]),  
                            \Filament\Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Placeholder::make('created_at')
                                        ->content(fn (?Squad $record): string => $record->created_at ?? '-' ),
                                    Forms\Components\Placeholder::make('updated_at')
                                        ->content(fn (?Squad $record): string => $record->updated_at ?? '-' ),
                                ])   
                        ])->columnSpan(['lg' => 1]),
                
                ])
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->url(fn (Squad $record): string => 
                        $record->user ?
                            route('filament.resources.users.edit', $record->user_id )
                            : route('filament.resources.users.index')
                    )
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSquads::route('/'),
            'create' => Pages\CreateSquad::route('/create'),
            'edit' => Pages\EditSquad::route('/{record}/edit'),
        ];
    }    
}
