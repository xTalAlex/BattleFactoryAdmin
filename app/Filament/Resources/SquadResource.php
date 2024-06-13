<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SquadResource\Pages;
use App\Models\Squad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SquadResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $model = Squad::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
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
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(15),
                                                Forms\Components\TextInput::make('code')
                                                    ->lazy()
                                                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                                        $set(
                                                            'code',
                                                            Str::upper(Str::startsWith($state, '#') ? $state : '#'.$state)
                                                        );
                                                    })
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(9),
                                            ])->columns(['md' => 2]),
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Select::make('rank')
                                                    ->options(config('uniteagency.squad_ranks'))
                                                    ->default(array_key_first(config('uniteagency.squad_ranks')))
                                                    ->selectablePlaceholder(false),
                                                Forms\Components\TextInput::make('active_members')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(1)
                                                    ->maxValue(30),
                                            ])->columns([
                                                'md' => 2,
                                            ]),
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Select::make('country')
                                                    ->searchable()
                                                    ->default('it')
                                                    ->getSearchResultsUsing(
                                                        fn (string $search) => collect(countries())
                                                            ->filter(
                                                                fn ($country) => Str::contains(Str::lower($country['name']), Str::lower($search))
                                                                    || Str::contains(Str::lower($country['iso_3166_1_alpha2']), Str::lower($search))
                                                                    || Str::contains(Str::lower($country['iso_3166_1_alpha3']), Str::lower($search))
                                                            )
                                                            ->mapWithKeys(fn ($item, $key) => [$key => $item['name']])
                                                    )
                                                    ->getOptionLabelUsing(fn ($value): ?string => country($value)->getName()),
                                            ])->columns([
                                                'md' => 2,
                                            ]),
                                        Forms\Components\Toggle::make('requires_approval')
                                            ->columnSpan('full'),
                                        Forms\Components\TextInput::make('link')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description')
                                            ->rows(4)
                                            ->maxLength(500),
                                    ]),
                            ])->columnSpan(['lg' => 2]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->relationship('user', 'name'),
                                        Forms\Components\Toggle::make('verified')
                                            ->default(true),
                                        Forms\Components\Toggle::make('featured'),
                                    ]),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('created_at')
                                            ->content(fn (?Squad $record): string => $record->created_at ?? '-'),
                                        Forms\Components\Placeholder::make('updated_at')
                                            ->content(fn (?Squad $record): string => $record->updated_at ?? '-'),
                                    ]),
                            ])->columnSpan(['lg' => 1]),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->url(
                        fn (Squad $record): string => $record->user ?
                            route('filament.admin.resources.users.edit', $record->user_id)
                            : route('filament.admin.resources.users.index')
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
                Tables\Columns\TextColumn::make('rank')
                    ->badge()
                    ->colors([
                        'primary',
                    ])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(20)
                    ->wrap()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('link')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Squad $record): string => ($record->link ?? ''))
                    ->openUrlInNewTab(),
                Tables\Columns\ViewColumn::make('country')
                    ->view('filament.tables.columns.country-flag')
                    // ->formatStateUsing(
                    //     fn (?string $state): string =>
                    //     $state ? country($state)->getName() : ''
                    // )
                    // ->description(fn (Squad $record): string => $record->country ?? '', position: 'above')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('requires_approval')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('featured')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('verified')
                    ->boolean()
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
            ->defaultSort('created_at', 'desc')
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
                Tables\Filters\SelectFilter::make('rank')
                    ->multiple()
                    ->options(config('uniteagency.squad_ranks'))
                    ->indicateUsing(function (array $data): array {
                        return $data['values'];
                    }),
                Tables\Filters\Filter::make('active_members')
                    ->form([
                        Forms\Components\TextInput::make('active_members_from')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->lazy(),
                        Forms\Components\TextInput::make('active_membres_to')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
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
                            $indicators['active_members_from'] = 'Active Members from '.$data['active_members_from'];
                        }

                        if ($data['active_membres_to'] ?? null) {
                            $indicators['active_membres_to'] = 'Active Members to '.$data['active_membres_to'];
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
                            $indicators['created_from'] = 'Created from '.\Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until '.\Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
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
