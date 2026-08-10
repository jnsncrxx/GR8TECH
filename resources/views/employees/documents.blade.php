@extends('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employees.documents'])

@section('title', 'Employee Documents')

@section('content')
<div class="max-w-7xl mx-auto space-y-4" x-data="{ 
        searchQuery: '',
        searchResults: [],
        isSearching: false,
        showResults: false,
        
        async searchEmployees() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                this.showResults = false;
                return;
            }
            this.isSearching = true;
            try {
                const response = await fetch('{{ route('employees.info.search') }}?query=' + encodeURIComponent(this.searchQuery));
                this.searchResults = await response.json();
                this.showResults = true;
            } catch (error) {
                console.error('Search failed:', error);
            }
            this.isSearching = false;
        },
        
        selectEmployee(id) {
            window.location.href = '{{ route('employees.documents') }}?employee_id=' + id;
        }
    }">
    
    <!-- Search Bar Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-4">
        <div class="relative w-full max-w-md">
            <div class="relative">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    @input.debounce.300ms="searchEmployees" 
                    @focus="searchQuery.length >= 2 ? showResults = true : null" 
                    @click.outside="showResults = false"
                    placeholder="Search employee or employee no." 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                >
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Search Results Dropdown -->
            <div x-show="showResults" x-transition style="display: none;" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                <template x-if="isSearching">
                    <div class="p-3 text-sm text-gray-500 text-center">Searching...</div>
                </template>
                <template x-if="!isSearching && searchResults.length === 0">
                    <div class="p-3 text-sm text-gray-500 text-center">No employees found.</div>
                </template>
                <template x-for="emp in searchResults" :key="emp.id">
                    <div @click="selectEmployee(emp.id)" class="p-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer flex flex-col">
                        <span class="font-medium text-sm text-gray-900" x-text="emp.last_name + ', ' + emp.first_name"></span>
                        <span class="text-xs text-gray-500" x-text="'Emp No: ' + emp.employee_id"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md border border-green-200 text-sm">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-3 rounded-md border border-red-200 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($selectedEmployee)
    <style>
        .docs-container {
            background-color: #ffffff;
            color: #374151;
            border-radius: 8px;
            padding: 24px;
            min-height: 50vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
            margin-top: 24px;
        }
        .docs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .docs-btn {
            background-color: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: background-color 0.2s;
        }
        .docs-btn:hover {
            background-color: #f9fafb;
        }
        .docs-btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid transparent;
        }
        .docs-btn-primary:hover {
            background-color: #1d4ed8;
        }
        .docs-group {
            margin-bottom: 32px;
        }
        .docs-group-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        .docs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 20px 16px;
        }
        .doc-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.1s;
        }
        .doc-item:hover {
            transform: translateY(-2px);
        }
        .doc-icon-wrapper {
            width: 80px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
        }
        .doc-icon-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .doc-icon-wrapper i {
            font-size: 40px;
        }
        .doc-icon-wrapper.folder {
            background: transparent;
            height: 80px;
            border: none;
        }
        .doc-icon-wrapper.folder i {
            font-size: 80px;
            color: #fcd34d;
        }
        .doc-name {
            font-size: 12px;
            color: #374151;
            word-break: break-word;
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.2;
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            background: #ffffff;
            border-radius: 8px;
            padding: 32px;
            width: 450px;
            color: #374151;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <!-- Header Identification -->
        <div class="flex items-center gap-6 mb-4 border-b border-gray-200 pb-4">
            <div class="flex items-center gap-2 flex-1 max-w-sm">
                <label class="font-semibold text-sm text-gray-700 w-16">Name:</label>
                <input type="text" value="{{ $selectedEmployee->last_name }}, {{ $selectedEmployee->first_name }}" class="flex-1 text-sm border border-gray-300 rounded px-3 py-1.5 bg-gray-100 font-medium" readonly>
            </div>
            <div class="flex items-center gap-2 w-48">
                <label class="font-semibold text-sm text-gray-700">Empno:</label>
                <input type="text" value="{{ $selectedEmployee->employee_id }}" class="flex-1 text-sm border border-gray-300 rounded px-3 py-1.5 bg-gray-100 font-medium" readonly>
            </div>
        </div>

        <div class="docs-container">
            <div class="docs-header">
                <div style="display:flex; gap: 12px;">
                    <button type="button" class="docs-btn" onclick="document.getElementById('folderModal').classList.add('active')">
                        <i class="fas fa-folder-plus text-gray-500"></i> Add Folder
                    </button>
                </div>
                <div>
                    <button type="button" class="docs-btn docs-btn-primary" onclick="document.getElementById('uploadModal').classList.add('active')">
                        <i class="fas fa-upload"></i> Upload Document
                    </button>
                </div>
            </div>

            {{-- Real Folders Row --}}
            @if(isset($folders) && $folders->isNotEmpty())
                <div class="docs-group">
                    <div class="docs-group-title">
                        <i class="fas fa-folder text-xs text-yellow-400"></i> Folders
                    </div>
                    <div class="docs-grid">
                        @foreach($folders as $folder)
                            <div class="doc-item" style="position:relative;">
                                <button
                                    onclick="if(confirm('Delete folder \'{{ addslashes($folder->name) }}\'? Files inside will be moved to root.')) { document.getElementById('del-folder-form-{{ $folder->id }}').submit(); }"
                                    style="position:absolute;top:-6px;right:10px;background:#fff;border:1px solid #e5e7eb;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:10px;color:#9ca3af;z-index:1;"
                                    title="Delete folder"
                                >&times;</button>
                                <form id="del-folder-form-{{ $folder->id }}" method="POST" action="{{ route('employees.documents.folders.delete', $folder->id) }}" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <div class="doc-icon-wrapper folder" onclick="document.getElementById('emp-folder-contents-{{ $folder->id }}').classList.toggle('hidden')">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <div class="doc-name" title="{{ $folder->name }}">
                                    {{ $folder->name }}
                                    <span class="text-gray-400 text-xs">({{ $folder->documents->count() }})</span>
                                </div>

                                {{-- Folder contents popup --}}
                                <div id="emp-folder-contents-{{ $folder->id }}" class="hidden" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:20px;box-shadow:0 10px 30px rgba(0,0,0,0.15);z-index:200;min-width:360px;max-width:90vw;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;border-bottom:1px solid #e5e7eb;padding-bottom:8px;">
                                        <span style="font-weight:600;color:#374151;font-size:15px;"><i class="fas fa-folder text-yellow-400 mr-2"></i>{{ $folder->name }}</span>
                                        <button onclick="document.getElementById('emp-folder-contents-{{ $folder->id }}').classList.add('hidden')" style="border:none;background:none;cursor:pointer;font-size:18px;color:#9ca3af;">&times;</button>
                                    </div>
                                    @if($folder->documents->isNotEmpty())
                                        <div class="docs-grid" style="min-width:300px;">
                                            @foreach($folder->documents as $fdoc)
                                                <a href="{{ asset('storage/' . $fdoc->path) }}" target="_blank" class="doc-item">
                                                    <div class="doc-icon-wrapper">
                                                        @php $ft = strtolower($fdoc->type); @endphp
                                                        @if(in_array($ft, ['jpg','jpeg','png','gif','webp']))
                                                            <img src="{{ asset('storage/' . $fdoc->path) }}" alt="{{ $fdoc->name }}">
                                                        @elseif($ft == 'pdf')
                                                            <i class="fas fa-file-pdf" style="color:#e2574c;"></i>
                                                        @elseif(in_array($ft, ['doc','docx']))
                                                            <i class="fas fa-file-word" style="color:#2b579a;"></i>
                                                        @elseif(in_array($ft, ['xls','xlsx']))
                                                            <i class="fas fa-file-excel" style="color:#217346;"></i>
                                                        @else
                                                            <i class="fas fa-file" style="color:#7f8c8d;"></i>
                                                        @endif
                                                    </div>
                                                    <div class="doc-name" title="{{ $fdoc->name }}">{{ $fdoc->name }}</div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p style="color:#9ca3af;text-align:center;padding:20px 0;">This folder is empty.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(isset($documentsByDate) && $documentsByDate->isNotEmpty())
                @foreach($documentsByDate as $dateLabel => $docs)
                    <div class="docs-group">
                        <div class="docs-group-title">
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i> {{ $dateLabel }}
                        </div>
                        <div class="docs-grid">
                            @foreach($docs as $doc)
                                <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" class="doc-item">
                                    <div class="doc-icon-wrapper">
                                        @php
                                            $type = strtolower($doc->type);
                                            $isImage = in_array($type, ['jpg','jpeg','png','gif','webp']);
                                        @endphp
                                        
                                        @if($isImage)
                                            <img src="{{ asset('storage/' . $doc->path) }}" alt="{{ $doc->name }}">
                                        @elseif($type == 'pdf')
                                            <i class="fas fa-file-pdf" style="color: #e2574c;"></i>
                                        @elseif(in_array($type, ['doc','docx']))
                                            <i class="fas fa-file-word" style="color: #2b579a;"></i>
                                        @elseif(in_array($type, ['xls','xlsx']))
                                            <i class="fas fa-file-excel" style="color: #217346;"></i>
                                        @elseif(in_array($type, ['zip','rar']))
                                            <i class="fas fa-file-archive" style="color: #f7b924;"></i>
                                        @elseif(in_array($type, ['php','js','css','html']))
                                            <i class="fas fa-code" style="color: #8c7ae6;"></i>
                                        @elseif(str_starts_with($type, 'document_'))
                                            {{-- Legacy documents --}}
                                            <i class="fas fa-file-alt" style="color: #8c7ae6;"></i>
                                        @else
                                            <i class="fas fa-file" style="color: #7f8c8d;"></i>
                                        @endif
                                    </div>
                                    <div class="doc-name" title="{{ $doc->name }}">{{ $doc->name }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @elseif(!isset($folders) || $folders->isEmpty())
                <div class="text-center text-gray-500 mt-16 pb-8 border-t border-gray-100 pt-8">
                    <i class="fas fa-file-alt text-5xl text-gray-200 mb-4 block"></i>
                    <p>No documents uploaded for this employee yet. Click "Upload Document" to start.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Folder Modal -->
    <div class="modal-overlay" id="folderModal">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-2 text-gray-900"><i class="fas fa-folder-plus text-yellow-400 mr-2"></i>New Folder</h2>
            <p class="text-gray-500 text-sm mb-6">Enter a name for the new folder.</p>
            
            <form method="POST" action="{{ route('employees.documents.folders.create') }}">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Folder Name</label>
                    <input type="text" name="name" required autofocus placeholder="e.g. Certificates"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
                <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <button type="button" class="px-5 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="document.getElementById('folderModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 rounded-md text-sm font-medium text-white hover:bg-blue-700 transition-colors">Create Folder</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal-overlay" id="uploadModal">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-2 text-gray-900">Upload Document</h2>
            <p class="text-gray-500 text-sm mb-6">Select one or multiple files to upload.</p>
            
            <div id="uploadErrorContainerEmp" class="hidden bg-red-50 text-red-700 p-3 rounded-md border border-red-200 text-sm mb-4"></div>

            <form method="POST" action="{{ route('employees.documents.save') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $selectedEmployee->id }}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Save to Folder (optional)</label>
                    <select name="folder_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="">-- Root (no folder) --</option>
                        @if(isset($folders))
                            @foreach($folders as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Files</label>
                    <input type="file" name="documents[]" multiple required class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 file:cursor-pointer
                    " onchange="validateFileSize(this)">
                </div>
                <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <button type="button" class="px-5 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" onclick="document.getElementById('uploadModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 rounded-md text-sm font-medium text-white hover:bg-blue-700 transition-colors">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Close modals on clicking overlay -->
    <script>
        ['uploadModal', 'folderModal'].forEach(function(id) {
            document.getElementById(id).addEventListener('click', function(e) {
                if(e.target === this) {
                    this.classList.remove('active');
                }
            });
        });

        function validateFileSize(input) {
            const maxSize = 2 * 1024 * 1024; // 2MB
            const errorContainer = document.getElementById('uploadErrorContainerEmp');
            if (errorContainer) {
                errorContainer.classList.add('hidden');
                errorContainer.innerText = '';
            }

            if (input.files) {
                for (let i = 0; i < input.files.length; i++) {
                    if (input.files[i].size > maxSize) {
                        if (errorContainer) {
                            errorContainer.innerText = 'The document must not exceed 2 MB.';
                            errorContainer.classList.remove('hidden');
                        }
                        input.value = ''; // Clear the selected files
                        return;
                    }
                }
            }
        }
    </script>
    @else
        <!-- No employee selected placeholder -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <i class="fas fa-file-alt text-6xl mb-4 text-gray-300"></i>
            <p class="text-lg">Please search and select an employee above to manage their documents.</p>
        </div>
    @endif
</div>
@endsection
