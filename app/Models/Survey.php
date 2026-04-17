<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Survey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'description', 'status', 'public_token',
        'starts_at', 'ends_at', 'allow_partial_save', 'show_progress_bar', 'logo_url',
    ];

    protected $casts = [
        'starts_at'          => 'datetime',
        'ends_at'            => 'datetime',
        'allow_partial_save' => 'boolean',
        'show_progress_bar'  => 'boolean',
    ];

    protected static function booted(): void {
        static::creating(function (Survey $s) {
            $s->public_token = $s->public_token ?? Str::random(32);
        });
    }

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function sections(): HasMany  { return $this->hasMany(Section::class)->orderBy('sort_order'); }
    public function questions(): HasMany { return $this->hasMany(Question::class)->orderBy('sort_order'); }
    public function responses(): HasMany { return $this->hasMany(Response::class); }
    public function skipRules(): HasMany { return $this->hasMany(SkipRule::class); }
    public function importJobs(): HasMany      { return $this->hasMany(ImportJob::class); }
    public function collaboratorUsers(): BelongsToMany {
        return $this->belongsToMany(User::class, 'survey_collaborators')->withTimestamps();
    }

    public function scopeActive($q)        { return $q->where('status', 'active'); }
    public function scopeOwnedBy($q, $uid) { return $q->where('user_id', $uid); }

    public function canBeAccessedBy(User $user): bool
    {
        return $user->isAdmin()
            || $this->user_id === $user->id
            || $this->collaboratorUsers()->where('users.id', $user->id)->exists();
    }

    public function isOwnedOrAdminBy(User $user): bool
    {
        return $user->isAdmin() || $this->user_id === $user->id;
    }

    public function isOpen(): bool {
        return $this->status === 'active'
            && ($this->starts_at === null || $this->starts_at->isPast())
            && ($this->ends_at === null   || $this->ends_at->isFuture());
    }
}
