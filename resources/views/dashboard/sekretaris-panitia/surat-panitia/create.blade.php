@extends('layouts.dashboard')

@section('content')

<x-dashboard.page-wrapper :title="$title"
    :breadcrumbItems="[['label' => 'Surat Panitia', 'url' => route('sekretaris-panitia.surat-panitia.index')], ['label' => 'Tambah Surat']]">
    <div x-data="qrPlacement({
        sekretarisPanitiaId: {{ $sekretarisPanitia ? $sekretarisPanitia->id : 'null' }},
        ketuaPanitiaId: {{ $ketuaPanitia ? $ketuaPanitia->id : 'null' }},
        ketuaUmumId: {{ $users->where('role_id', 2)->where('is_active', true)->first()->id ?? 'null' }},
        pembinaId: {{ $users->where('role_id', 6)->where('is_active', true)->first()->id ?? 'null' }}
    })" x-init="$store.pdf.currentPage = 1" class="grid grid-cols-1 lg:grid-cols-2">
        <!-- Left: PDF Viewer -->
        <div
            class="bg-white dark:bg-gray-800 flex flex-col items-center justify-center min-h-[500px] p-8 max-w-3xl w-full mx-auto relative">
            <!-- Loading Spinner -->
            <div id="pdf-loading"
                class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-800/80 z-10 hidden">
                <div role="status">
                    <svg aria-hidden="true"
                        class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-primary-600"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div id="pdf-canvas-container"
                class="w-full max-w-xl flex items-center justify-center border-2 border-dashed border-gray-500 dark:border-gray-600 rounded-lg p-2 hidden relative">
                <canvas id="pdf-canvas" class="max-w-full h-auto w-full"></canvas>
                <!-- Marker overlays will be rendered here by JS -->
                <div id="marker-overlay"
                    style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;"></div>
            </div>
            <div id="pdf-placeholder"
                class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed border-gray-500 dark:border-gray-600 rounded-lg p-8 mb-4">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 mb-2">Belum ada file PDF yang diunggah</p>
                <label for="file_path"
                    class="inline-block px-5 py-2 bg-primary-600 text-white rounded-lg cursor-pointer hover:bg-primary-700 focus:ring-2 focus:ring-primary-400 focus:outline-none transition font-medium">
                    Pilih File PDF
                </label>
            </div>
            <div id="pdf-pagination" class="flex items-center justify-center mt-4 gap-4 hidden">
                <button type="button" id="pdf-prev"
                    class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 disabled:opacity-50">
                    Prev
                </button>
                <span class="text-base font-medium text-gray-700 dark:text-gray-300">
                    Page <span id="pdf-page-num"></span> of <span id="pdf-num-pages"></span>
                </span>
                <button type="button" id="pdf-next"
                    class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 disabled:opacity-50">
                    Next
                </button>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="bg-white dark:bg-gray-800 p-4">
            <form id="surat-form" action="{{ route('sekretaris-panitia.surat-panitia.store') }}" method="POST"
                enctype="multipart/form-data" @submit.prevent="validateAndSubmit">
                @csrf
                <div class="grid grid-cols-6 gap-6">
                    <!-- Code Field -->
                    <div class="col-span-6">
                        <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode
                            Surat <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required minlength="3"
                            maxlength="100" pattern="^[a-zA-Z0-9\s-]+$"
                            title="Kode surat harus berisi 3-100 karakter, hanya huruf, angka, spasi, dan tanda hubung"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Contoh: ABC-20240601-001">
                        @error('code')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category Field -->
                    <div class="col-span-6">
                        <label for="category_id"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori Surat <span
                                class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected' : ''
                                }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Field -->
                    <div class="col-span-6">
                        <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                            Surat <span class="text-red-500">*</span></label>
                        <input type="date" name="date" id="date" value="{{ old('date') }}" required
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        @error('date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload Field -->
                    <div class="col-span-6">
                        <label for="file_path" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">File
                            Surat <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            <div class="flex items-center justify-center w-full">
                                <label for="file_path"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF (MAX. 10MB)</p>
                                    </div>
                                </label>
                            </div>
                            <div id="file-name"
                                class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400"
                                style="display:none;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span id="file-name-text"></span>
                            </div>
                        </div>
                        @error('file_path')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Single file input for both preview and form submission -->
                    <input id="file_path" name="file_path" type="file" class="hidden" accept=".pdf"
                        onchange="handlePDFUpload(event)" />

                    <!-- Signer Button Group -->
                    <div class="col-span-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pilih Penandatangan Surat
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih satu penandatangan, lalu tempatkan
                                QR pada halaman yang diinginkan. Ulangi untuk semua penandatangan.</p>
                        </div>
                        <div class="grid gap-2 mb-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                            <template x-for="(signer, key) in signers" :key="key">
                                <button type="button" :class="[
                                        activeSignerKey === key ? 'ring-2 ring-primary-600 bg-primary-50 dark:bg-primary-900' : 'bg-white dark:bg-gray-700',
                                        'flex-1 flex flex-col items-center rounded-xl shadow p-4 transition focus:outline-none border border-gray-200 dark:border-gray-600'
                                    ]"
                                    @click="activeSignerKey = key; window.addMarker && addMarker(key, signer.label);">
                                    <span class="text-base font-medium text-gray-900 dark:text-white"
                                        x-text="signer.labelFull"></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mb-2"
                                        x-text="signer.fullname"></span>
                                    <span class="text-xs mt-1"
                                        :class="signer.isPlaced ? 'text-primary-600' : 'text-gray-400'">
                                        <template x-if="signer.isPlaced">
                                            <span>Sudah ditempatkan di halaman <span
                                                    x-text="signer.qr_page"></span></span>
                                        </template>
                                        <template x-if="!signer.isPlaced">
                                            <span>Belum ditempatkan</span>
                                        </template>
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Submit & Cancel Buttons -->
                    <div class="col-span-6 flex justify-end gap-2">
                        <a href="{{ route('sekretaris-panitia.surat-panitia.index') }}"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            Batal
                        </a>
                        <button type="button" @click="validateAndSubmit"
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <x-modals.confirm-modal id="submit-surat-modal" title="Simpan Surat"
        message="Apakah Anda yakin ingin menyimpan surat ini?" :route="route('sekretaris-panitia.surat-panitia.store')"
        confirmText="Ya, Simpan" cancelText="Batal" type="confirm">
        <button type="button" id="modal-confirm-btn">Ya, Simpan</button>
    </x-modals.confirm-modal>
</x-dashboard.page-wrapper>

<script>
    // Initialize Alpine store for PDF state
    document.addEventListener('alpine:init', () => {
        Alpine.store('pdf', {
            currentPage: 1
        });
    });

    let pdfDoc = null;
    let pageNum = 1;
    let numPages = 1;
    let pageRendering = false;
    let pageNumPending = null;
    let pdfjsLib = null;
    let currentViewport = null; // Add this to store current viewport
    let markers = [
        // Example: { key: 'ketua_umum', label: 'KU', color: '#2563eb', pdfX: 0.5, pdfY: 0.5, placed: false }
    ];

    const signerStyles = {
        sekretaris_panitia: { color: 'bg-green-100',  border: 'border-green-600',  text: 'text-green-600',  label: 'SP' },
        ketua_panitia:      { color: 'bg-indigo-100', border: 'border-indigo-600', text: 'text-indigo-600', label: 'KP' },
        ketua_umum:         { color: 'bg-blue-100',   border: 'border-blue-600',   text: 'text-blue-600',   label: 'KU' },
        pembina:            { color: 'bg-yellow-100', border: 'border-yellow-500', text: 'text-yellow-600', label: 'P' }
    };

    // Function to trigger file input click
    function triggerFileInput() {
        document.getElementById('file_path').click();
    }

    // Add click handlers to both upload areas
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handlers to both upload areas
        document.querySelectorAll('label[for="file_path"]').forEach(label => {
            label.addEventListener('click', function(e) {
                e.preventDefault();
                triggerFileInput();
            });
        });

        // Add drag and drop handlers
        const dropZones = document.querySelectorAll('.border-dashed');
        dropZones.forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('border-primary-500');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('border-primary-500');
            });

            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('border-primary-500');
                const file = e.dataTransfer.files[0];
                if (file && file.type === 'application/pdf') {
                    const input = document.getElementById('file_path');
                    input.files = e.dataTransfer.files;
                    handlePDFUpload({ target: input });
                }
            });
        });
    });

    async function handlePDFUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        if (file.type !== 'application/pdf') {
            showCustomAlert('danger', 'Validasi Gagal', ['File harus berformat PDF']);
            event.target.value = ''; // Clear the file input
            return;
        }

        // Validate file size (10MB = 10 * 1024 * 1024 bytes)
        if (file.size > 10 * 1024 * 1024) {
            showCustomAlert('danger', 'Validasi Gagal', ['Ukuran file maksimal 10MB']);
            event.target.value = ''; // Clear the file input
            return;
        }

        // Show loading spinner
        document.getElementById('pdf-loading').classList.remove('hidden');

        document.getElementById('file-name').style.display = 'flex';
        document.getElementById('file-name-text').textContent = file.name;
        const pdfUrl = URL.createObjectURL(file);

        try {
            if (!pdfjsLib) {
                pdfjsLib = await import('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.0.375/pdf.min.mjs');
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/5.0.375/pdf.worker.min.mjs';
            }
            pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
            numPages = pdfDoc.numPages;
            pageNum = 1;
            Alpine.store('pdf').currentPage = 1;
            document.getElementById('pdf-placeholder').style.display = 'none';
            document.getElementById('pdf-canvas-container').style.display = 'flex';
            document.getElementById('pdf-pagination').style.display = numPages > 1 ? 'flex' : 'none';
            renderPage(pageNum);
        } catch (error) {
            console.error('Error loading PDF:', error);
            showCustomAlert('danger', 'Validasi Gagal', ['File PDF tidak valid atau rusak']);
            event.target.value = ''; // Clear the file input
        } finally {
            // Hide loading spinner
            document.getElementById('pdf-loading').classList.add('hidden');
        }
    }

    function renderPage(num) {
        pageRendering = true;
        pdfDoc.getPage(num).then(function(page) {
            const canvas = document.getElementById('pdf-canvas');
            const container = document.getElementById('pdf-canvas-container');
            const containerWidth = container.clientWidth;
            const unscaledViewport = page.getViewport({ scale: 1 });
            const scale = containerWidth / unscaledViewport.width;
            const viewport = page.getViewport({ scale: scale });

            // Store the viewport information
            currentViewport = {
                scale: scale,
                width: unscaledViewport.width,
                height: unscaledViewport.height
            };

            const outputScale = window.devicePixelRatio || 1;
            canvas.width = Math.floor(viewport.width * outputScale);
            canvas.height = Math.floor(viewport.height * outputScale);
            canvas.style.width = Math.floor(viewport.width) + 'px';
            canvas.style.height = Math.floor(viewport.height) + 'px';

            const context = canvas.getContext('2d');
            context.setTransform(outputScale, 0, 0, outputScale, 0, 0);

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            const renderTask = page.render(renderContext);
            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
                document.getElementById('pdf-page-num').textContent = num;
                document.getElementById('pdf-num-pages').textContent = numPages;
                document.getElementById('pdf-prev').disabled = num <= 1;
                document.getElementById('pdf-next').disabled = num >= numPages;

                // Update page number in Alpine store
                Alpine.store('pdf').currentPage = num;
                renderMarkers();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('pdf-prev').addEventListener('click', function() {
            if (pageNum <= 1) return;
            if (pageRendering) {
                pageNumPending = pageNum - 1;
            } else {
                renderPage(pageNum - 1);
            }
            pageNum--;
        });
        document.getElementById('pdf-next').addEventListener('click', function() {
            if (pageNum >= numPages) return;
            if (pageRendering) {
                pageNumPending = pageNum + 1;
            } else {
                renderPage(pageNum + 1);
            }
            pageNum++;
        });
    });

    window.addEventListener('resize', function() {
        if (pdfDoc) {
            renderPage(pageNum);
        }
    });

    function qrPlacement({sekretarisPanitiaId, ketuaPanitiaId, ketuaUmumId, pembinaId}) {
        return {
            pageNum: 1,
            activeSignerKey: 'ketua_panitia', // Default active signer
            signers: {
                sekretaris_panitia: {
                    id: sekretarisPanitiaId,
                    label: 'SP',
                    labelFull: 'Sekretaris Panitia',
                    fullname: @json($sekretarisPanitia ? $sekretarisPanitia->fullname : '-'),
                    order: 1,
                    qr_page: null,
                    qr_x: null,
                    qr_y: null,
                    isPlaced: false
                },
                ketua_panitia: {
                    id: ketuaPanitiaId,
                    label: 'KP',
                    labelFull: 'Ketua Panitia',
                    fullname: @json($ketuaPanitia ? $ketuaPanitia->fullname : '-'),
                    order: 2,
                    qr_page: null,
                    qr_x: null,
                    qr_y: null,
                    isPlaced: false
                },
                ketua_umum: {
                    id: ketuaUmumId,
                    label: 'KU',
                    labelFull: 'Ketua Umum',
                    fullname: @json($users->where('role_id', 2)->where('is_active', true)->first()->fullname ?? '-'),
                    order: 3,
                    qr_page: null,
                    qr_x: null,
                    qr_y: null,
                    isPlaced: false
                },
                pembina: {
                    id: pembinaId,
                    label: 'P',
                    labelFull: 'Pembina',
                    fullname: @json($users->where('role_id', 6)->where('is_active', true)->first()->fullname ?? '-'),
                    order: 4,
                    qr_page: null,
                    qr_x: null,
                    qr_y: null,
                    isPlaced: false
                }
            },
            get selectedSigners() {
                return Object.values(this.signers).filter(s => s.isPlaced && s.id);
            },
            startDrag(e) {
                e.preventDefault();
                const signer = this.signers[this.activeSignerKey];
                const canvas = document.getElementById('pdf-canvas');
                const rect = canvas.getBoundingClientRect();

                // Mouse position relative to canvas
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                // Convert to PDF coordinates
                let offsetX = 0, offsetY = 0;
                if (signer.isPlaced) {
                    const markerCenterX = signer.qr_x * currentViewport.scale;
                    const markerCenterY = signer.qr_y * currentViewport.scale;
                    offsetX = markerCenterX - x;
                    offsetY = markerCenterY - y;
                } else {
                    signer.qr_x = x / currentViewport.scale;
                    signer.qr_y = y / currentViewport.scale;
                    signer.qr_page = Alpine.store('pdf').currentPage;
                    signer.isPlaced = true;
                }

                const onMove = (ev) => {
                    const moveX = ev.clientX - rect.left;
                    const moveY = ev.clientY - rect.top;
                    if (currentViewport) {
                        // Convert to PDF coordinates and clamp
                        let pdfX = (moveX + offsetX) / currentViewport.scale;
                        let pdfY = (moveY + offsetY) / currentViewport.scale;
                        // Clamp so marker stays inside PDF (25 is half marker size in px, convert to PDF units)
                        const markerHalf = 25 / currentViewport.scale;
                        pdfX = Math.max(markerHalf, Math.min(pdfX, currentViewport.width - markerHalf));
                        pdfY = Math.max(markerHalf, Math.min(pdfY, currentViewport.height - markerHalf));
                        signer.qr_x = pdfX;
                        signer.qr_y = pdfY;
                        signer.qr_page = Alpine.store('pdf').currentPage;
                        signer.isPlaced = true;
                    }
                };

                const onUp = () => {
                    window.removeEventListener('mousemove', onMove);
                    window.removeEventListener('mouseup', onUp);
                };

                window.addEventListener('mousemove', onMove);
                window.addEventListener('mouseup', onUp);
            },
            deleteMarker(key) {
                const signer = this.signers[key];
                signer.qr_page = null;
                signer.qr_x = null;
                signer.qr_y = null;
                signer.isPlaced = false;
            },
            validateAndSubmit(e) {
                const form = document.getElementById('surat-form');
                
                // 1. Kode Surat Validation
                const codeInput = document.getElementById('code');
                const codeValue = codeInput.value.trim();
                
                if (!codeValue) {
                    showCustomAlert('danger', 'Validasi Gagal', ['Kode surat harus diisi']);
                    return;
                }
                
                if (codeValue.length < 3) {
                    showCustomAlert('danger', 'Validasi Gagal', ['Kode surat minimal 3 karakter']);
                    return;
                }
                
                if (codeValue.length > 100) {
                    showCustomAlert('danger', 'Validasi Gagal', ['Kode surat maksimal 100 karakter']);
                    return;
                }
                
                if (!/^[a-zA-Z0-9\s-]+$/.test(codeValue)) {
                    showCustomAlert('danger', 'Validasi Gagal', ['Kode surat hanya boleh berisi huruf, angka, spasi, dan tanda hubung']);
                    return;
                }

                // 2. Kategori Surat Validation
                const categorySelect = document.getElementById('category_id');
                if (!categorySelect.value) {
                    showCustomAlert('danger', 'Validasi Gagal', ['Silakan pilih kategori surat']);
                    return;
                }

                // 3. Tanggal Surat Validation
                const dateInput = document.getElementById('date');
                if (!dateInput.value) {
                    showCustomAlert('danger', 'Validasi Gagal', ['Silakan pilih tanggal surat']);
                    return;
                }

                // Validate date range
                const selectedDate = new Date(dateInput.value);
                const minDate = new Date('2000-01-01');
                const maxDate = new Date('2100-12-31');

                if (selectedDate < minDate || selectedDate > maxDate) {
                    showCustomAlert('danger', 'Validasi Gagal', ['Tanggal surat harus antara 01 Januari 2000 dan 31 Desember 2100']);
                    return;
                }

                // 4. File Surat Validation
                const fileInput = document.getElementById('file_path');
                if (!fileInput.files || !fileInput.files.length) {
                    showCustomAlert('danger', 'Validasi Gagal', ['Silakan pilih file PDF terlebih dahulu']);
                    return;
                }

                // 5. Tanda Tangan Validation
                const ketuaPanitia = this.signers.ketua_panitia;
                const ketuaUmum = this.signers.ketua_umum;
                const pembina = this.signers.pembina;
                let missing = [];
                if (!ketuaPanitia || !ketuaPanitia.isPlaced) {
                    missing.push('QR untuk penandatangan Ketua Panitia wajib ditempatkan');
                }
                if (!ketuaUmum || !ketuaUmum.isPlaced) {
                    missing.push('QR untuk penandatangan Ketua Umum wajib ditempatkan');
                }
                if (!pembina || !pembina.isPlaced) {
                    missing.push('QR untuk penandatangan Pembina wajib ditempatkan');
                }
                if (missing.length) {
                    showCustomAlert('danger', 'Validasi Gagal', missing);
                    return;
                }

                // Remove all previous hidden inputs for signers (but NOT the file input)
                form.querySelectorAll('input[type="hidden"][data-signer-input]').forEach(input => input.remove());

                // Get only placed signers and reassign order
                const placedSigners = Object.values(this.signers).filter(s => s.isPlaced && s.id);
                placedSigners.forEach((signer, idx) => {
                    signer.order = idx + 1;
                    // Always create hidden inputs for all required fields
                    [
                        ['id', signer.id],
                        ['order', signer.order],
                        ['qr_page', signer.qr_page],
                        ['qr_x', signer.qr_x],
                        ['qr_y', signer.qr_y]
                    ].forEach(([field, value]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `signers[${idx}][${field}]`;
                        input.value = value;
                        input.setAttribute('data-signer-input', '1');
                        form.appendChild(input);
                    });
                });

                addMarkerInputsToForm(form);

                // Use native confirm dialog
                if (confirm('Apakah Anda yakin ingin menyimpan surat ini?')) {
                    form.submit();
                }
            },
            showAlert(type, message) {
                alert(message);
            }
        }
    }

    function syncMarkersToAlpine() {
        if (!window.Alpine) return;
        const alpineData = Alpine && Alpine.$data && Alpine.$data(document.querySelector('[x-data]'));
        if (!alpineData) return;
        Object.keys(alpineData.signers).forEach(key => {
            const marker = markers.find(m => m.key === key && m.placed);
            if (marker) {
                alpineData.signers[key].isPlaced = true;
                alpineData.signers[key].qr_page = marker.qr_page;
            } else {
                alpineData.signers[key].isPlaced = false;
                alpineData.signers[key].qr_page = null;
            }
        });
    }

    function renderMarkers() {
        const overlay = document.getElementById('marker-overlay');
        overlay.innerHTML = '';
        const canvas = document.getElementById('pdf-canvas');
        const width = canvas.width / (window.devicePixelRatio || 1);
        const height = canvas.height / (window.devicePixelRatio || 1);
        // Responsive marker size: 10% of canvas width, min 32px, max 64px
        const markerSize = Math.max(32, Math.min(64, width * 0.1));
        const currentPage = Alpine.store('pdf').currentPage;
        markers.forEach(marker => {
            if (!marker.placed || marker.qr_page !== currentPage) return;
            const style = signerStyles[marker.key] || { color: 'bg-gray-100', border: 'border-gray-400', text: 'text-gray-600', label: marker.label || '' };
            const markerDiv = document.createElement('div');
            markerDiv.className = `marker-qr group absolute z-30 cursor-move bg-transparent ${style.border} border-2 rounded-lg shadow-lg flex items-center justify-center transition hover:scale-105`;
            markerDiv.style.position = 'absolute';
            markerDiv.style.left = (marker.pdfX * width - 24) + 'px'; // 48/2
            markerDiv.style.top = (marker.pdfY * height - 24) + 'px';
            markerDiv.style.width = markerDiv.style.height = '48px';
            markerDiv.style.pointerEvents = 'auto';

            markerDiv.innerHTML = `
              <div class="flex flex-col items-center justify-center w-full h-full">
                <div class="rounded-full ${style.color} ${style.border} flex items-center justify-center" style="width:32px;height:32px;">
                  <svg class="w-5 h-5 ${style.text}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4Zm10 10h6v6h-6v-6Zm0-10h6v6h-6V4Zm-4 10h.01v.01H10V14Zm0 4h.01v.01H10V18Zm-3 2h.01v.01H7V20Zm0-4h.01v.01H7V16Zm-3 2h.01v.01H4V18Zm0-4h.01v.01H4V14Z"/>
                    <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M7 7h.01v.01H7V7Zm10 10h.01v.01H17V17Z"/>
                  </svg>
                </div>
                <span class="mt-1 text-xs font-bold ${style.text}">${style.label}</span>
              </div>
              <button type="button" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow transition hover:bg-red-700" onclick="deleteMarker('${marker.key}')">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 20 20">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M6 14L14 6"/>
                </svg>
              </button>
            `;
            markerDiv.addEventListener('mousedown', (e) => startMarkerDrag(e, marker, 48));
            markerDiv.addEventListener('touchstart', (e) => startMarkerDrag(e, marker, 48), { passive: false });
            overlay.appendChild(markerDiv);
        });
        syncMarkersToAlpine();
    }

    function startMarkerDrag(e, marker, markerSize) {
        e.preventDefault();
        const canvas = document.getElementById('pdf-canvas');
        const width = canvas.width / (window.devicePixelRatio || 1);
        const height = canvas.height / (window.devicePixelRatio || 1);
        const currentPage = Alpine.store('pdf').currentPage;

        // Support both mouse and touch
        const getXY = ev => {
            if (ev.touches && ev.touches.length) {
                return { x: ev.touches[0].clientX, y: ev.touches[0].clientY };
            }
            return { x: ev.clientX, y: ev.clientY };
        };
        const start = getXY(e);
        const origPdfX = marker.pdfX;
        const origPdfY = marker.pdfY;

        function onMove(ev) {
            ev.preventDefault();
            const move = getXY(ev);
            const dx = move.x - start.x;
            const dy = move.y - start.y;
            let newPdfX = origPdfX + dx / width;
            let newPdfY = origPdfY + dy / height;
            // Clamp so marker stays inside PDF (half marker size in PDF units)
            const markerHalf = markerSize / 2 / width;
            newPdfX = Math.max(markerHalf, Math.min(newPdfX, 1 - markerHalf));
            newPdfY = Math.max(markerHalf, Math.min(newPdfY, 1 - markerHalf));
            marker.pdfX = newPdfX;
            marker.pdfY = newPdfY;
            marker.qr_page = currentPage;
            renderMarkers();
        }
        function onUp(ev) {
            window.removeEventListener('mousemove', onMove);
            window.removeEventListener('mouseup', onUp);
            window.removeEventListener('touchmove', onMove);
            window.removeEventListener('touchend', onUp);
        }
        window.addEventListener('mousemove', onMove);
        window.addEventListener('mouseup', onUp);
        window.addEventListener('touchmove', onMove, { passive: false });
        window.addEventListener('touchend', onUp);
    }

    // Example: Add a marker when a button is clicked
    function addMarker(key, label) {
        // Remove existing marker for this key
        markers = markers.filter(m => m.key !== key);
        // Place in center by default
        const currentPage = Alpine.store('pdf').currentPage;
        markers.push({ key, label, pdfX: 0.5, pdfY: 0.5, placed: true, qr_page: currentPage });
        renderMarkers();
    }

    // On PDF render or resize, call renderMarkers()
    window.addEventListener('resize', renderMarkers);

    function addMarkerInputsToForm(form) {
        // Remove old marker inputs
        form.querySelectorAll('input[data-marker-input]').forEach(i => i.remove());
        // Get actual PDF width/height in points (from currentViewport)
        const pdfWidth = currentViewport ? currentViewport.width : 1;
        const pdfHeight = currentViewport ? currentViewport.height : 1;
        // Add new ones in the format the backend expects
        markers.filter(m => m.placed).forEach((marker, idx) => {
            // Find the Alpine signer for this marker
            const alpineData = Alpine && Alpine.$data && Alpine.$data(document.querySelector('[x-data]'));
            const signer = alpineData && alpineData.signers[marker.key];
            if (!signer) return;
            [
                ['id', signer.id],
                ['order', idx + 1],
                ['qr_page', marker.qr_page],
                ['qr_x', marker.pdfX * pdfWidth], // convert to points
                ['qr_y', marker.pdfY * pdfHeight] // convert to points
            ].forEach(([field, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `signers[${idx}][${field}]`;
                input.value = value;
                input.setAttribute('data-marker-input', '1');
                form.appendChild(input);
            });
        });
    }

    // Add event listener for form submission after confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('surat-form');
        const modal = document.querySelector('#submit-surat-modal');
        if (modal) {
            const confirmBtn = document.getElementById('modal-confirm-btn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    form.submit();
                });
            }
        }
    });

    function deleteMarker(key) {
        markers = markers.filter(m => m.key !== key);
        renderMarkers();
    }

    function showCustomAlert(type, title, messages) {
        // Remove any existing alert
        document.querySelectorAll('.custom-inline-alert').forEach(e => e.remove());

        // Build the alert HTML
        const color = {
            info: 'blue',
            danger: 'red',
            error: 'red',
            warning: 'yellow',
            success: 'green'
        }[type] || 'blue';

        const ul = messages.map(msg => `<li>${msg}</li>`).join('');
        const alertDiv = document.createElement('div');
        alertDiv.className = `custom-inline-alert flex p-4 mb-4 text-sm text-${color}-800 rounded-lg bg-${color}-50 dark:bg-gray-700 dark:text-${color}-400`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
          <svg class="shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
          </svg>
          <span class="sr-only">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
          <div>
            <span class="font-medium">${title}</span>
            <ul class="mt-1.5 list-disc list-inside">${ul}</ul>
          </div>
        `;
        // Insert at the top of the form
        document.getElementById('surat-form').prepend(alertDiv);
    }
</script>
@endsection