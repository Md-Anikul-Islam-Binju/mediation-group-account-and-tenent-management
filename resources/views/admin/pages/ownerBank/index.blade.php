
@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Mediation Group Account Management</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Owner</a></li>
                        <li class="breadcrumb-item active">Owner Bank!</li>
                    </ol>
                </div>
                <h4 class="page-title">Owner Bank!</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('owner-bank-create')
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
                        <th>Owner Name</th>
                        <th>Bank Info</th>
                        <th>Remark</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($ownerBank as $key => $ownerBankData)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $ownerBankData->owner->name }}</td>
                            <td>
                                Bank Name: {{ $ownerBankData->bank_name }} <br>
                                Account Holder: {{ $ownerBankData->account_holder_name }} <br>
                                Account Number: {{ $ownerBankData->account_number }} <br>
                                Branch: {{ $ownerBankData->branch_name }} <br>
                                Routing Number: {{ $ownerBankData->route_number }}
                            </td>
                            <td>{{ $ownerBankData->remark ?? 'N/A' }}</td>
                            <td>{{ $ownerBankData->status == 'active' ? 'Active' : 'Inactive' }}</td>

                            <td style="width: 120px;">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('owner-bank-edit')
                                        <button class="btn btn-info btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editNewModalId{{ $ownerBankData->id }}">
                                            Edit
                                        </button>
                                    @endcan

                                    @can('owner-bank-delete')
                                        <a href="{{ route('owner.bank.destroy', $ownerBankData->id) }}"
                                           class="btn btn-danger btn-sm"
                                           data-bs-toggle="modal"
                                           data-bs-target="#danger-header-modal{{ $ownerBankData->id }}">
                                            Delete
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editNewModalId{{ $ownerBankData->id }}" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Owner Bank</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('owner.bank.update', $ownerBankData->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">

                                                <div class="col-6 mb-3">
                                                    <label>Owner</label>
                                                    <select name="owner_id" class="form-select" required>
                                                        @foreach($owners as $owner)
                                                            <option value="{{ $owner->id }}" {{ $ownerBankData->owner_id == $owner->id ? 'selected' : '' }}>
                                                                {{ $owner->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Bank Name</label>
                                                    <input type="text" name="bank_name" class="form-control" value="{{ $ownerBankData->bank_name }}" required>
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Account Holder Name</label>
                                                    <input type="text" name="account_holder_name" class="form-control" value="{{ $ownerBankData->account_holder_name }}" required>
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Account Number</label>
                                                    <input type="text" name="account_number" class="form-control" value="{{ $ownerBankData->account_number }}" required>
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Branch Name</label>
                                                    <input type="text" name="branch_name" class="form-control" value="{{ $ownerBankData->branch_name }}">
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Routing Number</label>
                                                    <input type="text" name="route_number" class="form-control" value="{{ $ownerBankData->route_number }}">
                                                </div>

                                                <div class="col-12 mb-3">
                                                    <label>Remark</label>
                                                    <textarea name="remark" class="form-control" rows="3">{{ $ownerBankData->remark }}</textarea>
                                                </div>

                                                <div class="col-12 mb-3">
                                                    <label>Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" {{ $ownerBankData->status == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $ownerBankData->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        <div id="danger-header-modal{{ $ownerBankData->id }}" class="modal fade" tabindex="-1">
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
                                        <a href="{{ route('owner.bank.destroy', $ownerBankData->id) }}" class="btn btn-danger">Delete</a>
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
                    <h4 class="modal-title">Add Owner Bnak</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('owner.bank.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <div class="col-6 mb-3">
                                <label>Owner</label>
                                <select name="owner_id" class="form-select" required>
                                    <option value="" disabled selected>Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" required>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Account Holder Name</label>
                                <input type="text" name="account_holder_name" class="form-control" required>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Account Number</label>
                                <input type="text" name="account_number" class="form-control" required>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Branch Name</label>
                                <input type="text" name="branch_name" class="form-control">
                            </div>

                            <div class="col-6 mb-3">
                                <label>Routing Number</label>
                                <input type="text" name="route_number" class="form-control">
                            </div>

                            <div class="col-12 mb-3">
                                <label>Remark</label>
                                <textarea name="remark" class="form-control" rows="3"></textarea>
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



@endsection
