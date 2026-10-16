<?php

namespace App\Console\Commands;

use App\Mail\AppointmentUpdated;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RemindAppointments extends Command
{
    protected $signature = 'appointments:remind';

    protected $description = 'Email customers about confirmed appointments due tomorrow';

    public function handle(): int
    {
        if (config('mail.default') === 'log') {
            $this->comment('Mail is set to log, so reminders were not sent.');

            return self::SUCCESS;
        }

        $appointments = Appointment::query()
            ->where('status', 'confirmed')
            ->whereBetween('starts_at', [now()->addDay()->startOfDay(), now()->addDay()->endOfDay()])
            ->get();

        foreach ($appointments as $appointment) {
            Mail::to($appointment->email)->send(new AppointmentUpdated($appointment));
        }

        $this->info('Reminders sent: '.$appointments->count());

        return self::SUCCESS;
    }
}
