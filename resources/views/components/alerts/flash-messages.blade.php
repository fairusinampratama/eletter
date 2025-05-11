@if (session()->has('success'))
<x-alerts.flash-message type="success">
    {{ session('success') }}
</x-alerts.flash-message>
@endif

@if (session()->has('error'))
<x-alerts.flash-message type="error">
    {{ session('error') }}
</x-alerts.flash-message>
@endif

@if (session()->has('info'))
<x-alerts.flash-message type="info">
    {{ session('info') }}
</x-alerts.flash-message>
@endif

@if (session()->has('warning'))
<x-alerts.flash-message type="warning">
    {{ session('warning') }}
</x-alerts.flash-message>
@endif