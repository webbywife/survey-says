<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = ['survey_id', 'title', 'description', 'sort_order', 'display_condition'];
    protected $casts    = ['sort_order' => 'integer', 'display_condition' => 'array'];

    public function survey(): BelongsTo    { return $this->belongsTo(Survey::class); }
    public function questions(): HasMany   { return $this->hasMany(Question::class)->orderBy('sort_order'); }
}
