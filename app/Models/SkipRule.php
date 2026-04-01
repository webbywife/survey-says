<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkipRule extends Model
{
    protected $fillable = [
        'survey_id', 'source_question_id', 'condition_type',
        'condition_value', 'target_question_id',
    ];

    public function survey(): BelongsTo          { return $this->belongsTo(Survey::class); }
    public function sourceQuestion(): BelongsTo  { return $this->belongsTo(Question::class, 'source_question_id'); }
    public function targetQuestion(): BelongsTo  { return $this->belongsTo(Question::class, 'target_question_id'); }
}
