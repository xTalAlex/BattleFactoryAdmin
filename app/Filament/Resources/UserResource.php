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
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

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
                                        ->required()
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
                                        ->required(fn (string $context): bool => $context === 'create')
                                        ->same('password_confirmation')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('password_confirmation')
                                        ->password()
                                        ->dehydrated(false)
                                        ->required(fn (string $context): bool => $context === 'create') 
                                        ->maxLength(255),
                                ]),
                                
                        ])
                        ->columnSpan(['lg' => 2]),
                    \Filament\Forms\Components\Group::make()
                        ->schema([
                            \Filament\Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Placeholder::make('Avatar')
                                        ->content(fn (User $record): HtmlString => new HtmlString('<a href="'.($record->profile_photo_url).'"><img style="border-radius: 9999px; margin: auto; width: 7rem;" src="'.($record->profile_photo_url).'"/></a>')),
                                ])->visibleOn('edit'),
                            \Filament\Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Toggle::make('is_admin'),
                                ]),
                            \Filament\Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Placeholder::make('email_verified_at')
                                        ->content(fn (?User $record): string => $record->email_verified_at ?? '-' ),
                                    Forms\Components\Placeholder::make('created_at')
                                        ->content(fn (?User $record): string => $record->created_at ?? '-' ),
                                    Forms\Components\Placeholder::make('updated_at')
                                        ->content(fn (?User $record): string => $record->updated_at ?? '-' ),
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
                    ->rounded()
                    ->size(52),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('is_admin')
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
