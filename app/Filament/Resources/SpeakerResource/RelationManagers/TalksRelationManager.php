<?php

namespace App\Filament\Resources\SpeakerResource\RelationManagers;

use Filament\Forms;
use App\Models\Talk;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class TalksRelationManager extends RelationManager
{
    protected static string $relationship = 'talks';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(Talk::getMiniForm($this->getOwnerRecord()->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
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

                Tables\Columns\IconColumn::make('new_talk')->boolean(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(function ($state) {
                        return $state->getColor();
                    }),

                Tables\Columns\IconColumn::make('length')
                    ->icon(function ($state) {
                        return $state->getLengthIcon();
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
