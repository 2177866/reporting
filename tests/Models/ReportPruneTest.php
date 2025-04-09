<?php

namespace Alyakin\Reporting\Tests\Models;

use Alyakin\Reporting\Models\Report;
use Alyakin\Reporting\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ReportPruneTest extends TestCase
{
    public function test_old_soft_deleted_reports_are_pruned()
    {
        Config::set('reporting.soft_delete_days', 10);

        $reportToDelete = Report::factory()->create([
            'reportable_type' => 'Post',
            'reportable_id' => (string) Str::uuid(),
            'deleted_at' => now()->subDays(11),
        ]);

        $reportToKeep = Report::factory()->create([
            'reportable_type' => 'Post',
            'reportable_id' => (string) Str::uuid(),
            'deleted_at' => now()->subDays(5),
        ]);

        $this->assertDatabaseHas('reports', ['id' => $reportToDelete->id]);
        $this->artisan('model:prune', ['--model' => Report::class])->run();

        $this->assertDatabaseMissing('reports', ['id' => $reportToDelete->id]);
        $this->assertDatabaseHas('reports', ['id' => $reportToKeep->id]);
    }
}
