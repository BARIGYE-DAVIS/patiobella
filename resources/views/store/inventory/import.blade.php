@extends('layouts.store')

@section('title', 'Import Inventory Items')
@section('page-title', 'Import Inventory Items')

@section('content')
<style>
    .dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .dropzone:hover {
        border-color: #3b82f6;
        background-color: #f8fafc;
    }
    .dropzone.dragover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    .template-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
    }
    .instruction-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
    }
    .result-item {
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 8px;
    }
    .result-success {
        background-color: #dcfce7;
        border-left: 4px solid #22c55e;
    }
    .result-error {
        background-color: #fee2e2;
        border-left: 4px solid #ef4444;
    }
    .column-badge {
        display: inline-block;
        background-color: #e2e8f0;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        color: #475569;
        margin: 2px;
    }
    .required-star {
        color: #ef4444;
    }
</style>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-import text-blue-600"></i>
                Import Inventory Items
            </h3>
            <p class="text-xs text-gray-500 mt-1">Bulk import inventory items using CSV file</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Template & Instructions -->
        <div class="space-y-6">
            <!-- Download Template Card -->
            <div class="template-card rounded-xl p-5">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-download text-green-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 mb-1">Step 1: Download Template</h4>
                        <p class="text-sm text-gray-600 mb-3">Download the CSV template with the correct column format</p>
                        <a href="{{ route('store.inventory.import.template') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                </div>
            </div>

            <!-- Instructions Card -->
            <div class="instruction-card rounded-xl p-5">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 mb-2">Template Instructions</h4>
                        <div class="text-sm text-gray-700 space-y-3">
                            <p><span class="font-semibold text-red-600">*</span> Required columns: <strong>name</strong>, <strong>category</strong>, <strong>uom</strong>, <strong>quantity</strong></p>

                            <div class="bg-white/50 rounded-lg p-3">
                                <p class="font-semibold text-xs text-gray-600 mb-2">Column Format:</p>
                                <table class="w-full text-xs">
                                    <tr class="border-b border-gray-200">
                                        <td class="py-1 font-semibold">name</td>
                                        <td class="py-1">Item name <span class="required-star">*</span></td>
                                        <td class="py-1 text-gray-500">e.g., Chicken Breast</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-1 font-semibold">category</td>
                                        <td class="py-1">Category name <span class="required-star">*</span></td>
                                        <td class="py-1 text-gray-500">e.g., Meat, Vegetables</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-1 font-semibold">uom</td>
                                        <td class="py-1">Unit of Measurement <span class="required-star">*</span></td>
                                        <td class="py-1 text-gray-500">e.g., kg, litre, piece, bottle</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="py-1 font-semibold">quantity</td>
                                        <td class="py-1">Initial stock quantity <span class="required-star">*</span></td>
                                        <td class="py-1 text-gray-500">e.g., 100</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 font-semibold">expiry_date</td>
                                        <td class="py-1">Expiry date (optional)</td>
                                        <td class="py-1 text-gray-500">Format: YYYY-MM-DD</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mt-2">
                                <p class="font-semibold text-xs text-gray-600 mb-1">What happens during import:</p>
                                <ul class="text-xs text-gray-600 list-disc list-inside space-y-1">
                                    <li><strong>Category</strong> - Created automatically if it doesn't exist</li>
                                    <li><strong>UOM</strong> - Created in units_of_measure table if not exists</li>
                                    <li><strong>Batch Number</strong> - Generated automatically by system</li>
                                    <li><strong>Stock Movement</strong> - Created for audit trail</li>
                                    <li><strong>Duplicate items</strong> - If item exists, new batch is added to existing item</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Upload Section -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-upload text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">Step 2: Upload File</h4>
                    <p class="text-sm text-gray-500">Upload your completed CSV file</p>
                </div>
            </div>

            <form id="importForm" enctype="multipart/form-data">
                @csrf

                <div id="dropzone" class="dropzone mb-4">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3 block"></i>
                    <p class="text-gray-600 mb-2">Drag & drop your CSV file here or click to browse</p>
                    <p class="text-xs text-gray-400">Supported format: .csv (Max 5MB)</p>
                    <input type="file" name="file" id="fileInput" class="hidden" accept=".csv">
                </div>

                <div id="fileInfo" class="hidden mb-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-csv text-green-600"></i>
                            <span id="fileName" class="text-sm text-gray-700"></span>
                            <span id="fileSize" class="text-xs text-gray-400"></span>
                        </div>
                        <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="uploadBtn" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-upload"></i> Upload & Import
                </button>
            </form>

            <!-- Progress Bar -->
            <div id="progressContainer" class="hidden mt-4">
                <div class="flex justify-between text-xs text-gray-600 mb-1">
                    <span>Importing...</span>
                    <span id="progressPercent">0%</span>
                </div>
                <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div id="progressBar" class="bg-blue-600 rounded-full h-2 transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div id="resultsSection" class="hidden bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-simple"></i>
                Import Results
            </h4>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800" id="totalCount">0</p>
                    <p class="text-xs text-gray-500">Total Rows</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600" id="successCount">0</p>
                    <p class="text-xs text-green-600">Successful</p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600" id="failedCount">0</p>
                    <p class="text-xs text-red-600">Failed</p>
                </div>
            </div>
            <div id="errorsList" class="space-y-2 max-h-60 overflow-y-auto"></div>
        </div>
    </div>
</div>

<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFile = document.getElementById('removeFile');
    const uploadBtn = document.getElementById('uploadBtn');
    const importForm = document.getElementById('importForm');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const resultsSection = document.getElementById('resultsSection');

    let selectedFile = null;

    // Click dropzone to open file picker
    dropzone.addEventListener('click', () => fileInput.click());

    // Drag and drop events
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFile(e.target.files[0]);
        }
    });

    function handleFile(file) {
        const fileExt = file.name.split('.').pop().toLowerCase();

        if (fileExt !== 'csv') {
            alert('Please upload a valid CSV file');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }

        selectedFile = file;
        fileName.innerText = file.name;
        fileSize.innerText = '(' + (file.size / 1024).toFixed(2) + ' KB)';
        fileInfo.classList.remove('hidden');
        dropzone.style.borderColor = '#3b82f6';
    }

    removeFile.addEventListener('click', () => {
        selectedFile = null;
        fileInput.value = '';
        fileInfo.classList.add('hidden');
        dropzone.style.borderColor = '#cbd5e1';
    });

    // Form submission
    importForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!selectedFile) {
            alert('Please select a file to upload');
            return;
        }

        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('_token', '{{ csrf_token() }}');

        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
        progressContainer.classList.remove('hidden');
        resultsSection.classList.add('hidden');

        let progress = 0;
        const progressInterval = setInterval(() => {
            progress = Math.min(progress + 10, 90);
            progressBar.style.width = progress + '%';
            progressPercent.innerText = progress + '%';
        }, 500);

        try {
            const response = await fetch('{{ route("store.inventory.import.process") }}', {
                method: 'POST',
                body: formData
            });

            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            progressPercent.innerText = '100%';

            const data = await response.json();

            setTimeout(() => {
                progressContainer.classList.add('hidden');
                progressBar.style.width = '0%';

                if (data.success) {
                    displayResults(data.results);
                } else {
                    alert(data.message || 'Import failed');
                }

                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload & Import';
            }, 500);

        } catch (error) {
            clearInterval(progressInterval);
            progressContainer.classList.add('hidden');
            alert('An error occurred during import');
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload & Import';
        }
    });

    function displayResults(results) {
        document.getElementById('totalCount').innerText = results.total || 0;
        document.getElementById('successCount').innerText = results.success || 0;
        document.getElementById('failedCount').innerText = results.failed || 0;

        const errorsList = document.getElementById('errorsList');
        errorsList.innerHTML = '';

        if (results.errors && results.errors.length > 0) {
            results.errors.forEach(error => {
                errorsList.innerHTML += `
                    <div class="result-item result-error">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-sm text-red-700">${escapeHtml(error)}</span>
                    </div>
                `;
            });
        } else if (results.success > 0) {
            errorsList.innerHTML = `
                <div class="result-item result-success">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span class="text-sm text-green-700">All items imported successfully!</span>
                </div>
            `;
        }

        resultsSection.classList.remove('hidden');

        if (results.success > 0) {
            selectedFile = null;
            fileInput.value = '';
            fileInfo.classList.add('hidden');
            dropzone.style.borderColor = '#cbd5e1';
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>a
@endsection
