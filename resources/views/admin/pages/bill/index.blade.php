@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Mediation Group Account Management</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Bill</a></li>
                        <li class="breadcrumb-item active">Bill!</li>
                    </ol>
                </div>
                <h4 class="page-title">Bill!</h4>
            </div>
        </div>
        <div class="col-12">
            <div class="card">

                {{-- FILTER --}}
                <div class="card-header d-flex justify-content-between">
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
                        <span class="badge bg-info">Total: {{ number_format($totalAmount,2) }}</span>
                        <span class="badge bg-success">Collected: {{ number_format($totalPaid,2) }}</span>
                        <span class="badge bg-danger">Due: {{ number_format($totalDue,2) }}</span>
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

                                if ($due <= 0) {
                                    $rowClass = 'table-success';
                                    $status = 'Paid';
                                    $badge = 'success';
                                } elseif ($percentDue <= 80) {
                                    $rowClass = 'table-warning';
                                    $status = 'Partial';
                                    $badge = 'warning';
                                } else {
                                    $rowClass = 'table-danger';
                                    $status = 'Pending';
                                    $badge = 'danger';
                                }

                                $extra = json_decode($bill->is_extra_amount, true) ?? [];
                            @endphp

                            <tr class="{{ $rowClass }}">
                                <td>{{ $bill->invoice_number }}</td>
                                <td>{{ $bill->flat_address }}</td>
                                <td>{{ $bill->owner_name }}</td>
                                <td>{{ $bill->tenant_name }}</td>

                                <td>{{ number_format($total,2) }}</td>
                                <td>{{ number_format($paid,2) }}</td>
                                <td>{{ number_format($due,2) }}</td>

                                {{-- EXTRA --}}
                                <td>
                                    @if($extra)
                                        @foreach($extra as $k => $v)
                                            <small>{{ $k }}: {{ $v }}</small><br>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </td>

                                {{-- DB STATUS --}}
                                <td>
                                    <span class="badge bg-dark">{{ ucfirst($bill->status) }}</span>
                                </td>

                                {{-- CALCULATED --}}
                                <td>
                                    <span class="badge bg-{{ $badge }}">{{ $status }}</span>
                                </td>

                                <td>
                                    @can('bill-update')
                                    <button class="btn btn-sm btn-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $bill->id }}">
                                        Update
                                    </button>
                                    @endcan

                                    @can('bill-invoice')
                                    <a href="{{route('bill.invoice',$bill->id)}}" class="btn btn-sm btn-secondary">
                                        Invoice
                                    </a>
                                    @endcan
                                </td>
                            </tr>



                        @endforeach

                        </tbody>
                    </table>


                    @foreach($bills as $bill)
                        <div class="modal fade" id="editModal{{ $bill->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <form method="POST" action="{{ route('bill.update', $bill->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5>Update Bill</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            {{-- STATUS --}}
                                            <select name="status" class="form-select mb-3">
                                                <option value="pending" {{ $bill->status=='pending'?'selected':'' }}>Pending</option>
                                                <option value="finalized" {{ $bill->status=='finalized'?'selected':'' }}>Finalized</option>
                                                <option value="paid" {{ $bill->status=='paid'?'selected':'' }}>Paid</option>
                                            </select>

                                            <hr>

                                            <label>Extra Charges</label>

                                            @php
                                                $extra = json_decode($bill->is_extra_amount, true) ?? [];
                                            @endphp

                                            <div id="extraWrapper{{ $bill->id }}">

                                                @foreach($extra as $k => $v)
                                                    <div class="row mb-2 extra-row">
                                                        <div class="col-5">
                                                            <input type="text" name="extra_key[]" value="{{ $k }}" class="form-control">
                                                        </div>

                                                        <div class="col-5">
                                                            <input type="number" name="extra_value[]" value="{{ $v }}" class="form-control">
                                                        </div>

                                                        <div class="col-2">
                                                            <button type="button" class="btn btn-danger removeRow">X</button>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>

                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    onclick="addExtraRow({{ $bill->id }})">
                                                + Add
                                            </button>

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-primary">Update</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $bills->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        function addExtraRow(id){
            let wrapper = document.getElementById('extraWrapper'+id);

            wrapper.insertAdjacentHTML('beforeend', `
            <div class="row mb-2 extra-row">
                <div class="col-5">
                    <input type="text" name="extra_key[]" class="form-control">
                </div>
                <div class="col-5">
                    <input type="number" name="extra_value[]" class="form-control">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger removeRow">X</button>
                </div>
            </div>
        `);
        }

        document.addEventListener('click', function(e){
            if(e.target.classList.contains('removeRow')){
                e.target.closest('.extra-row').remove();
            }
        });
    </script>

@endsection
