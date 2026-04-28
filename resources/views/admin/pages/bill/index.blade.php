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


                            </tr>

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

@endsection


