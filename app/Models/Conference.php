<?php

namespace App\Models;

use Filament\Forms\Get;
use App\Enums\Region;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [

            // Tabs::make()
            //     ->columnSpanFull()
            //     ->tabs([
            //         Tabs\Tab::make('Conference Details')
            //             ->schema([
            //                 TextInput::make('name')
            //                     ->columnSpanFull()
            //                     ->label(label: 'Conference')
            //                     ->required()
            //                     ->default(state: 'My Conference')
            //                     ->hint(hint: "The name of the Conference")
            //                     ->hintIcon(icon: 'heroicon-o-information-circle')
            //                     ->maxLength(100),

            //                 MarkdownEditor::make('description')
            //                     ->columnSpanFull()
            //                     //->disableToolbarButtons()
            //                     //->toolbarButtons(buttons: ['h2', 'bold'])
            //                     ->required(),

            //                 DateTimePicker::make('start_date')
            //                     ->native(condition: false)
            //                     ->helperText(text: "Please include the time when the conference will start along with Date")
            //                     ->required(),

            //                 DateTimePicker::make('end_date')
            //                     ->native(condition: false)
            //                     ->helperText(text: "Please include the tentative time when the Conference will end")
            //                     ->required(),

            //                 Fieldset::make('Status')
            //                     ->columns(1)
            //                     ->schema([

            //                         Select::make('status')
            //                             ->options(options: [
            //                                 'draft' => 'Draft',
            //                                 'published' => 'Published',
            //                                 'archieved' => 'Archieved'
            //                             ])
            //                             ->required(),
            //                         Toggle::make(name: 'is_published')
            //                             ->default(state: true),

            //                     ])
            //             ]),
            //         Tabs\Tab::make('Location')
            //             ->schema([
            //                 Select::make(name: 'region')
            //                     ->live()
            //                     ->required()
            //                     ->enum(enum: Region::class)
            //                     ->options(options: Region::class),

            //                 Select::make('venue_id')
            //                     ->searchable()
            //                     ->preload()
            //                     ->editOptionForm(Venue::getForm())
            //                     ->createOptionForm(Venue::getForm())
            //                     ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
            //                         return $query->where(column: 'region', operator: $get('region'));
            //                     }),
            //             ])

            //     ]),

            Section::make(heading: 'Conference Details')
                ->collapsible()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->label(label: 'Conference')
                        ->required()
                        ->default(state: 'My Conference')
                        ->hint(hint: "The name of the Conference")
                        ->hintIcon(icon: 'heroicon-o-information-circle')
                        ->maxLength(100),

                    MarkdownEditor::make('description')
                        ->columnSpanFull()
                        //->disableToolbarButtons()
                        //->toolbarButtons(buttons: ['h2', 'bold'])
                        ->required(),

                    DateTimePicker::make('start_date')
                        ->native(condition: false)
                        ->helperText(text: "Please include the time when the conference will start along with Date")
                        ->required(),

                    DateTimePicker::make('end_date')
                        ->native(condition: false)
                        ->helperText(text: "Please include the tentative time when the Conference will end")
                        ->required(),

                    Fieldset::make('Status')
                        ->columns(1)
                        ->schema([

                            Select::make('status')
                                ->options(options: [
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archieved' => 'Archieved'
                                ])
                                ->required(),
                            Toggle::make(name: 'is_published')
                                ->default(state: true),

                        ])
                ]),

            Section::make(heading: 'Location')
                ->columns(2)
                ->schema([
                    Select::make(name: 'region')
                        ->live()
                        ->required()
                        ->enum(enum: Region::class)
                        ->options(options: Region::class),

                    Select::make('venue_id')
                        ->searchable()
                        ->preload()
                        ->editOptionForm(Venue::getForm())
                        ->createOptionForm(Venue::getForm())
                        ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                            return $query->where(column: 'region', operator: $get('region'));
                        }),
                ]),






            // CheckboxList::make('speakers')
            //     ->relationship(name: 'speakers', titleAttribute: 'name')
            //     ->options(
            //         options: Speaker::all()->pluck('name', 'id')
            //     )
            //     ->searchable()
        ];
    }
}
