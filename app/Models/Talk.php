<?php

namespace App\Models;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
