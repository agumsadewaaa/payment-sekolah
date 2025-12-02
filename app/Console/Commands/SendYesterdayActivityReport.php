<?php

namespace App\Console\Commands;

use App\Mail\DailyActivityReportMail;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendYesterdayActivityReport extends Command
{
    protected $signature = 'report:activity-yesterday {--to=* : Optional override recipient emails}';

    protected $description = 'Send yesterday\'s activity report via email to admins at 00:01.';

    public function handle(): int
    {
        $yesterday = Carbon::yesterday();
        $start = $yesterday->copy()->startOfDay();
        $end = $yesterday->copy()->endOfDay();

        $this->info("Generating report for: " . $yesterday->toDateString());

        $activities = ActivityLog::with(['user' => function($q){ $q->select('id','name','email'); }])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $dateLabel = $yesterday->translatedFormat('d F Y');


        // Hardcode recipient email
        $recipient = 'keuanganyesa@gmail.com';
        $mailable = new DailyActivityReportMail($activities, $dateLabel);
        Mail::to($recipient)->send($mailable);
        $this->info('Queued report to: ' . $recipient);

        return self::SUCCESS;
    }
}
