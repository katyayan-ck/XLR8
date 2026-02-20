@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-file-invoice-dollar text-warning"></i> Dealer Invoice Details
        <small class="d-none d-md-inline">Booking #{{ $booking->id }}</small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">

        <!-- ──────────────────────────────────────────────── -->
        <!--       Booking Information (View Only) Card         -->
        <!--       सबसे ऊपर दिखेगा - context के लिए जरूरी       -->
        <!-- ──────────────────────────────────────────────── -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-gradient-light text-dark d-flex align-items-center">
                <i class="la la-info-circle me-2 text-primary fs-4"></i>
                <h5 class="mb-0 fw-semibold">
                    Booking Information (View Only) #{{ $booking->id }}
                </h5>
            </div>

            <div class="card-body p-4">
                <dl class="row g-4 mb-0">
                    <!-- Row 1: Basic Info -->
                    <div class="col-12">
                        <div class="row g-4 border-bottom pb-3 mb-3">
                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">Booking Date</dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->booking_date
                                    ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y')
                                    : '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">Customer Name</dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->name ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">Branch</dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->branch
                                    ? ($booking->branch->name ?? $booking->branch->abbr ?? '—')
                                    : '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">Location</dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    @if($booking->location_id)
                                    {{ $booking->location?->name ?? '<span class="text-muted">—</span>' }}
                                    @else
                                    {{ $booking->location_other ?: '<span class="text-muted">—</span>' }}
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Vehicle Details + Chassis No. -->
                    <div class="col-12">
                        <div class="row g-4">
                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">
                                    <i class="la la-car-side me-1 text-primary"></i> Model
                                </dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->model ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">
                                    <i class="la la-cogs me-1 text-primary"></i> Variant
                                </dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->variant ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">
                                    <i class="la la-palette me-1 text-primary"></i> Color
                                </dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->color ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted small fw-medium mb-1">
                                    <i class="la la-key me-1 text-primary"></i> Chassis No.
                                </dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->chassis_no ?? $booking->chasis_no ?? '<span
                                        class="text-muted">—</span>' }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
        <div class="card p-3" id="dealer-invoice-card">
            <div class="card-body">
                <h5 class="mb-3">Dealer Invoice Details - Booking #{{ $booking->id }}</h5>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                @endif

                <form id="dealer-invoice-form" class="forms-sample" method="POST" action="{{ $saveAction }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- DMS Invoice Number -->
                        <div class="col-sm-3 form-group {{ $errors->has('dms_invoice_number') ? 'has-danger' : '' }}">
                            <label for="dms_invoice_number">DMS Invoice No. <span class="text-danger">*</span></label>
                            <input type="text" name="dms_invoice_number" id="dms_invoice_number"
                                class="form-control {{ $errors->has('dms_invoice_number') ? 'is-invalid' : '' }}"
                                value="{{ old('dms_invoice_number') }}" placeholder="INV00A123456" required>

                            @error('dms_invoice_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- DMS Invoice Date -->
                        <div class="col-sm-3 form-group {{ $errors->has('dms_invoice_date') ? 'has-danger' : '' }}">
                            <label for="dms_invoice_date">DMS Invoice Date <span class="text-danger">*</span></label>
                            <input type="text" name="dms_invoice_date_display" id="dms_invoice_date"
                                class="form-control flatpickr {{ $errors->has('dms_invoice_date') ? 'is-invalid' : '' }}"
                                placeholder="dd-MMM-yyyy" required>
                            <input type="hidden" name="dms_invoice_date" id="hidden_dms_invoice_date">
                            @error('dms_invoice_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Dealer Invoice Number (Blocked + Readonly) -->
                        <div class="col-sm-3 form-group">
                            <label>Dealer Invoice Number</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $booking->dealer_inv_no ?? 'N/A' }}" readonly tabindex="-1">
                        </div>

                        <!-- Dealer Invoice Date (Blocked + Readonly) -->
                        <div class="col-sm-3 form-group">
                            <label>Dealer Invoice Date</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $booking->dealer_inv_date ? \Carbon\Carbon::parse($booking->dealer_inv_date)->format('d-M-Y') : 'N/A' }}"
                                readonly tabindex="-1">
                        </div>

                        <!-- Submit -->
                        <div class="col-12 mt-4 text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="la la-save"></i> Submit Dealer Invoice
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('after_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('after_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    (function($) {
    'use strict';

    function initDealerInvoiceForm() {
        // Flatpickr - date picker only, no default/pre-filled value
        flatpickr("#dms_invoice_date", {
            dateFormat: "d-M-Y",
            maxDate: "today",
            allowInput: false,
            // No defaultDate set → blank rahega jab page load hoga
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const d = selectedDates[0];
                    const formatted = d.getFullYear() + '-' +
                                     String(d.getMonth() + 1).padStart(2, '0') + '-' +
                                     String(d.getDate()).padStart(2, '0');
                    $("#hidden_dms_invoice_date").val(formatted);
                }
            }
        });

        // Masking for DMS Invoice Number - format ke andar hi input rahega
        $('#dms_invoice_number').mask('INV00A000000', {
            placeholder: "INV00A123456"
        });

        // Uppercase karte rahenge
        $('#dms_invoice_number').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        // jQuery Validation
        $.validator.addMethod("dmsInvoiceFormat", function(value, element) {
            return this.optional(element) || /^INV\d{2}[A-Z]\d{6}$/.test(value);
        }, "Please enter valid DMS Invoice number (e.g., INV00A123456)");

        $("#dealer-invoice-form").validate({
            rules: {
                dms_invoice_number: {
                    required: true,
                    dmsInvoiceFormat: true,
                    minlength: 12,
                    maxlength: 12
                },
                dms_invoice_date_display: {
                    required: true
                }
            },
            messages: {
                dms_invoice_number: {
                    required: "DMS Invoice Number is required",
                    dmsInvoiceFormat: "Please enter valid DMS Invoice number (e.g., INV00A123456)",
                    minlength: "Must be exactly 11 characters",
                    maxlength: "Must be exactly 11 characters"
                },
                dms_invoice_date_display: {
                    required: "DMS Invoice Date is required"
                }
            },
            errorElement: "span",
            errorClass: "text-danger small",
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            highlight: function(element) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid");
            }
        });
    }

    $(document).ready(function() {
        initDealerInvoiceForm();

        // Optional: smooth scroll to card
        document.getElementById("dealer-invoice-card")?.scrollIntoView({ behavior: "smooth", block: "start" });
    });

})(jQuery);
</script>
@endpush