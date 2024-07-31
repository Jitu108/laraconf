<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Enums\Region;
use App\Models\Venue;
use Filament\Forms\Form;
use App\Models\Conference;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\FormsComponent;
use function Laravel\Prompts\text;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ConferenceResource\Pages;
use App\Filament\Resources\ConferenceResource\RelationManagers;
use App\Models\Speaker;

class ConferenceResource extends Resource
{
    protected static ?string $model = Conference::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(label: 'Conference')
                    ->required()
                    ->default(state: 'My Conference')
                    ->hint(hint: "The name of the Conference")
                    ->hintIcon(icon: 'heroicon-o-information-circle')
                    ->maxLength(100),

                Forms\Components\MarkdownEditor::make('description')
                    //->disableToolbarButtons()
                    //->toolbarButtons(buttons: ['h2', 'bold'])
                    ->required(),

                Forms\Components\DateTimePicker::make('start_date')
                    ->native(condition: false)
                    ->helperText(text: "Please include the time when the conference will start along with Date")
                    ->required(),

                Forms\Components\DateTimePicker::make('end_date')
                    ->native(condition: false)
                    ->helperText(text: "Please include the tentative time when the Conference will end")
                    ->required(),

                Forms\Components\Toggle::make(name: 'is_published')
                    ->default(state: true),

                Forms\Components\Select::make('status')
                    ->options(options: [
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archieved' => 'Archieved'
                    ])
                    ->required(),

                Forms\Components\Select::make(name: 'region')
                    ->live()
                    ->required()
                    ->enum(enum: Region::class)
                    ->options(options: Region::class),

                Forms\Components\Select::make('venue_id')
                    ->searchable()
                    ->preload()
                    ->editOptionForm(Venue::getForm())
                    ->createOptionForm(Venue::getForm())
                    ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                        return $query->where(column: 'region', operator: $get('region'));
                    }),

                // Forms\Components\CheckboxList::make('speakers')
                //     ->relationship(name: 'speakers', titleAttribute: 'name')
                //     ->options(
                //         options: Speaker::all()->pluck('name', 'id')
                //     )
                //     ->searchable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(label: 'Conference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable(),
                Tables\Columns\TextColumn::make('venue.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConferences::route('/'),
            'create' => Pages\CreateConference::route('/create'),
            'edit' => Pages\EditConference::route('/{record}/edit'),
        ];
    }
}
