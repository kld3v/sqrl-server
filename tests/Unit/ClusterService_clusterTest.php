<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClusterService_clusterTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // The cluster method should execute without errors.
    function test_cluster_method_execution_without_errors()
    {
        $clusterService = new \App\Services\CalculateVenues\ClusterService();
        $clusterService->cluster();
        $this->assertTrue(true);
    }
}
