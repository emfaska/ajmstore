<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the analysis dashboard loads successfully and passes metrics to the view.
     */
    public function test_it_can_load_the_analysis_dashboard(): void
    {
        $response = $this->get(route('analysis.index'));

        $response->assertStatus(200);
        $response->assertViewIs('analysis.index');
        $response->assertViewHasAll(['filters', 'summary', 'trends', 'top_products', 'status_distribution']);
    }

    /**
     * Test that the form request validation fails when start_date is after end_date.
     */
    public function test_it_validates_invalid_date_range(): void
    {
        $response = $this->get(route('analysis.index', [
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->subDays(5)->format('Y-m-d'), // End date before start date
        ]));

        $response->assertSessionHasErrors(['end_date']);
    }

    /**
     * Test that validation fails if a future start date is provided.
     */
    public function test_it_validates_future_start_date(): void
    {
        $response = $this->get(route('analysis.index', [
            'start_date' => Carbon::now()->addDay()->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors(['start_date']);
    }
}
