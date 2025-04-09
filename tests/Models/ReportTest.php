<?php

namespace Alyakin\Reporting\Tests\Models;

use Alyakin\Reporting\Models\Report;
use Alyakin\Reporting\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class ReportTest extends TestCase
{
    protected $postModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Тестовая таблица "posts"
        $this->app['db']->connection()->getSchemaBuilder()->create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        $this->postModel = new class extends \Illuminate\Database\Eloquent\Model
        {
            public $table = 'posts';

            public $incrementing = false;

            public $keyType = 'string';

            protected $guarded = [];
        };

        \Illuminate\Database\Eloquent\Factories\Factory::guessFactoryNamesUsing(fn (string $modelName) => null);
    }

    public function test_report_has_polymorphic_relation()
    {
        $report = new Report();
        $this->assertInstanceOf(MorphTo::class, $report->reportable());
    }

    public function test_report_can_store_and_cast_meta_field()
    {
        $report = Report::create([
            'reportable_type' => 'Post',
            'reportable_id' => (string) Str::uuid(),
            'reason' => 'inappropriate',
            'meta' => ['ip' => '127.0.0.1', 'agent' => 'test'],
        ]);

        $this->assertEquals('127.0.0.1', $report->meta['ip']);
        $this->assertIsArray($report->meta);
    }

    public function test_report_uses_soft_deletes()
    {
        $post = $this->postModel::create(['id' => (string) Str::uuid(), 'title' => 'Test']);
        $report = Report::factory()->for($post, 'reportable')->create();
        $report->delete();

        $this->assertSoftDeleted($report);
    }

    public function test_report_has_uuid_as_primary_key()
    {
        $post = $this->postModel::create(['id' => (string) Str::uuid(), 'title' => 'Test']);
        $report = Report::factory()->for($post, 'reportable')->create();

        $this->assertTrue(Str::isUuid($report->getKey()));
    }
}
