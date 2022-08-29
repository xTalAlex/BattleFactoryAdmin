<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Squad;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SquadResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SquadResource\RelationManagers;

class SquadResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $model = Squad::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code'];
    }

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Code' => $record->code,
            'User' => $record->user ? $record->user->name : '-',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                    'md' => 2,
                    'lg' => 3,
                ])
                ->schema([
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Group::make()
                                        ->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->maxLength(15),
                                            Forms\Components\TextInput::make('code')
                                                ->mask(fn (Forms\Components\TextInput\Mask $mask) => 
                                                    $mask->pattern('{#}********')
                                                )
                                                ->lazy()
                                                ->afterStateUpdated(function (Closure $set, $state) {
                                                    $set('code', strtoupper($state));
                                                })
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->maxLength(9),
                                        ])->columns(['md' => 2]),
                                    Forms\Components\Group::make()
                                        ->schema([
                                            Forms\Components\Select::make('rank')
                                                ->options(config('battlefactory.squad_ranks'))
                                                ->disablePlaceholderSelection(),
                                            Forms\Components\TextInput::make('active_members')
                                                ->numeric()
                                                ->default(1)
                                                ->minValue(1)
                                                ->maxValue(30),
                                        ])->columns([
                                            'md' => 2
                                        ]),  
                                    Forms\Components\Group::make()
                                        ->schema([
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
                                            'md' => 2
                                        ]), 
                                    Forms\Components\Toggle::make('requires_approval')
                                        ->columnSpan('full'),                                                                              
                                    Forms\Components\TextInput::make('link')
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('description')
                                        ->rows(4)
                                        ->maxLength(500),
                                ])
                        ])->columnSpan(['lg' => 2]),
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Select::make('user_id')
                                        ->relationship('user','name'),
                                    Forms\Components\Toggle::make('verified'),
                                    Forms\Components\Toggle::make('featured'),
                                ]),  
                            Forms\Components\Card::make()
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
                Tables\Columns\TextColumn::make('active_members')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('rank')
                    ->colors([
                        'primary',
                    ])
                    ->toggleable(),
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BooleanColumn::make('requires_approval')
                    ->toggleable(),
                Tables\Columns\BooleanColumn::make('featured')
                    ->toggleable(),
                Tables\Columns\BooleanColumn::make('verified')
                    ->toggleable(),
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
