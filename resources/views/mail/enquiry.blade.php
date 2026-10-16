<!DOCTYPE html>
<html lang="en">
<body>
    <p>{{ config('brand.name') }} received a {{ $lead->source }} enquiry.</p>
    <p>{{ $lead->name }} · {{ $lead->email }}</p>
    @if ($lead->phone)
        <p>{{ $lead->phone }}</p>
    @endif
    @if ($lead->service)
        <p>Service: {{ $lead->service }}</p>
    @endif
    @if ($lead->description)
        <p>{{ $lead->description }}</p>
    @endif
</body>
</html>
