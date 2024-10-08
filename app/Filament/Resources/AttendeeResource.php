<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Attendee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\AttendeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AttendeeResource\RelationManagers;
use App\Filament\Resources\AttendeeResource\Widgets\AttendeeChartWidget;
use App\Filament\Resources\AttendeeResource\Widgets\AttendeesStatsWidget;
use Awcodes\Shout\Components\Shout;
use Illuminate\Database\Eloquent\Model;

class AttendeeResource extends Resource
{
    protected static ?string $model = Attendee::class;

    // protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'First Group';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return Attendee::count();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Conference' => $record->conference->name
        ];
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Shout::make('warn-price')
                    ->type('warning')
                    ->visible(function (Forms\Get $get) {
                        return $get('ticket_cost') > 100;
                    })
                    ->columnSpanFull()
                    ->content(function (Forms\Get $get) {
                        $price = $get('ticket_cost');
                        return 'This is $' . $price - 500 . ' more than the average ticket price.';
                    }),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ticket_cost')
                    //->live()
                    ->lazy()
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_paid')
                    ->required(),
                Forms\Components\TextInput::make('conference_id')
                    ->required()
                    ->numeric(),

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
                        ->action(function ($livewire) {
                            $data = Attendee::factory()->make()->toArray();
                            $data['conference_id'] = 1;
                            $livewire->form->fill($data);
                        }),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ticket_cost')
                    ->numeric()
                    ->prefix('$')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('conference_id')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->hidden()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->hidden()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getWidgets(): array
    {
        return [
            AttendeesStatsWidget::class,
            AttendeeChartWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendees::route('/'),
            'create' => Pages\CreateAttendee::route('/create'),
            'edit' => Pages\EditAttendee::route('/{record}/edit'),
        ];
    }
}
