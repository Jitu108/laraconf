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
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\TalkResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TalkResource\RelationManagers;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Second Group';


    public static function form(Form $form): Form
    {
        return $form
            ->schema(Talk::getForm());
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
                Tables\Actions\EditAction::make()
                    ->slideOver(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->disabled(function (Talk $record) {
                            return $record->canApproveOrReject() == false;
                        })
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action((function (Talk $record) {
                            $record->approve();
                        }))
                        ->after(function () {
                            Notification::make()->success()->title('Talk was approved')
                                ->body('The speaker has been notified and the talk has been added to the conference schedule')
                                ->send();
                        }),

                    Tables\Actions\Action::make('reject')
                        ->requiresConfirmation()
                        ->disabled(function (Talk $record) {
                            return $record->canApproveOrReject() == false;
                        })
                        ->icon('heroicon-o-no-symbol')
                        ->color('warning')
                        ->action((function (Talk $record) {
                            $record->reject();
                        }))
                        ->after(function () {
                            Notification::make()->danger()->title('Talk was rejected')
                                ->body('The speaker has been notified that the talk was rejected')
                                ->send();
                        }),

                    // Tables\Actions\Action::make('revertToSubmitted')
                    //     ->action(function (Talk $record) {
                    //         $record->revertToSubmitted();
                    //     })
                ])


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each->approve();
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->icon('heroicon-o-check-circle')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each->reject();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),


                ]),


            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->tooltip('This will export all records visiblein the table. Adjust filters to export a subset of recrods.')
                    ->action(function ($livewire) {
                        $livewire->getFilteredTableQuery();
                    })
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
            //'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
