<?php

namespace App\Models;

use Filament\Forms;
use App\Models\Speaker;
use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Models\Conference;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Talk extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'speaker_id' => 'integer',
        'status' => TalkStatus::class,
        'length' => TalkLength::class
    ];

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public static function getMiniForm($speakerId = null): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\RichEditor::make('abstract')
                ->required()
                ->columnSpanFull(),
            Forms\Components\Select::make('speaker_id')
                ->hidden(function () use ($speakerId) {
                    return $speakerId != null;
                })
                ->relationship('speaker', 'name')
                ->required()
                ->columnSpanFull(),
        ];
    }

    public static function getForm($speakerId = null): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\RichEditor::make('abstract')
                ->required()
                ->columnSpanFull(),
            Forms\Components\Select::make('speaker_id')
                ->hidden(function () use ($speakerId) {
                    return $speakerId != null;
                })
                ->relationship('speaker', 'name')
                ->required()
                ->columnSpanFull(),

            Forms\Components\Select::make('status')
                ->enum(enum: TalkStatus::class)
                ->options(options: TalkStatus::class),
            Forms\Components\Select::make('length')
                ->enum(enum: TalkLength::class)
                ->options(options: TalkLength::class),
            Forms\Components\Toggle::make('new_talk'),

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
        ];
    }

    public function approve(): void
    {
        if ($this->status == TalkStatus::SUBMITTED) {
            $this->status = TalkStatus::APPROVED;
            //email the speaker that the talk is accepted

            $this->save();
        }
    }

    public function canApproveOrReject(): bool
    {
        if ($this->status == TalkStatus::SUBMITTED) {
            return true;
        }
        return false;
    }

    public function reject(): void
    {
        if ($this->status == TalkStatus::SUBMITTED) {
            $this->status = TalkStatus::REJECTED;
            //email the speaker that the talk is accepted

            $this->save();
        }
    }

    public function revertToSubmitted(): void
    {
        $this->status = TalkStatus::SUBMITTED;
        $this->save();
    }

    public function canReject(): bool
    {
        if ($this->status == TalkStatus::SUBMITTED) {
            return true;
        }
        return false;
    }
}
