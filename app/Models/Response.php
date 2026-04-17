<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Response extends Model
{
    protected $fillable = [
        'survey_id', 'serial', 'status', 'started_at', 'completed_at',
        'duration_seconds', 'ip_address', 'user_agent', 'respondent_token', 'is_offline_sync',
        'checked_at', 'checked_by', 'approved_at', 'approved_by',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'checked_at'   => 'datetime',
        'approved_at'  => 'datetime',
        'status'       => 'integer',
    ];

    public function survey(): BelongsTo  { return $this->belongsTo(Survey::class); }
    public function answers(): HasMany   { return $this->hasMany(Answer::class); }

    public function scopeComplete($q) { return $q->where('status', 1); }
    public function scopePartial($q)  { return $q->where('status', 2); }

    public function generateSerial(): void {
        $this->serial = 'S' . $this->survey_id . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        $this->saveQuietly();
    }
}
