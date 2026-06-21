<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\ReporteDraft;
use App\Models\AsignacionTemp;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    ReporteDraft::where('updated_at', '<', now()->subDays(2))->delete();
    AsignacionTemp::where('updated_at', '<', now()->subDays(2))->delete();
})->dailyAt('03:00');
