<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionOption extends Model
{
    protected $fillable = ['question_id', 'option_code', 'label', 'sort_order'];
    protected $casts    = ['sort_order' => 'integer'];

    public function question(): BelongsTo    { return $this->belongsTo(Question::class); }
    public function answerOptions(): HasMany { return $this->hasMany(AnswerOption::class); }
    public function gridCells(): HasMany     { return $this->hasMany(AnswerGridCell::class); }
}
