<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GridRow extends Model
{
    protected $fillable = ['question_id', 'row_code', 'label', 'sort_order'];
    protected $casts    = ['sort_order' => 'integer'];

    public function question(): BelongsTo  { return $this->belongsTo(Question::class); }
    public function gridCells(): HasMany   { return $this->hasMany(AnswerGridCell::class); }
}
