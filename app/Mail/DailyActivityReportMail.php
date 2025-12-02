<?php

namespace App\Mail;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DailyActivityReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @var \Illuminate\Support\Collection<int, ActivityLog> */
    public $activities;

    /** @var string */
    public $dateLabel;

    public function __construct($activities, string $dateLabel)
    {
        $this->activities = $activities;
        $this->dateLabel = $dateLabel;
    }

    public function build()
    {
        return $this->subject('Laporan Aktivitas Harian — ' . $this->dateLabel)
            ->view('emails.daily_activity_report');
    }
}
