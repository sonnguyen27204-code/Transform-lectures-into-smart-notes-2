@extends('layouts.app')

@section('title', 'Upload Lecture')

@section('content')
<div class="max-w-2xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('lectures.index') }}" class="p-2 rounded-xl hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors">
            <svg class="w-5 h-5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-stone-900 dark:text-stone-50">Upload Lecture</h1>
            <p class="text-stone-500">Add a new lecture recording</p>
        </div>
    </div>

    {{-- Error --}}
    @if($errors->any())
        <x-ui.alert type="error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('lectures.store') }}" enctype="multipart/form-data" id="upload-form" class="space-y-6">
        @csrf

        {{-- Info --}}
        <x-ui.card title="Lecture Info" description="Add details to help organize your lectures.">
            <div class="space-y-4">
                <x-ui.input
                    label="Title"
                    name="title"
                    required
                    placeholder="Lecture 3 - Linear Functions"
                    :value="old('title')"
                />

                <x-ui.input
                    label="Description (optional)"
                    name="description"
                    type="textarea"
                    placeholder="Brief description of the lecture content..."
                />

                <x-ui.input label="Language" name="language" type="select">
                    <option value="vi" @selected(old('language', 'vi') === 'vi')>Vietnamese</option>
                    <option value="en" @selected(old('language') === 'en')>English</option>
                    <option value="auto" @selected(old('language') === 'auto')>Auto detect</option>
                </x-ui.input>
            </div>
        </x-ui.card>

        {{-- Upload --}}
        <x-ui.card title="Audio File" description="Upload your lecture recording in MP3, WAV, M4A, OGG, or WebM format.">
            <x-lectures.dropzone :max-kb="config('gemini.audio.max_size_kb')" />
            <x-lectures.recorder />
        </x-ui.card>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3 pb-8">
            <a href="{{ route('lectures.index') }}" class="text-sm font-medium text-stone-500 hover:text-stone-700 dark:hover:text-stone-300 transition-colors">Cancel</a>
            <x-ui.button type="submit" variant="primary" size="lg" id="submit-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Upload & Process
            </x-ui.button>
        </div>
    </form>
</div>
@push('scripts')
<script>
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('audio-input');
const dropzoneEmpty = document.getElementById('dropzone-empty');
const dropzoneFilled = document.getElementById('dropzone-filled');

['dragenter', 'dragover'].forEach(evt => {
    dropzone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropzone.classList.add('border-stone-400', 'bg-stone-100');
    });
});

['dragleave', 'drop'].forEach(evt => {
    dropzone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-stone-400', 'bg-stone-100');
    });
});

dropzone.addEventListener('drop', (e) => {
    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        showFile(fileInput.files[0]);
    }
});

fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) showFile(fileInput.files[0]);
});

function showFile(file) {
    dropzoneEmpty.classList.add('hidden');
    dropzoneFilled.classList.remove('hidden');
    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-meta').textContent = formatBytes(file.size);
    document.getElementById('preview-player').src = URL.createObjectURL(file);
}

function resetFile() {
    fileInput.value = '';
    dropzoneEmpty.classList.remove('hidden');
    dropzoneFilled.classList.add('hidden');
    document.getElementById('preview-player').src = '';
}

function formatBytes(bytes) {
    const k = 1024, sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// Record
let mediaRecorder, audioChunks = [], recordInterval, recordSeconds = 0;

async function toggleRecord() {
    const btn = document.getElementById('btn-record');
    const label = document.getElementById('record-label');
    const timer = document.getElementById('record-timer');
    const status = document.getElementById('record-status');

    if (!mediaRecorder || mediaRecorder.state === 'inactive') {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            recordSeconds = 0;

            mediaRecorder.ondataavailable = (e) => audioChunks.push(e.data);
            mediaRecorder.onstop = () => {
                const blob = new Blob(audioChunks, { type: 'audio/webm' });
                const file = new File([blob], 'recording-' + Date.now() + '.webm', { type: 'audio/webm' });
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                showFile(file);
                stream.getTracks().forEach(t => t.stop());
                status.classList.add('hidden');
            };

            mediaRecorder.start();
            btn.classList.remove('bg-stone-100', 'dark:bg-stone-800', 'text-stone-700', 'dark:text-stone-300');
            btn.classList.add('bg-red-500', 'text-white');
            label.textContent = 'Stop Recording';
            timer.classList.remove('hidden');
            status.classList.remove('hidden');
            status.textContent = 'Recording... Click button to stop.';

            recordInterval = setInterval(() => {
                recordSeconds++;
                const m = String(Math.floor(recordSeconds / 60)).padStart(2, '0');
                const s = String(recordSeconds % 60).padStart(2, '0');
                timer.textContent = `${m}:${s}`;
            }, 1000);
        } catch (e) {
            alert('Cannot access microphone: ' + e.message);
        }
    } else {
        mediaRecorder.stop();
        clearInterval(recordInterval);
        btn.classList.add('bg-stone-100', 'dark:bg-stone-800', 'text-stone-700', 'dark:text-stone-300');
        btn.classList.remove('bg-red-500', 'text-white');
        label.textContent = 'Start Recording';
        timer.classList.add('hidden');
    }
}

// Submit loading
document.getElementById('upload-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Processing...';
});
</script>
@endpush
@endsection