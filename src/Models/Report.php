<?php

namespace Alyakin\Reporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use SoftDeletes, Prunable, HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason',
        'meta',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
    ];


    public static function newFactory()
    {
        return \Alyakin\Reporting\Database\Factories\ReportFactory::new();
    }


    /**
     * Polymorphic relationship: the model being reported.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reportable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship: the user who submitted the report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('reporting.user_model'));
    }

    /**
     * Define the query for records that should be pruned.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        $days = config('reporting.soft_delete_days', 31);

        return $days !== null
            ? $this->onlyTrashed()->where('deleted_at', '<=', now()->subDays($days))
            : $this->newQuery()->whereRaw('1 = 0'); // Disable pruning if no limit
    }
}
