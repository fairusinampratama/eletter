@props(['letter'])

<div class="flex flex-col space-y-1">
    @php
    $status = $letter->getSigningStatus();
    $isCommitteeLetter = $letter->category->committee_id !== null;

    if ($isCommitteeLetter) {
    $roleNames = [
    1 => 'Sekretaris Panitia',
    2 => 'Ketua Panitia',
    3 => 'Ketua Umum',
    4 => 'Pembina'
    ];
    } else {
    $roleNames = [
    1 => 'Sekretaris Umum',
    2 => 'Ketua Umum',
    3 => 'Pembina'
    ];
    }
    @endphp

    @foreach($status['signed'] as $signature)
    <div class="flex items-center text-green-600">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="text-sm">{{ $roleNames[$signature->order] ?? 'Unknown' }}</span>
    </div>
    @endforeach

    @foreach($status['pending'] as $signature)
    <div class="flex items-center text-gray-500">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm">{{ $roleNames[$signature->order] ?? 'Unknown' }}</span>
    </div>
    @endforeach
</div>
