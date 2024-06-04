<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => $record->email
        ];
    }

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
                                        Forms\Components\TextInput::make('name')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->same('password_confirmation')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('password_confirmation')
                                            ->password()
                                            ->dehydrated(false)
                                            ->maxLength(255),
                                    ]),

                            ])
                            ->columnSpan(['lg' => 2]),
                        \Filament\Forms\Components\Group::make()
                            ->schema([
                                \Filament\Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('Avatar')
                                            ->content(fn (User $record): HtmlString => new HtmlString('<a href="' . ($record->profile_photo_url) . '"><img style="border-radius: 9999px; margin: auto; width: 7rem;" src="' . ($record->profile_photo_url) . '"/></a>')),
                                    ])->visibleOn('edit'),
                                \Filament\Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_admin'),
                                    ]),
                                \Filament\Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('email_verified_at')
                                            ->content(fn (?User $record): string => $record->email_verified_at ?? '-'),
                                        Forms\Components\Placeholder::make('created_at')
                                            ->content(fn (?User $record): string => $record->created_at ?? '-'),
                                        Forms\Components\Placeholder::make('updated_at')
                                            ->content(fn (?User $record): string => $record->updated_at ?? '-'),
                                    ])
                            ])->columnSpan(['lg' => 1]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo_url')
                    ->label('Avatar')
                    ->circular()
                    ->size(52),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('email_verified_at')
                    ->colors([
                        'success' => fn ($state): bool => $state !== null,
                    ])
                    ->dateTime()
                    ->toggleable()
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('is_admin')->label('Admin')
                    ->query(fn (Builder $query): Builder => $query->where('is_admin', true))
                    ->toggle(),
                Tables\Filters\Filter::make('email_verified_at')->label('Email verified')
                    ->query(fn (Builder $query): Builder => $query->where('email_verified_at', null))
                    ->toggle(),
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
            RelationManagers\SquadsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
