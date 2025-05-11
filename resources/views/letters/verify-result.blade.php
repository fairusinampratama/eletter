<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Result - {{ $letter->code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Verification Result</h1>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-2">Letter Details</h2>
                    <p><span class="font-medium">Code:</span> {{ $letter->code }}</p>
                    <p><span class="font-medium">Date:</span> {{ $letter->date }}</p>
                    <p><span class="font-medium">Category:</span> {{ $letter->category->name }}</p>
                    <p><span class="font-medium">Verification ID:</span> {{ $letter->verification_id }}</p>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-2">Verification Status</h2>
                    <div
                        class="p-4 rounded-lg {{ $valid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        @if($valid)
                        <p class="font-medium">✓ Letter is valid and all signatures are authentic</p>
                        @else
                        <p class="font-medium">✗ Letter verification failed</p>
                        @if(!empty($unsignedSignatures))
                        <p class="mt-2">Waiting for signatures from: {{ implode(', ', $unsignedSignatures) }}</p>
                        @endif
                        @if(!empty($invalidSignatures))
                        <p class="mt-2">Invalid signatures from: {{ implode(', ', $invalidSignatures) }}</p>
                        @endif
                        @endif
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-3">Signatures:</h2>
                    <ul class="space-y-2">
                        @foreach($letter->signatures as $signature)
                        <li class="flex items-center bg-gray-50 p-3 rounded">
                            <span class="font-medium">{{ $signature->signer->fullname }}</span>
                            <span class="text-sm text-gray-500 ml-2">
                                @if($signature->signed_at)
                                @if($signature->signed_at instanceof \Carbon\Carbon)
                                {{ $signature->signed_at->format('M d, Y H:i') }}
                                @else
                                {{ \Carbon\Carbon::parse($signature->signed_at)->format('M d, Y H:i') }}
                                @endif
                                @else
                                Not signed yet
                                @endif
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-6">
                    <a href="{{ route('verify', $letter->verification_id) }}"
                        class="text-blue-500 hover:text-blue-700">← Back to Verification</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>