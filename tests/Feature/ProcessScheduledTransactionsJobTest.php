<?php

use App\Jobs\ProcessScheduledTransactionsJob;

it('should schedule the ProcessScheduledTransactionsJob with parameters', function () {
    $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);

    $jobId = 123;
    $schedule->job(new ProcessScheduledTransactionsJob($jobId))->daily();

    $events = $schedule->events();

    // Assertions
    $this->assertNotEmpty($events);
    $this->assertContainsOnlyInstancesOf(\Illuminate\Console\Scheduling\Event::class, $events);
    $this->assertTrue($events[0]->isScheduled());
    $this->assertEquals(ProcessScheduledTransactionsJob::class, $events[0]->command());
    $this->assertEquals([$jobId], $events[0]->arguments());
});
