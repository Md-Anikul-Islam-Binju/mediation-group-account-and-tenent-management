@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Mediation Group Account Management</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Rent</a></li>
                        <li class="breadcrumb-item active">Rent!</li>
                    </ol>
                </div>
                <h4 class="page-title">Rent!</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end gap-1">
                    @can('rent-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewModalId">
                            Add New
                        </button>
                    @endcan

                    @can('bill-generate')
                    @if($isGenerated)
                        <button class="btn btn-secondary" disabled>
                            Bill Already Generated ({{ now()->format('F') }})
                        </button>
                    @else
                        <button id="generateBillBtn" class="btn btn-success">
                            Generate Bill {{ now()->format('F') }}
                        </button>
                        <div class="modal fade" id="progressModal" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content text-center p-4">

                                    <h5>Generating Bills...</h5>
                                    <p id="progressText">Starting...</p>

                                    <div class="progress mt-3">
                                        <div id="progressBar"
                                             class="progress-bar progress-bar-striped progress-bar-animated"
                                             style="width: 0%">0%</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif
                    @endcan
                </div>
            </div>

            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Owner</th>
                        <th>Flat</th>
                        <th>Tenant</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($rents as $key => $rent)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>
                                Name : {{ $rent->owner->name ?? 'N/A' }}<br>
                                Rent: {{ $rent->flat->monthly_rental_amount }}<br>
                                Service Charge: {{ $rent->flat->service_charge ?? 0 }}
                            </td>
                            <td>{{ $rent->flat->address ?? 'N/A' }}</td>

                            <td>
                                Name : {{ $rent->tenant->name ?? 'N/A' }}<br>
                                Rent: {{  $rent->monthly_rental_amount }}<br>
                                Service Charge: {{ $rent->service_charge ?? 0  }}

                                @php
                                    $ownerCost = ($rent->flat->monthly_rental_amount ?? 0)
                                               + ($rent->flat->service_charge ?? 0);

                                    $tenantIncome = ($rent->monthly_rental_amount ?? 0)
                                                  + ($rent->service_charge ?? 0);

                                    $profit = $tenantIncome - $ownerCost;
                                @endphp

                                <div>
                                    <small class="text-primary">
                                        🏠 Given to Owner: {{ $ownerCost }}
                                    </small><br>

                                    <small class="text-info">
                                        💰 From Tenant: {{ $tenantIncome }}
                                    </small><br>

                                    @if($profit >= 0)
                                        <span class="badge bg-success mt-1">
                                            Profit: {{ $profit }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger mt-1">
                                            Loss: {{ $profit }}
                                        </span>
                                    @endif
                                </div>


                            </td>
                            <td>{{ \Carbon\Carbon::parse($rent->date)->format('j F Y') }}</td>

                            <td>{{ $rent->status == 'active' ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('rent-edit')
                                        <button class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editRent{{ $rent->id }}">
                                            Edit
                                        </button>
                                    @endcan

                                    @can('rent-delete')
                                            <a href="{{ route('rent.destroy', $rent->id) }}"
                                               class="btn btn-danger btn-sm"
                                               data-bs-toggle="modal"
                                               data-bs-target="#danger-header-modal{{ $rent->id }}">
                                                Delete
                                            </a>

                                    @endcan
                                </div>
                            </td>
                        </tr>

                        {{-- EDIT MODAL --}}
                        <div class="modal fade" id="editRent{{ $rent->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">

                                <form method="POST" action="{{ route('rent.update',$rent->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4>Edit Rent</h4>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label>Owner</label>
                                                    <select name="owner_id" class="form-select owner-select" data-id="{{ $rent->id }}">
                                                        @foreach($owners as $owner)
                                                            <option value="{{ $owner->id }}"
                                                                {{ $rent->owner_id == $owner->id ? 'selected' : '' }}>
                                                                {{ $owner->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Tenant</label>
                                                    <select name="tenant_id" class="form-select">
                                                        @foreach($tenants as $tenant)
                                                            <option value="{{ $tenant->id }}"
                                                                {{ $rent->tenant_id == $tenant->id ? 'selected' : '' }}>
                                                                {{ $tenant->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 mb-3">
                                                    <label>Flat</label>
                                                    <select name="flat_id" id="flatEditSelect{{ $rent->id }}" class="form-select">
                                                        @foreach($flats->where('owner_id',$rent->owner_id) as $flat)
                                                            <option value="{{ $flat->id }}"
                                                                {{ $rent->flat_id == $flat->id ? 'selected' : '' }}>
                                                                {{ $flat->address }} ({{ $flat->status }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label>Date</label>
                                                    <input type="date" name="date"
                                                           value="{{ $rent->date }}" class="form-control">
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <label>Rent Amount</label>
                                                    <input type="number" name="monthly_rental_amount"
                                                           value="{{ $rent->monthly_rental_amount }}"
                                                           class="form-control">
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label>Service Charge</label>
                                                    <input type="number" name="service_charge"
                                                           value="{{ $rent->service_charge }}"
                                                           class="form-control">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label>Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" {{ $rent->status == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $rent->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>



                                            <div class="mb-3">
                                                <label>Remark</label>
                                                <textarea name="remark" class="form-control">{{ $rent->remark }}</textarea>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-primary">Update</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>


                        {{-- Delete Modal --}}
                        <div id="danger-header-modal{{ $rent->id }}" class="modal fade" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-colored-header bg-danger">
                                        <h4 class="modal-title">Delete</h4>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h5>Do you want to delete this rent?</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <a href="{{ route('rent.destroy', $rent->id) }}" class="btn btn-danger">Delete</a>
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

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addNewModalId" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Rent</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('rent.store') }}">
                        @csrf

                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label>Owner</label>
                                        <select name="owner_id" id="ownerSelect" class="form-select">
                                            <option value="">Select Owner</option>
                                            @foreach($owners as $owner)
                                                <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-6 mb-3">
                                        <label>Tenant</label>
                                        <select name="tenant_id" class="form-select">
                                            @foreach($tenants as $tenant)
                                                <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label>Flat</label>
                                        <select name="flat_id" id="flatSelect" class="form-select">
                                            <option value="">Select Flat</option>
                                        </select>
                                    </div>

                                    <div class="col-6 mb-3">
                                        <label>Date</label>
                                        <input type="date" name="date" class="form-control">
                                    </div>

                                    <div class="col-6 mb-3">
                                        <label>Rent Amount</label>
                                        <input type="number" name="monthly_rental_amount" class="form-control">
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label>Service Charge</label>
                                        <input type="number" name="service_charge" class="form-control">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Remark</label>
                                    <textarea name="remark" class="form-control"></textarea>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('ownerSelect').addEventListener('change', function () {
            let ownerId = this.value;
            let flatSelect = document.getElementById('flatSelect');

            flatSelect.innerHTML = '<option>Loading...</option>';

            if (ownerId) {
                fetch(`/get-owner-flats/${ownerId}`)
                    .then(response => response.json())
                    .then(data => {

                        flatSelect.innerHTML = '<option value="">Select Flat</option>';

                        if (data.length === 0) {
                            flatSelect.innerHTML = '<option>No Vacant Flats</option>';
                            return;
                        }

                        data.forEach(flat => {
                            flatSelect.innerHTML += `
                        <option value="${flat.id}">
                            ${flat.address} (৳${flat.monthly_rental_amount})
                        </option>
                    `;
                        });
                    });
            } else {
                flatSelect.innerHTML = '<option>Select Flat</option>';
            }
        });
    </script>


    <script>
        document.querySelectorAll('.owner-select').forEach(select => {
            select.addEventListener('change', function () {

                let ownerId = this.value;
                let rentId = this.getAttribute('data-id');

                let flatSelect = document.getElementById('flatEditSelect' + rentId);

                flatSelect.innerHTML = '<option>Loading...</option>';

                fetch(`/get-owner-flats/${ownerId}`)
                    .then(res => res.json())
                    .then(data => {

                        flatSelect.innerHTML = '';

                        data.forEach(flat => {
                            flatSelect.innerHTML += `
                        <option value="${flat.id}">
                            ${flat.address}  (৳${flat.monthly_rental_amount})
                        </option>
                    `;
                        });
                    });
            });
        });
    </script>

    <script>
        document.getElementById('generateBillBtn').addEventListener('click', function () {

            let btn = this;
            btn.disabled = true;

            let modal = new bootstrap.Modal(document.getElementById('progressModal'));
            let progressBar = document.getElementById('progressBar');
            let progressText = document.getElementById('progressText');

            modal.show();

            // STEP 1: Start
            fetch('/bill-generate-start')
                .then(res => res.json())
                .then(data => {

                    if (data.status === 'exists') {
                        alert('Already generated for this month!');
                        modal.hide();
                        btn.disabled = false;
                        return;
                    }

                    let total = data.total;
                    let offset = 0;

                    function process() {

                        fetch(`/bill-generate-step?offset=${offset}`)
                            .then(res => res.json())
                            .then(res => {

                                if (res.done) {
                                    progressBar.style.width = '100%';
                                    progressBar.innerText = '100%';
                                    progressText.innerText = 'Completed ✅';

                                    setTimeout(() => location.reload(), 1000);
                                    return;
                                }

                                offset++;

                                let percent = Math.round((offset / total) * 100);

                                progressBar.style.width = percent + '%';
                                progressBar.innerText = percent + '%';

                                progressText.innerText = `Processing ${offset} of ${total}`;

                                process(); // next step
                            });
                    }

                    process();
                });
        });
    </script>
@endsection
