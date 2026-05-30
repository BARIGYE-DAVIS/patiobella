{{-- resources/views/admin/users/signature.blade.php --}}

@extends('layouts.app')

@section('title', 'Digital Signature')
@section('page-title', 'Digital Signature for ' . $targetUser->first_name . ' ' . $targetUser->last_name)

@section('content')
<style>
    .signature-canvas {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background-color: white;
        cursor: crosshair;
    }
    .signature-container {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem;
    }
</style>

<div class="max-w-2xl mx-auto">

    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ route('users.edit', $targetUser->id) }}" class="text-gray-600 hover:text-gray-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to User Details
        </a>
    </div>

    {{-- Draw Signature Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-pen mr-2 text-blue-600"></i>
                Draw Your Signature
            </h3>
            <p class="text-xs text-gray-500 mt-1">Use mouse or touch to draw your signature in the box below</p>
        </div>
        <div class="p-6">
            <div class="signature-container">
                <canvas id="signatureCanvas" width="500" height="200" class="signature-canvas w-full" style="width: 100%; height: auto; touch-action: none;"></canvas>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="button" id="clearCanvasBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-eraser mr-1"></i> Clear
                </button>
                <button type="button" id="saveCanvasBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-save mr-1"></i> Save Signature
                </button>
            </div>
        </div>
    </div>

    {{-- Current Signature Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-signature mr-2 text-emerald-600"></i>
                Current Signature
            </h3>
        </div>
        <div class="p-6 text-center">
            @if($targetUser->signature_url)
                <div class="inline-block p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <img src="{{ $targetUser->signature_url }}?v={{ time() }}" alt="Signature" class="max-w-md max-h-24" id="currentSignatureImg">
                </div>
                <div class="mt-4">
                    <button type="button" id="removeSignatureBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-trash mr-1"></i> Remove Signature
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Last updated: {{ $targetUser->signature_updated_at ? \Carbon\Carbon::parse($targetUser->signature_updated_at)->format('d M Y H:i') : 'Never' }}
                </p>
            @else
                <div class="text-gray-400 py-8">
                    <i class="fas fa-signature text-4xl mb-2 block"></i>
                    <p>No signature uploaded yet</p>
                    <p class="text-xs mt-2">Draw your signature above and click Save</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<script>
    // Signature Pad for drawing
    const canvas = document.getElementById('signatureCanvas');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'white',
        penColor: '#1f2937',
        velocityFilterWeight: 0.7,
        minWidth: 1,
        maxWidth: 3,
        throttle: 16
    });

    // Adjust canvas size for responsiveness
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    // Clear canvas button
    document.getElementById('clearCanvasBtn').addEventListener('click', () => {
        signaturePad.clear();
    });

    // Save drawn signature
    document.getElementById('saveCanvasBtn').addEventListener('click', async () => {
        if (signaturePad.isEmpty()) {
            alert('Please draw a signature first.');
            return;
        }

        const saveBtn = document.getElementById('saveCanvasBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';

        try {
            const dataURL = signaturePad.toDataURL('image/png');
            const blob = await fetch(dataURL).then(res => res.blob());
            const formData = new FormData();
            formData.append('signature', blob, 'signature.png');
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('{{ route("users.upload-signature", $targetUser->id) }}', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to save signature');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    });

    // Remove signature
    const removeBtn = document.getElementById('removeSignatureBtn');
    if (removeBtn) {
        removeBtn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to remove this signature?')) return;

            try {
                const response = await fetch('{{ route("users.remove-signature", $targetUser->id) }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Failed to remove signature');
            }
        });
    }
</script>
@endpush
@endsection
