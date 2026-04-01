<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnswerGridCell extends Model
{
    protected $fillable = ['answer_id', 'grid_row_id', 'question_option_id', 'cell_value'];

    public function answer(): BelongsTo         { return $this->belongsTo(Answer::class); }
    public function gridRow(): BelongsTo        { return $this->belongsTo(GridRow::class); }
    public function questionOption(): BelongsTo { return $this->belongsTo(QuestionOption::class); }
}
