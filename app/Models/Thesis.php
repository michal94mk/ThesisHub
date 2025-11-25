<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thesis extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'student_id',
        'supervisor_id',
        'title',
        'type',
        'field_of_study',
        'specialization',
        'abstract',
        'outline',
        'keywords',
        'status',
        'submission_date',
        'defense_date',
        'academic_year',
        'supervisor_notes',
        'approved_at',
        'submitted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'keywords' => 'array',
        'submission_date' => 'date',
        'defense_date' => 'date',
        'approved_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /**
     * Available thesis statuses
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_RETURNED_FOR_CORRECTIONS = 'returned_for_corrections';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SUBMITTED_FOR_DEFENSE = 'submitted_for_defense';
    public const STATUS_DEFENDED = 'defended';

    /**
     * Available thesis types
     */
    public const TYPE_BACHELOR = 'bachelor';
    public const TYPE_MASTER = 'master';

    /**
     * Get the student who owns the thesis.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the supervisor who oversees the thesis.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the documents for the thesis.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Check if thesis is in draft status
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if thesis is pending approval
     */
    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    /**
     * Check if thesis is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if thesis is defended
     */
    public function isDefended(): bool
    {
        return $this->status === self::STATUS_DEFENDED;
    }

    /**
     * Submit thesis for approval
     */
    public function submit(): void
    {
        $this->status = self::STATUS_PENDING_APPROVAL;
        $this->submitted_at = now();
        $this->save();
    }

    /**
     * Approve thesis
     */
    public function approve(): void
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_at = now();
        $this->save();
    }

    /**
     * Reject thesis
     */
    public function reject(?string $notes = null): void
    {
        $this->status = self::STATUS_REJECTED;
        if ($notes) {
            $this->supervisor_notes = $notes;
        }
        $this->save();
    }

    /**
     * Return thesis for corrections
     */
    public function returnForCorrections(string $notes): void
    {
        $this->status = self::STATUS_RETURNED_FOR_CORRECTIONS;
        $this->supervisor_notes = $notes;
        $this->save();
    }

    /**
     * Get status label for display
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_RETURNED_FOR_CORRECTIONS => 'Returned for Corrections',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_SUBMITTED_FOR_DEFENSE => 'Submitted for Defense',
            self::STATUS_DEFENDED => 'Defended',
            default => 'Unknown',
        };
    }

    /**
     * Get type label for display
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_BACHELOR => 'Bachelor',
            self::TYPE_MASTER => 'Master',
            default => 'Unknown',
        };
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by student
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to filter by supervisor
     */
    public function scopeForSupervisor($query, int $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }
}
