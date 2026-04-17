<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyCollaborator extends Model
{
    protected $fillable = ['survey_id', 'user_id'];

    public function survey(): BelongsTo { return $this->belongsTo(Survey::class); }
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
}
