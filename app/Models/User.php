<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    public function isAdmin(): bool        { return $this->role === 'admin'; }
    public function isResearcher(): bool   { return $this->role === 'researcher'; }
    public function isInterviewer(): bool  { return $this->role === 'interviewer'; }
    public function isSupervisor(): bool   { return $this->role === 'supervisor'; }
    public function isStudyLeader(): bool  { return $this->role === 'study_leader'; }

    public function canEditResponses(): bool
    {
        return in_array($this->role, ['admin', 'researcher', 'interviewer', 'supervisor', 'study_leader']);
    }

    public function canCheckResponses(): bool
    {
        return in_array($this->role, ['admin', 'supervisor', 'study_leader']);
    }

    public function canApproveResponses(): bool
    {
        return in_array($this->role, ['admin', 'study_leader']);
    }

    public function surveys(): HasMany { return $this->hasMany(Survey::class); }
    public function importJobs(): HasMany { return $this->hasMany(ImportJob::class); }
}
