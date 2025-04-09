<?php

namespace Alyakin\Reporting\Tests\Traits;

use Alyakin\Reporting\Models\Report;
use Alyakin\Reporting\Tests\TestCase;
use Alyakin\Reporting\Traits\Reportable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class ReportableTest extends TestCase
{
    protected $reportableModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['db']->connection()->getSchemaBuilder()->create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        $this->reportableModel = new class extends \Illuminate\Database\Eloquent\Model
        {
            use Reportable;

            public $table = 'videos';

            public $incrementing = false;

            public $keyType = 'string';

            protected $guarded = [];
        };
    }

    public function test_add_report_creates_report_for_model()
    {
        $model = $this->reportableModel::create([
            'id' => (string) Str::uuid(),
            'title' => 'Test video',
        ]);

        $report = $model->addReport([
            'reason' => 'abuse',
            'meta' => ['ip' => '127.0.0.1'],
        ]);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals('abuse', $report->reason);
        $this->assertEquals($model->getKey(), $report->reportable_id);
        $this->assertEquals(get_class($model), $report->reportable_type);
    }

    public function test_reports_relation_returns_collection()
    {
        $model = $this->reportableModel::create([
            'id' => (string) Str::uuid(),
            'title' => 'Test video',
        ]);

        $model->addReport(['reason' => 'spam']);
        $model->addReport(['reason' => 'violence']);

        $this->assertCount(2, $model->reports);
        $this->assertEquals(['spam', 'violence'], $model->reports->pluck('reason')->all());
    }
}
