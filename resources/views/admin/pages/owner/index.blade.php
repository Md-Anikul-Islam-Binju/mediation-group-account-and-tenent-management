@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Mediation Group Account Management</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Owner</a></li>
                        <li class="breadcrumb-item active">Owner!</li>
                    </ol>
                </div>
                <h4 class="page-title">Owner!</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('owner-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewModalId">
                            Add New
                        </button>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Agreements</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($owners as $key => $owner)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $owner->name }}</td>
                            <td>{{ $owner->email ?? 'N/A' }}</td>
                            <td>{{ $owner->phone ?? 'N/A' }}</td>

{{--                            <td>--}}
{{--                                @if(!empty($owner->agreement_paper) && is_array($owner->agreement_paper))--}}
{{--                                    @foreach($owner->agreement_paper as $key => $file)--}}
{{--                                        @php--}}
{{--                                            if ($key === 0) {--}}
{{--                                                $label = 'Main Copy';--}}
{{--                                            } else {--}}
{{--                                                $label = 'Extension Copy ' . $key;--}}
{{--                                            }--}}
{{--                                        @endphp--}}

{{--                                        <a href="{{ asset('images/owner_agreements/' . $file) }}"--}}
{{--                                           target="_blank"--}}
{{--                                           class="badge bg-info mb-1">--}}
{{--                                            {{ $label }}--}}
{{--                                        </a>--}}
{{--                                    @endforeach--}}
{{--                                @else--}}
{{--                                    N/A--}}
{{--                                @endif--}}
{{--                            </td>--}}

                            <td>
                                @if(!empty($owner->agreement_paper) && is_array($owner->agreement_paper))
                                    @foreach($owner->agreement_paper as $key => $file)
                                        @php
                                            $label = $key === 0 ? 'Main Copy' : 'Extension Copy ' . $key;
                                            $indent = $key * 20; // 20px per level
                                        @endphp

                                        <div style="margin-left: {{ $indent }}px;" class="mb-1">
                                            <a href="{{ asset('images/owner_agreements/' . $file) }}"
                                               target="_blank"
                                               class="badge bg-info">
                                                {{ $label }}
                                            </a>
                                        </div>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>



                            <td>{{ $owner->status == 'active' ? 'Active' : 'Inactive' }}</td>

                            <td style="width: 120px;">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('owner-edit')
                                        <button class="btn btn-info btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editNewModalId{{ $owner->id }}">
                                            Edit
                                        </button>
                                    @endcan

                                    @can('owner-delete')
                                        <a href="{{ route('owner.destroy', $owner->id) }}"
                                           class="btn btn-danger btn-sm"
                                           data-bs-toggle="modal"
                                           data-bs-target="#danger-header-modal{{ $owner->id }}">
                                            Delete
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editNewModalId{{ $owner->id }}" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Owner</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('owner.update', $owner->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label>Name</label>
                                                    <input type="text" name="name" value="{{ $owner->name }}" class="form-control" required>
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Email</label>
                                                    <input type="email" name="email" value="{{ $owner->email }}" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label>Phone</label>
                                                    <input type="text" name="phone" value="{{ $owner->phone }}" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Agreement Papers</label>

                                                    <input type="file"
                                                           id="editAgreementFiles{{ $owner->id }}"
                                                           name="agreement_paper[]"
                                                           class="d-none"
                                                           multiple>

                                                    <button type="button"
                                                            class="btn btn-outline-primary btn-sm"
                                                            onclick="document.getElementById('editAgreementFiles{{ $owner->id }}').click()">
                                                        <i class="mdi mdi-upload"></i> Add Files
                                                    </button>

                                                    <div id="editFilePreview{{ $owner->id }}" class="mt-2"></div>

                                                    @if(!empty($owner->agreement_paper))
                                                        <div class="mt-2">
                                                            @foreach($owner->agreement_paper as $file)
                                                                <a href="{{ asset('images/owner_agreements/'.$file) }}"
                                                                   target="_blank"
                                                                   class="badge bg-info me-1 mb-1">
                                                                    Existing File
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif


                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" {{ $owner->status == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $owner->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>




                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-primary" type="submit">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Modal --}}
                        <div id="danger-header-modal{{ $owner->id }}" class="modal fade" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-colored-header bg-danger">
                                        <h4 class="modal-title">Delete</h4>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h5>Do you want to delete this owner?</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <a href="{{ route('owner.destroy', $owner->id) }}" class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addNewModalId" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Owner</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('owner.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Agreement Papers</label>

                                <!-- Hidden file input -->
                                <input type="file"
                                       id="addAgreementFiles"
                                       name="agreement_paper[]"
                                       class="d-none"
                                       multiple>

                                <!-- Button -->
                                <button type="button"
                                        class="btn btn-outline-primary"
                                        onclick="document.getElementById('addAgreementFiles').click()">
                                    <i class="mdi mdi-upload"></i> Select Files
                                </button>

                                <!-- Preview -->
                                <div id="addFilePreview" class="mt-2"></div>

                                <small class="text-muted">
                                    Allowed: PDF, JPG, PNG | Multiple files supported
                                </small>
                            </div>

                        </div>




                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
        <script>
            // ===== ADD MODAL =====
            setupFileUpload(
                'addAgreementFiles',
                'addFilePreview'
            );

            // ===== EDIT MODALS =====
            @foreach($owners as $owner)
            setupFileUpload(
                'editAgreementFiles{{ $owner->id }}',
                'editFilePreview{{ $owner->id }}'
            );
            @endforeach

            function setupFileUpload(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                let files = [];

                if (!input) return;

                input.addEventListener('change', () => {
                    files = Array.from(input.files);
                    render();
                });

                function render() {
                    preview.innerHTML = '';

                    if (files.length === 0) {
                        preview.innerHTML = '<span class="text-muted">No files selected</span>';
                        return;
                    }

                    files.forEach((file, index) => {
                        const div = document.createElement('div');
                        div.className = 'd-flex justify-content-between align-items-center border rounded px-2 py-1 mb-1';

                        div.innerHTML = `
                    <span class="text-truncate">
                        <i class="mdi mdi-file"></i> ${file.name}
                    </span>
                    <button type="button" class="btn btn-sm btn-danger">
                        <i class="mdi mdi-close"></i>
                    </button>
                `;

                        div.querySelector('button').onclick = () => {
                            files.splice(index, 1);
                            updateInput();
                        };

                        preview.appendChild(div);
                    });

                    updateInput();
                }

                function updateInput() {
                    const dt = new DataTransfer();
                    files.forEach(file => dt.items.add(file));
                    input.files = dt.files;
                    render();
                }
            }
        </script>
@endpush

@endsection
