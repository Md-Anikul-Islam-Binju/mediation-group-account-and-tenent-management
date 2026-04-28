
@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Mediation Group Account Management</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Invoice</a></li>
                        <li class="breadcrumb-item active">Invoice!</li>
                    </ol>
                </div>
                <h4 class="page-title">Invoice!</h4>
            </div>
        </div>

        <div class="container my-4">

            <!-- Print Button -->
            <div class="text-end mb-3 d-print-none">
                <button class="btn btn-primary btn-print" onclick="window.print()">Print Invoice</button>
            </div>

            <div class="bg-white p-3 p-md-5 shadow-sm">

                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <h3 class="fw-bold text-secondary">RENT INVOICE</h3>
                        <p class="mb-1">Date: 3rd May 2026</p>
                        <p class="mb-1">Invoice: NA80724-16</p>
                        <p class="text-decoration-underline">Road 96 House 4B Apt 6B</p>
                    </div>

                    <div class="col-12 col-md-6  text-center">
                        <img src="https://via.placeholder.com/50" class="mb-2" alt="">
                        <h5 class="mb-0">Mediation Group</h5>
                        <small class="text-danger">Your red carpet experience starts here</small>
                    </div>
                </div>

                <!-- Bill Section -->
                <div class="row mb-4">
                    <div class="col-12 col-md-6  mb-3">
                        <h6 class="fw-bold">Bill To:</h6>
                        <p class="mb-0 fw-semibold">Ms. Shin Ji Youn</p>
                        <p class="mb-0">Embassy of the Republic of Korea</p>
                        <p class="mb-0">Dhaka-1212, Bangladesh</p>
                        <p class="mb-0">Phone:</p>
                    </div>

                    <div class="col-12 col-md-6  text start">
                        <h6 class="fw-bold">Bill By:</h6>
                        <p class="mb-0">Mediation Group</p>
                        <p class="mb-0">House: 8B, Road: 50, Gulshan 2, Dhaka</p>
                        <p class="mb-0">Phone: 01711-536373</p>
                        <p class="mb-0 small">Email: info@mediationgroupbd.com</p>
                        <p class="mb-0 small">Web: www.mediationgroupbd.com</p>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle">
                        <thead class="table-secondary text-center">
                        <tr>
                            <th>DESCRIPTION</th>
                            <th>MONTHLY (USD)</th>
                            <th>MONTH</th>
                            <th>AMOUNT (USD)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Rent for the month of May 2026</td>
                            <td class="text-center">1,200</td>
                            <td class="text-center">1</td>
                            <td class="text-end">1,200</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Payment + Summary -->
                <div class="row">
                    <div class="col-12 col-md-7 mb-3 small">
                        <p class="fw-bold mb-1">Payable to:</p>
                        <p class="mb-0">Account Name: Abu Ahsan Habib Sarker</p>
                        <p class="mb-0">Account Number: 099-321000000-1760</p>
                        <p class="mb-0">Bank Name: United Commercial Bank Limited</p>
                        <p class="mb-0">SWIFT: UCBLBDDH</p>
                        <p class="mb-0">Routing: 245260555</p>
                        <p class="mb-0">Bank Address: Bashundhara, Dhaka</p>
                        <p class="mb-0">TIN: 216403358562</p>
                    </div>

                    <div class="col-12 col-md-5">
                        <table class="table table-sm">
                            <tr>
                                <td>Subtotal</td>
                                <td class="text-end">1,200</td>
                            </tr>
                            <tr>
                                <td>VAT</td>
                                <td class="text-end">0%</td>
                            </tr>
                            <tr>
                                <td>VAT IN AMOUNT</td>
                                <td class="text-end">0</td>
                            </tr>
                            <tr>
                                <td>AIT</td>
                                <td class="text-end">0%</td>
                            </tr>
                            <tr>
                                <td>AIT IN AMOUNT</td>
                                <td class="text-end">0%</td>
                            </tr>
                            <tr class="fw-bold border-top">
                                <td>Total</td>
                                <td class="text-end">1,200</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Amount in words -->
                <div class="mt-3">
                    <p class="fw-bold small text-decoration-underline">
                        AMOUNT IN WORD: TWELVE HUNDRED USD ONLY.
                    </p>
                </div>

                <!-- Signature -->
                <div class="mt-5 small">
                    <p class="mb-0">Masuka Nasrin Pinki</p>
                    <p class="mb-0">Sr. Accountant</p>
                    <p class="mb-0">Mediation Group</p>
                    <p class="mb-0">Gulshan-2, Dhaka</p>
                </div>

            </div>
        </div>
    </div>




@endsection
