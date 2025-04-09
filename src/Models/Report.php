<?php

namespace Alyakin\Reporting\Models;

use Alyakin\Reporting\Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, HasUuids, Prunable, SoftDeletes;

    protected $fillable = [
        'id',
        'reportable_type',
        'reportable_id',
        'reason',
        'meta',
        'user_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory<Report>
     */
    public static function newFactory()
    {
        return ReportFactory::new();
    }

    /**
     * Polymorphic relationship: the model being reported.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, self>
     */
    public function reportable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship: the user who submitted the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Foundation\Auth\User, self>
     */
    public function user()
    {
        /** @var class-string<\Illuminate\Foundation\Auth\User> $userModel */
        $userModel = config('reporting.user_model', 'App\\Models\\User');

        return $this->belongsTo($userModel);
    }

    /**
     * Define the query for records that should be pruned.
     *
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function prunable()
    {
        /** @var null|int */
        $days = config('reporting.soft_delete_days', 31);

        return $days !== null
            ? $this->onlyTrashed()->where('deleted_at', '<=', now()->subDays($days))
            : $this->newQuery()->whereRaw('1 = 0'); // Disable pruning if no limit
    }
}
