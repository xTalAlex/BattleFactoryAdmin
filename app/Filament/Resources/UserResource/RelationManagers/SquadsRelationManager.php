<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Closure;
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
                                                ->options(config('uniteagency.squad_ranks'))
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
                Tables\Columns\TextColumn::make('name')
                    ->url(fn (Squad $record): string =>
                        route('filament.resources.squads.edit', $record )
                    )
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
                Tables\Filters\Filter::make('featured')
                    ->query(fn (Builder $query): Builder => $query->where('featured', true))
                    ->toggle(), 
                Tables\Filters\Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->where('verified', true))
                    ->toggle(),
                Tables\Filters\Filter::make('requires_approval')
                    ->query(fn (Builder $query): Builder => $query->where('requires_approval', true)),    
                Tables\Filters\Filter::make('link')->label('Has link')
                    ->query(fn (Builder $query): Builder => $query->whereNot('link', null)),    
                Tables\Filters\MultiSelectFilter::make('rank')
                    ->options(config('uniteagency.squad_ranks'))
                    ->indicateUsing(function (array $data): array {
                        return $data['values'];
                    }),
                Tables\Filters\Filter::make('active_members')
                    ->form([
                        Forms\Components\TextInput::make('active_members_from')
                            ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                                ->range()
                                ->from(1)
                                ->to(30)
                                ->maxValue(30)
                            )
                            ->lazy(),
                        Forms\Components\TextInput::make('active_membres_to')
                            ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                                ->range()
                                ->from(1)
                                ->to(30)
                                ->maxValue(30)
                            )
                            ->lazy(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['active_members_from'],
                                fn (Builder $query, $count): Builder => $query->whereDate('active_members', '>=', $count),
                            )
                            ->when(
                                $data['active_membres_to'],
                                fn (Builder $query, $count): Builder => $query->whereDate('active_members', '<=', $count),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                 
                        if ($data['active_members_from'] ?? null) {
                            $indicators['active_members_from'] = 'Active Members from ' . $data['active_members_from'];
                        }
                 
                        if ($data['active_membres_to'] ?? null) {
                            $indicators['active_membres_to'] = 'Active Members to ' . $data['active_membres_to'];
                        }
                 
                        return $indicators;
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                 
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Created from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                 
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                 
                        return $indicators;
                    })
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
