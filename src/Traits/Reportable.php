<?php

namespace Alyakin\Reporting\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait Reportable
{
    /**
     * Define a polymorphic one-to-many relationship with the report model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports(): MorphMany
    {
        $reportModel = config('reporting.report_model', \Alyakin\Reporting\Models\Report::class);

        return $this->morphMany($reportModel, 'reportable');
    }

    /**
     * Add a new report to the model.
     *
     * @param array $attributes
     * @param int|string|null $userId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addReport(array $attributes, $userId = null)
    {
        // Determine the user ID: passed value or authenticated user ID
        $resolvedUserId = $userId ?? Auth::id();

        return $this->reports()->create(array_merge($attributes, [
            'user_id' => $resolvedUserId,
        ]));
    }
}
