<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportJob extends Model
{
    protected $fillable = [
        'survey_id', 'user_id', 'original_filename',
        'row_count_total', 'row_count_imported', 'row_count_skipped',
        'status', 'error_log', 'completed_at',
    ];

    protected $casts = [
        'error_log'    => 'array',
        'completed_at' => 'datetime',
    ];

    public function survey(): BelongsTo { return $this->belongsTo(Survey::class); }
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
}
