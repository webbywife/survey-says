<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'survey_id', 'section_id', 'variable_code', 'label', 'help_text',
        'type', 'sort_order', 'is_required', 'config',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'config'      => 'array',
        'sort_order'  => 'integer',
    ];

    public function survey(): BelongsTo    { return $this->belongsTo(Survey::class); }
    public function section(): BelongsTo   { return $this->belongsTo(Section::class); }
    public function options(): HasMany     { return $this->hasMany(QuestionOption::class)->orderBy('sort_order'); }
    public function gridRows(): HasMany    { return $this->hasMany(GridRow::class)->orderBy('sort_order'); }
    public function skipRulesFrom(): HasMany { return $this->hasMany(SkipRule::class, 'source_question_id'); }
    public function answers(): HasMany     { return $this->hasMany(Answer::class); }

    public function isChoiceType(): bool  { return in_array($this->type, ['single_choice', 'multi_select']); }
    public function isMultiSelect(): bool { return $this->type === 'multi_select'; }
    public function isGrid(): bool        { return $this->type === 'grid'; }
    public function isPHLocation(): bool  { return $this->type === 'ph_location'; }
}
