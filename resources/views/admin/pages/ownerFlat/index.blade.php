@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Owner Flat</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-end">
            @can('owner-flat-create')
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addFlatModal">
                    Add New
                </button>
            @endcan
        </div>

        <div class="card-body">
            <table class="table table-striped dt-responsive nowrap w-100">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Owner</th>
                    <th>Flat Code</th>
                    <th>Address</th>
                    <th>Rent</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($ownerFlats as $key => $flat)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $flat->owner->name }}</td>
                        <td>{{ $flat->flat_uniq_code }}</td>
                        <td>{{ $flat->address }}</td>
                        <td>{{ number_format($flat->monthly_rental_amount,2) }}</td>
                        <td>
                        <span class="badge {{ $flat->status == 'Vacant' ? 'bg-success' : 'bg-danger' }}">
                            {{ $flat->status }}
                        </span>
                        </td>
                        <td>
                            @can('owner-flat-edit')
                                <button class="btn btn-info btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editFlat{{ $flat->id }}">
                                    Edit
                                </button>
                            @endcan

                            @can('owner-flat-delete')
                                <form action="{{ route('owner.flat.destroy', $flat->id) }}"
                                      method="GET" class="d-inline">
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>

                    {{-- EDIT MODAL --}}
                    <div class="modal fade" id="editFlat{{ $flat->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('owner.flat.update', $flat->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Owner Flat</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row">

                                            <div class="col-12 mb-3">
                                                <label>Owner</label>
                                                <select name="owner_id" class="form-select" required>
                                                    @foreach($owners as $owner)
                                                        <option value="{{ $owner->id }}"
                                                            {{ $flat->owner_id == $owner->id ? 'selected' : '' }}>
                                                            {{ $owner->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>


                                            <div class="col-12 mb-3">
                                                <label>Address</label>
                                                <textarea name="address" class="form-control" required>{{ $flat->address }}</textarea>
                                            </div>

                                            <div class="col-6 mb-3">
                                                <label>Monthly Rent</label>
                                                <input type="number" step="0.01"
                                                       name="monthly_rental_amount"
                                                       class="form-control"
                                                       value="{{ $flat->monthly_rental_amount }}" required>
                                            </div>

                                            <div class="col-6 mb-3">
                                                <label>Service Charge</label>
                                                <input type="number" step="0.01"
                                                       name="service_charge"
                                                       class="form-control"
                                                       value="{{ $flat->service_charge }}">
                                            </div>

                                            <div class="col-6 mb-3">
                                                <label>Security Deposit Month</label>
                                                <input type="number"
                                                       name="security_deposit_month"
                                                       class="form-control"
                                                       value="{{ $flat->security_deposit_month }}">
                                            </div>

                                            <div class="col-6 mb-3">
                                                <label>Security Deposit Amount</label>
                                                <input type="number" step="0.01"
                                                       name="security_deposit_amount"
                                                       class="form-control"
                                                       value="{{ $flat->security_deposit_amount }}">
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label>Remark</label>
                                                <textarea name="remark" class="form-control">{{ $flat->remark }}</textarea>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label>Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="Vacant" {{ $flat->status == 'Vacant' ? 'selected' : '' }}>Vacant</option>
                                                    <option value="Booked" {{ $flat->status == 'Booked' ? 'selected' : '' }}>Booked</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Update</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- END EDIT MODAL --}}

                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addFlatModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('owner.flat.store') }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Add Owner Flat</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-12 mb-3">
                                <label>Owner</label>
                                <select name="owner_id" class="form-select" required>
                                    <option value="" disabled selected>Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-12 mb-3">
                                <label>Address</label>
                                <textarea name="address" class="form-control" required></textarea>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Monthly Rent</label>
                                <input type="number" step="0.01"
                                       name="monthly_rental_amount"
                                       class="form-control" required>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Service Charge</label>
                                <input type="number" step="0.01"
                                       name="service_charge"
                                       class="form-control">
                            </div>

                            <div class="col-6 mb-3">
                                <label>Security Deposit Month</label>
                                <input type="number"
                                       name="security_deposit_month"
                                       class="form-control">
                            </div>

                            <div class="col-6 mb-3">
                                <label>Security Deposit Amount</label>
                                <input type="number" step="0.01"
                                       name="security_deposit_amount"
                                       class="form-control">
                            </div>

                            <div class="col-12 mb-3">
                                <label>Remark</label>
                                <textarea name="remark" class="form-control"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection
