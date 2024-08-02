<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Talk;
use Filament\Tables;
use Filament\Forms\Form;
use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Actions;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\TalkResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TalkResource\RelationManagers;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('abstract')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('speaker_id')
                    ->relationship('speaker', 'name')
                    ->required()
                    ->columnSpanFull(),

                Select::make('status')
                    ->enum(enum: TalkStatus::class)
                    ->options(options: TalkStatus::class),
                Select::make('length')
                    ->enum(enum: TalkLength::class)
                    ->options(options: TalkLength::class),
                Toggle::make('new_talk'),

                Actions::make([
                    Action::make('star')
                        ->label('Fill with Factory Data')
                        ->icon('heroicon-m-star')
                        ->visible(function (string $operation) {
                            if ($operation !== 'create') {
                                return false;
                            }

                            if (!app()->environment('local')) {
                                return false;
                            }

                            return true;
                        })
                        ->requiresConfirmation()
                        ->action(function ($livewire) {
                            $data = Talk::factory()->make()->toArray();
                            //unset($data['speaker_id']);
                            $livewire->form->fill($data);
                        }),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->description(function (Talk $record) {
                        return Str::of($record->abstract)->limit(40);
                    })
                    ->tooltip(function (Talk $record) {
                        return $record->abstract;
                    })
                    ->sortable()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('abstract')
                //     ->wrap(),
                ImageColumn::make('speaker.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(function (Talk $record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode($record->speaker->name);
                    }),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('new_talk')->boolean(),
                TextColumn::make('status')->badge()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),

                IconColumn::make('length')
                    ->icon(function ($state) {
                        return $state->getLengthIcon();
                    }),
            ])
            ->filters([
                TernaryFilter::make('new_talk'),
                SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('has_avatar')
                    ->label('Show only Speakers with Avatar')
                    ->toggle()
                    ->query(function ($query) {
                        return $query->whereHas('speaker', function (Builder $query) {
                            $query->whereNotNull('avatar');
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
