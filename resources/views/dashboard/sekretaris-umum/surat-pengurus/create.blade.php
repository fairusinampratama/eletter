@extends('layouts.dashboard')

@section('content')
<x-alerts.flash-messages />

<x-dashboard.page-wrapper :title="$title"
    :breadcrumbItems="[['label' => 'Surat Pengurus', 'url' => route('sekretaris-umum.surat-pengurus.index')], ['label' => 'Tambah Surat']]">
    <div x-data="qrPlacement({
        sekretarisUmumId: {{ $users->where('role_id', 3)->first()->id ?? 'null' }},
        ketuaUmumId: {{ $users->where('role_id', 2)->first()->id }},
        pembinaId: {{ $users->where('role_id', 6)->first()->id ?? 'null' }}
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
                class="w-full max-w-xl flex items-center justify-center border-2 border-dashed border-gray-500 dark:border-gray-600 rounded-lg p-2 hidden">
                <canvas id="pdf-canvas" class="max-w-full h-auto w-full"></canvas>
                <!-- QR markers for each signer -->
                <template x-for="(signer, key) in signers" :key="key">
                    <div x-show="(signer.isPlaced && signer.qr_page === $store.pdf.currentPage) || (!signer.isPlaced && activeSignerKey === key)"
                        :style="signer.isPlaced
                            ? 'position: absolute; left: ' + signer.qr_x + 'px; top: ' + signer.qr_y + 'px;'
                            : (activeSignerKey === key ? 'position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);' : '')"
                        class="w-[50px] h-[50px] bg-white border-2 border-primary-600 z-20 flex items-center justify-center group"
                        :class="activeSignerKey === key ? 'cursor-move ring-2 ring-primary-400' : 'opacity-60 cursor-not-allowed'"
                        @mousedown="activeSignerKey === key ? startDrag($event) : null">
                        <div class="w-full h-full flex items-center justify-center bg-primary-600/10">
                            <span x-text="signer.label" class="text-sm font-medium text-primary-600"></span>
                        </div>
                        <div
                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                            <span x-text="signer.labelFull"></span> - Page <span x-text="signer.qr_page"></span>
                        </div>
                        <!-- Delete button for active marker -->
                        <button x-show="activeSignerKey === key && signer.isPlaced" type="button"
                            class="absolute -top-2 -right-2 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs shadow hover:bg-red-700 focus:outline-none"
                            @click.stop="deleteMarker(key)">
                            ×
                        </button>
                    </div>
                </template>
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
            <form action="{{ route('sekretaris-umum.surat-pengurus.store') }}" method="POST"
                enctype="multipart/form-data" @submit.prevent="validateAndSubmit">
                @csrf
                <div class="grid grid-cols-6 gap-6">
                    <!-- Code Field -->
                    <div class="col-span-6">
                        <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode
                            Surat <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required
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
                        <div class="flex gap-2 mb-4">
                            <template x-for="(signer, key) in signers" :key="key">
                                <button type="button" :class="[
                                        activeSignerKey === key ? 'ring-2 ring-primary-600 bg-primary-50 dark:bg-primary-900' : 'bg-white dark:bg-gray-700',
                                        'flex-1 flex flex-col items-center rounded-xl shadow p-4 transition focus:outline-none border border-gray-200 dark:border-gray-600'
                                    ]" @click="activeSignerKey = key">
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

                    <!-- Hidden inputs for form submission -->
                    <template x-for="(signer, key) in signers" :key="key">
                        <template x-if="signer.isPlaced && signer.id">
                            <div>
                                <input type="hidden" :name="`signers[${key}][id]`" :value="signer.id">
                                <input type="hidden" :name="`signers[${key}][order]`" :value="signer.order">
                                <input type="hidden" :name="`signers[${key}][qr_page]`" :value="signer.qr_page">
                                <input type="hidden" :name="`signers[${key}][qr_x]`" :value="signer.qr_x">
                                <input type="hidden" :name="`signers[${key}][qr_y]`" :value="signer.qr_y">
                            </div>
                        </template>
                    </template>

                    <!-- Submit & Cancel Buttons -->
                    <div class="col-span-6 flex justify-end gap-2">
                        <a href="{{ route('sekretaris-umum.surat-pengurus.index') }}"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            Batal
                        </a>
                        <button type="submit"
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
            alert('Error loading PDF. Please try again.');
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

    function qrPlacement({sekretarisUmumId, ketuaUmumId, pembinaId}) {
        return {
            pageNum: 1,
            activeSignerKey: 'ketua_umum', // Default active signer
            signers: {
                sekretaris_umum: {
                    id: sekretarisUmumId,
                    label: 'SU',
                    labelFull: 'Sekretaris Umum',
                    fullname: @json($users->where('role_id', 3)->first()->fullname ?? '-'),
                    order: 1,
                    qr_page: null,
                    qr_x: null,
                    qr_y: null,
                    isPlaced: false
                },
                ketua_umum: {
                    id: ketuaUmumId,
                    label: 'KU',
                    labelFull: 'Ketua Umum',
                    fullname: @json($users->where('role_id', 2)->first()->fullname ?? '-'),
                    order: 2,
                    qr_page: null,
                    qr_x: null,
                    qr_y: null,
                    isPlaced: false
                },
                pembina: {
                    id: pembinaId,
                    label: 'P',
                    labelFull: 'Pembina',
                    fullname: @json($users->where('role_id', 6)->first()->fullname ?? '-'),
                    order: 3,
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
                // If not placed, start from center
                if (!signer.isPlaced) {
                    signer.qr_x = rect.width/2;
                    signer.qr_y = rect.height/2;
                    signer.qr_page = Alpine.store('pdf').currentPage;
                }
                const onMove = (ev) => {
                    const x = ev.clientX - rect.left;
                    const y = ev.clientY - rect.top;
                    // Store center coordinates of the square
                    signer.qr_x = Math.max(25, Math.min(x, rect.width - 25));
                    signer.qr_y = Math.max(25, Math.min(y, rect.height - 25));
                    signer.qr_page = Alpine.store('pdf').currentPage;
                    signer.isPlaced = true;
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
                const form = e.target;
                // File input validation
                const fileInput = document.getElementById('file_path');
                if (!fileInput.files || !fileInput.files.length) {
                    alert('Silakan pilih file PDF terlebih dahulu.');
                    return;
                }
                // Only Ketua Umum is required, others are optional
                const ketuaUmum = Object.values(this.signers).find(s => s.labelFull === 'Ketua Umum');
                if (!ketuaUmum || !ketuaUmum.isPlaced) {
                    alert('QR untuk penandatangan Ketua Umum wajib ditempatkan.');
                    return;
                }
                form.submit();
            }
        }
    }
</script>
@endsection