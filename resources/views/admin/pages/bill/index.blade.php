@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Mediation Group Account Management</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Bill/Invoice</a></li>
                        <li class="breadcrumb-item active">Bill/Invoice!</li>
                    </ol>
                </div>
                <h4 class="page-title">Bill/Invoice!</h4>
            </div>
        </div>
        <div class="col-12">
            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">

                    <form method="GET" class="d-flex gap-2">
                        <input type="month" name="month" value="{{ $month }}" class="form-control">

                        <input type="text" name="address"
                               value="{{ request('address') }}"
                               placeholder="Search address..."
                               class="form-control">

                        <button class="btn btn-primary">Filter</button>
                    </form>

                </div>

                <div class="card-body">

                    {{-- SUMMARY --}}
                    <div class="mb-3">
                    <span class="badge bg-info">
                        Total: {{ number_format($totalAmount,2) }}
                    </span>

                        <span class="badge bg-success">
                        Collected: {{ number_format($totalPaid,2) }}
                    </span>

                        <span class="badge bg-danger">
                        Due: {{ number_format($totalDue,2) }}
                    </span>
                    </div>

                    {{-- TABLE --}}
                    <table class="table table-bordered table-hover">

                        <thead class="table-dark">
                        <tr>
                            <th>Invoice</th>
                            <th>Flat</th>
                            <th>Owner</th>
                            <th>Tenant</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Extra</th>
                            <th>Invoice Status</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($bills as $bill)

                            @php
                                $total = (float) $bill->total_amount;
                                $due = (float) $bill->due_amount;
                                $paid = (float) $bill->paid_amount;

                                $percentDue = $total > 0 ? ($due / $total) * 100 : 0;

                                // ROW COLOR LOGIC
                                if ($due <= 0) {
                                    $rowClass = 'table-primary';
                                    $status = 'Paid';
                                    $badge = 'primary';
                                } elseif ($percentDue <= 80) {
                                    $rowClass = 'table-warning';
                                    $status = 'Partial';
                                    $badge = 'warning';
                                } else {
                                    $rowClass = 'table-danger';
                                    $status = 'Pending';
                                    $badge = 'danger';
                                }

                                // EXTRA AMOUNT
                                $extra = json_decode($bill->is_extra_amount, true);
                            @endphp

                            <tr class="{{ $rowClass }}">

                                <td>{{ $bill->invoice_number }}</td>
                                <td>{{ $bill->flat_address }}</td>
                                <td>{{ $bill->owner_name }}</td>
                                <td>{{ $bill->tenant_name }}</td>

                                <td>{{ number_format($total,2) }}</td>
                                <td>{{ number_format($paid,2) }}</td>
                                <td>{{ number_format($due,2) }}</td>

                                {{-- EXTRA AMOUNT --}}
                                <td>
                                    @if($extra)
                                        @foreach($extra as $key => $value)
                                            <small>{{ ucfirst($key) }}: {{ $value }}</small><br>
                                        @endforeach
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                {{-- INVOICE STATUS (DB FIELD) --}}
                                <td>
                                <span class="badge bg-dark">
                                    {{ ucfirst($bill->status) }}
                                </span>
                                </td>

                                {{-- PAYMENT STATUS (CALCULATED) --}}
                                <td>
                                    <span class="badge bg-{{ $badge }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td style="width: 120px;">
                                    <div class="d-flex justify-content-end gap-1">
                                        @can('bill-update')
                                            <button class="btn btn-info btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editNewModalId{{ $bill->id }}">
                                                Update
                                            </button>
                                        @endcan

                                        @can('bill-invoice')
                                            <a href="#" class="btn btn-success btn-sm">
                                                Invoice
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editNewModalId{{ $bill->id }}" data-bs-backdrop="static" tabindex="-1">
                                <div class="modal-dialog  modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Update Bill</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="#" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <label>Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" {{ $bill->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="finalized" {{ $bill->status == 'finalized' ? 'selected' : '' }}>Finalized</option>
                                                            <option value="paid" {{ $bill->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                        </select>
                                                    </div>

                                                    {{-- EXTRA AMOUNT DYNAMIC --}}
                                                    <label class="mb-2">Extra Charges</label>

                                                    @php
                                                        $extra = json_decode($bill->is_extra_amount, true) ?? [];
                                                    @endphp

                                                    <div id="extraWrapper{{ $bill->id }}">

                                                        @foreach($extra as $key => $value)
                                                            <div class="row mb-2 extra-row">
                                                                <div class="col-5">
                                                                    <input type="text"
                                                                           name="extra_key[]"
                                                                           value="{{ $key }}"
                                                                           class="form-control"
                                                                           placeholder="Label (e.g Gas Bill)">
                                                                </div>

                                                                <div class="col-5">
                                                                    <input type="number"
                                                                           name="extra_value[]"
                                                                           value="{{ $value }}"
                                                                           class="form-control"
                                                                           placeholder="Amount">
                                                                </div>

                                                                <div class="col-2">
                                                                    <button type="button" class="btn btn-danger removeRow">X</button>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                    </div>

                                                    <button type="button"
                                                            class="btn btn-sm btn-success mt-2"
                                                            onclick="addExtraRow({{ $bill->id }})">
                                                        + Add More
                                                    </button>
                                                </div><br>
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-primary" type="submit">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        @endforeach
                        </tbody>

                    </table>

                    {{-- PAGINATION --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $bills->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function addExtraRow(id) {
            let wrapper = document.getElementById('extraWrapper' + id);

            let html = `
        <div class="row mb-2 extra-row">
            <div class="col-5">
                <input type="text" name="extra_key[]" class="form-control" placeholder="Label">
            </div>
            <div class="col-5">
                <input type="number" name="extra_value[]" class="form-control" placeholder="Amount">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-danger removeRow">X</button>
            </div>
        </div>
    `;

            wrapper.insertAdjacentHTML('beforeend', html);
        }

        // remove row
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow')) {
                e.target.closest('.extra-row').remove();
            }
        });
    </script>

@endsection


