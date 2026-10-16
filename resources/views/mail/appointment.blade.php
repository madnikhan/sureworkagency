<!DOCTYPE html>
<html lang="en">
<body>
    <p>Hello {{ $appointment->name }},</p>
    <p>Your consultation request with {{ config('brand.name') }} is {{ str_replace('_', ' ', $appointment->status) }}.</p>
    @if ($appointment->starts_at)
        <p>Time: {{ $appointment->starts_at->timezone($appointment->timezone ?: 'Europe/London')->format('j M Y H:i') }} ({{ $appointment->timezone }})</p>
    @endif
    @if ($appointment->meeting_link)
        <p>Meeting link: {{ $appointment->meeting_link }}</p>
    @endif
    <p>This message is about the appointment you asked for. It is not a marketing email.</p>
</body>
</html>
