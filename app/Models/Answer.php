<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Answer extends Model
{
    protected $fillable = ['response_id', 'question_id', 'value_text'];

    public function response(): BelongsTo        { return $this->belongsTo(Response::class); }
    public function question(): BelongsTo        { return $this->belongsTo(Question::class); }
    public function selectedOptions(): HasMany   { return $this->hasMany(AnswerOption::class); }
    public function gridCells(): HasMany         { return $this->hasMany(AnswerGridCell::class); }
    public function questionOptions(): BelongsToMany {
        return $this->belongsToMany(QuestionOption::class, 'answer_options');
    }
}
