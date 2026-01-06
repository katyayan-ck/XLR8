{{-- resources/views/admin/booking/show.blade.php --}}
@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>Booking Details #{{ $entry->sap_no ?? $entry->dms_no ?? $entry->id }}</h2>
</section>
@endsection

@section('content')
<div class="container-fluid">
    @include(backpack_view('inc.alerts'))
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 fw-bold">Booking Details (View Only)</h4>
                </div>
                <div class="card-body">

                    <!-- Payment Details -->
                    <h5 class="mb-3 fw-bold text-primary">Payment Details</h5>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="small fw-bold">Customer Type</label>
                            <input type="text" class="form-control" value="{{ ucfirst($entry->customertype ?? 'N/A') }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Customer Category</label>
                            <input type="text" class="form-control" value="{{ ucfirst($entry->customercat ?? 'N/A') }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Booking Date</label>
                            <input type="text" class="form-control"
                                value="{{ $entry->booking_date ? \Carbon\Carbon::parse($entry->booking_date)->format('d-M-Y') : 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Collection Type</label>
                            <input type="text" class="form-control" value="{{ match((int)($entry->col_type ?? 1)) {
                                    1 => 'Receipt',
                                    2 => 'Field Collection By Sales Team',
                                    3 => 'Field Collection By DSA',
                                    4 => 'Used Car Purchase',
                                    default => 'N/A'
                                } }}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="small fw-bold">Collected By</label>
                            <input type="text" class="form-control"
                                value="{{ \App\Models\User::find($entry->col_by)?->name ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="small fw-bold">Booking Amount</label>
                            <input type="text" class="form-control"
                                value="₹ {{ number_format($entry->booking_amount ?? 0) }}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="small fw-bold">
                                {{ in_array($entry->col_type, [1,4])
                                ? ($entry->col_type == 1 ? 'Receipt No.' : 'Voucher No.')
                                : 'Receipt/Voucher No.' }}
                            </label>
                            <input type="text" class="form-control" value="{{ $entry->receipt_no ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="small fw-bold">Receipt/Voucher Date</label>
                            <input type="text" class="form-control"
                                value="{{ $entry->receipt_date ? \Carbon\Carbon::parse($entry->receipt_date)->format('d-M-Y') : 'N/A' }}"
                                readonly>
                        </div>

                        <!-- Payment Proof -->
                        <div class="col-sm-4">
                            <label class="small fw-bold">Payment Proof (Image/PDF)</label>
                            <div class="border rounded bg-light p-3 text-center" style="min-height: 180px;">
                                @php
                                $payment = $entry->bookingAmounts()->latest()->first();
                                $hasMedia = $payment && $payment->hasMedia('amount-proof');
                                $media = $hasMedia ? $payment->getFirstMedia('amount-proof') : null;
                                $fileUrl = $hasMedia ? $media->getUrl() : null;
                                @endphp
                                @if($hasMedia)
                                @if($media->mime_type === 'application/pdf')
                                <i class="la la-file-pdf text-danger" style="font-size: 70px;"></i>
                                <p class="small text-muted mt-2 mb-1">{{ basename($media->file_name) }}</p>
                                @else
                                <img src="{{ $fileUrl }}" class="img-thumbnail border shadow-sm"
                                    style="max-height: 110px; max-width: 100%; object-fit: cover; border-radius: 8px;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display:none;" class="text-danger small mt-2">
                                    <i class="la la-exclamation-triangle"></i> Image load failed
                                </div>
                                @endif
                                <div class="mt-3">
                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary btn-sm px-4">
                                        <i class="la la-external-link-alt"></i> Open Full Size
                                    </a>
                                </div>
                                @else
                                <div class="text-muted small mt-5">No payment proof uploaded</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <h5 class="mb-3 mt-4 fw-bold text-primary">Customer Details</h5>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="small fw-bold">Customer Name</label>
                            <input type="text" class="form-control" value="{{ $entry->name ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Care Of</label>
                            <input type="text" class="form-control" value="{{ match($entry->careof) {
                                    '1' => 'Son of',
                                    '2' => 'Daughter of',
                                    '3' => 'Married to',
                                    '4' => 'Guardian Name',
                                    '5' => 'Owned By',
                                    default => 'N/A'
                                } }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Care Of Name</label>
                            <input type="text" class="form-control" value="{{ $entry->careofname ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Contact No.</label>
                            <input type="text" class="form-control" value="{{ $entry->mobile ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Alternate Contact No.</label>
                            <input type="text" class="form-control" value="{{ $entry->altmobile ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Gender</label>
                            <input type="text" class="form-control" value="{{ ucfirst($entry->gender ?? 'N/A') }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Occupation</label>
                            <input type="text" class="form-control" value="{{ match($entry->occupation) {
                                    'Agriculture' => 'Agriculture',
                                    'Business' => 'Business',
                                    'Salaried Govt.' => 'Salaried Govt.',
                                    'Salaried Pvt.' => 'Salaried Pvt.',
                                    'Self Employed Professional' => 'Self Employed Professional',
                                    'Pensioner' => 'Pensioner',
                                    'Other' => 'Other',
                                    default => 'N/A'
                                } }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">PAN Card No.</label>
                            <input type="text" class="form-control" value="{{ $entry->panno ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Aadhar No.</label>
                            <input type="text" class="form-control" value="{{ $entry->adharno ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">GSTN</label>
                            <input type="text" class="form-control" value="{{ $entry->gstn ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Customer D.O.B.</label>
                            <input type="text" class="form-control"
                                value="{{ $entry->customerdob ? \Carbon\Carbon::parse($entry->customerdob)->format('d-M-Y') : 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Customer Age</label>
                            <input type="text" class="form-control" value="{{ $entry->customerage ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="small fw-bold">Branch</label>
                            <input type="text" class="form-control" value="{{ $entry->branch?->name ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="small fw-bold">Location</label>
                            <input type="text" class="form-control" 
                                   value="{{ $entry->location ? ($entry->location->name . ' - ' . ($entry->location->abbr ?? 'N/A')) : 'N/A' }}"
                                   readonly>
                        </div>
                        <div class="col-sm-4">
                            <label class="small fw-bold">Other Location</label>
                            <input type="text" class="form-control" value="{{ $entry->locationother ?? 'N/A' }}"
                                readonly>
                        </div>
                    </div>

                    <!-- Referred By Details -->
                    <h5 class="mb-3 mt-4 fw-bold text-primary">Referred By Details</h5>
                    <div class="row g-3">
                        <div class="col-sm-2">
                            <label class="small fw-bold">Referred By</label>
                            <input type="text" class="form-control" value="{{ $entry->referredby ? 'Yes' : 'No' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Referred Customer Name</label>
                            <input type="text" class="form-control" value="{{ $entry->refcustomername ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Referred Mobile No.</label>
                            <input type="text" class="form-control" value="{{ $entry->refmobileno ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-2">
                            <label class="small fw-bold">Existing Model</label>
                            <input type="text" class="form-control" value="{{ $entry->refexistingmodel ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-2">
                            <label class="small fw-bold">Variant</label>
                            <input type="text" class="form-control" value="{{ $entry->refvariant ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-2">
                            <label class="small fw-bold">Chassis/Regn. No.</label>
                            <input type="text" class="form-control" value="{{ $entry->refchassisregno ?? 'N/A' }}"
                                readonly>
                        </div>
                    </div>

                    <!-- Purchase Type Details -->
                    <h5 class="mb-3 mt-4 fw-bold text-primary">Purchase Type Details</h5>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="small fw-bold">Purchase Type</label>
                            <input type="text" class="form-control" value="{{ match($entry->buyertype) {
                                    'First time Buyer' => 'First time Buyer',
                                    'Additional Buy' => 'Additional Buy',
                                    'Exchange Buy' => 'Exchange Buy',
                                    'Scrappage' => 'Scrappage',
                                    default => 'N/A'
                                } }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Brand Make 1</label>
                            <input type="text" class="form-control" value="{{ $entry->enummaster1 ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Model Variant 1</label>
                            <input type="text" class="form-control" value="{{ $entry->vehicledetails ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Brand Make 2</label>
                            <input type="text" class="form-control" value="{{ $entry->enummaster2 ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Model Variant 2</label>
                            <input type="text" class="form-control" value="{{ $entry->vehicledetails2 ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Vehicle Registration No.</label>
                            <input type="text" class="form-control" value="{{ $entry->registrationno ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Manufacturing Year</label>
                            <input type="text" class="form-control" value="{{ $entry->manufacturingyear ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Odometer Reading</label>
                            <input type="text" class="form-control" value="{{ $entry->odometerreading ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Expected Price</label>
                            <input type="text" class="form-control"
                                value="₹ {{ number_format($entry->expectedprice ?? 0) }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Offered Price</label>
                            <input type="text" class="form-control"
                                value="₹ {{ number_format($entry->offeredprice ?? 0) }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Exchange Bonus</label>
                            <input type="text" class="form-control"
                                value="₹ {{ number_format($entry->exchangebonus ?? 0) }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Price Gap</label>
                            <input type="text" class="form-control"
                                value="₹ {{ number_format($entry->difference ?? 0) }}" readonly>
                        </div>
                    </div>

                    <!-- Vehicle Details -->
                    <h5 class="mb-3 mt-4 fw-bold text-primary">Vehicle Details</h5>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="small fw-bold">Segment</label>
                            <input type="text" class="form-control" value="{{ $entry->segment?->name ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Model</label>
                            <input type="text" class="form-control" value="{{ $entry->model ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Variant</label>
                            <input type="text" class="form-control" value="{{ $entry->variant ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Color</label>
                            <input type="text" class="form-control" value="{{ $entry->color ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Seating</label>
                            <input type="text" class="form-control" value="{{ $entry->seating ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-5">
                            <label class="small fw-bold">Accessories</label>
                            <input type="text" class="form-control"
                                value="{{ $entry->accessories ? str_replace(',', ', ', $entry->accessories) : 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-2">
                            <label class="small fw-bold">Accessories Amount</label>
                            <input type="text" class="form-control"
                                value="₹ {{ number_format($entry->apack_amount ?? 0) }}" readonly>
                        </div>
                        <div class="col-sm-2">
                            <label class="small fw-bold">Allotted Chassis No.</label>
                            <input type="text" class="form-control" value="{{ $entry->chassis ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <!-- Booking Type & Source -->
                    <h5 class="mb-3 mt-4 fw-bold text-primary">Booking Type & Source</h5>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="small fw-bold">Booking Mode</label>
                            <input type="text" class="form-control" value="{{ ucfirst($entry->bookingmode ?? 'N/A') }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Online Booking Ref No.</label>
                            <input type="text" class="form-control" value="{{ $entry->refrenceno ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Booking Source</label>
                            <input type="text" class="form-control"
                                value="{{ ucfirst($entry->bookingsource ?? 'N/A') }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">DSA</label>
                            <input type="text" class="form-control"
                                value="{{ \App\Models\Xl_DSA_Master::find($entry->dsadetails)?->name ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Sales Consultant</label>
                            <input type="text" class="form-control"
                                value="{{ \App\Models\User::find($entry->saleconsultant)?->name ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Delivery Date Type</label>
                            <input type="text" class="form-control" value="{{ ucfirst($entry->deliverytype ?? 'N/A') }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Expected Delivery Date</label>
                            <input type="text" class="form-control"
                                value="{{ $entry->expecteddeldate ? \Carbon\Carbon::parse($entry->expecteddeldate)->format('d-M-Y') : 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Finance Mode</label>
                            <input type="text" class="form-control" value="{{ ucfirst($entry->finmode ?? 'N/A') }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Financier</label>
                            <input type="text" class="form-control"
                                value="{{ \App\Models\XlFinancier::find($entry->financier)?->name ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Financier Short Name</label>
                            <input type="text" class="form-control" value="{{ $entry->financiershortname ?? 'N/A' }}"
                                readonly>
                        </div>
                        <div class="col-sm-3">
                            <label class="small fw-bold">Loan File Status</label>
                            <input type="text" class="form-control" value="{{ ucfirst($entry->loanstatus ?? 'N/A') }}"
                                readonly>
                        </div>
                        
                    </div>

                    <!-- Back Button -->
                    <div class="row mt-5">
                        <div class="col-12 text-center">
                            <a href="{{ backpack_url('booking') }}" class="btn btn-secondary btn-lg px-5 shadow">
                                <i class="la la-arrow-left me-2"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection