<?php

namespace App\Http\Controllers\Admin;

use App\Actions\TransitionRecord;
use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Mail\AppointmentUpdated;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        abort_unless(auth()->user()->hasPermission('appointments.manage'), 403);

        return Inertia::render('Admin/Appointments', [
            'appointments' => Appointment::query()->latest()->paginate(20),
        ]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('appointments.manage'), 403);
        $data = $request->validate([
            'status' => ['required', 'string'],
            'starts_at' => ['nullable', 'date'],
            'meeting_link' => ['nullable', 'string', 'max:255'],
        ]);
        if ($data['starts_at'] ?? null) {
            $appointment->starts_at = $data['starts_at'];
            $appointment->save();
        }
        if ($data['meeting_link'] ?? null) {
            $appointment->meeting_link = $data['meeting_link'];
            $appointment->save();
        }
        if ($data['status'] !== $appointment->status) {
            TransitionRecord::run($appointment, $data['status'], AppointmentStatus::map(), 'appointment.status');
        }
        if (config('mail.default') !== 'log') {
            Mail::to($appointment->email)->send(new AppointmentUpdated($appointment));
        }

        return back()->with('success', 'Appointment updated.');
    }
}
