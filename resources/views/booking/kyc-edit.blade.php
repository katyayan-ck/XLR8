@extends(backpack_view('blank'))

@section('title', 'Complete KYC - Booking #' . $booking->id)

@push('after_styles')
<link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .is-valid {
        border-color: #28a745 !important;
        box-shadow: 0 0 5px rgba(40, 167, 69, .5);
    }

    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 5px rgba(220, 53, 69, .5);
    }

    .required-mark {
        color: #dc3545;
        margin-left: 3px;
    }

    .card-header {
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h2><i class="la la-id-card text-warning"></i> Complete KYC - Booking #{{ $booking->id }}</h2>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
    @endif

    <form id="kyc-form" method="POST" action="{{ route('kyc.update', $booking->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="pending_flag" value="1">

        <!-- KYC Details -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">KYC Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>PAN No. <span class="required-mark">*</span></label>
                        <input type="text" name="pan_no" id="panno" class="form-control"
                            value="{{ old('pan_no', $booking->pan_no) }}" maxlength="10">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Aadhaar No. <span class="required-mark">*</span></label>
                        <input type="text" name="adhar_no" id="adharno" class="form-control"
                            value="{{ old('adhar_no', $booking->adhar_no) }}" maxlength="14">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>GST No.</label>
                        <input type="text" name="gst_no" id="gstn" class="form-control"
                            value="{{ old('gst_no', $booking->gstn ?? '') }}" maxlength="15">
                        <div class="form-check mt-2">
                            <input type="checkbox" name="gst_not_required" id="notrequiredgst" class="form-check-input"
                                {{ old('gst_not_required', ($booking->gstn === '0' || empty($booking->gstn)) ? 'checked'
                            : '') }}>
                            <label class="form-check-label">GST Not Required</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="la la-save"></i> Save KYC Details
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Readonly Details -->
        {{-- <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Booking & Customer Details (Readonly)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Booking Date:</strong> {{ $booking->booking_date ?
                        \Carbon\Carbon::parse($booking->booking_date)->format('d-M-Y') : 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Customer:</strong> {{ $booking->name ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Mobile:</strong> {{ $booking->mobile ?? 'N/A' }}
                    </div>
                    <!-- आप बाकी readonly fields यहाँ जोड़ सकते हैं -->
                </div>
            </div>
        </div> --}}
        <!-- Readonly Details - Improved View-Only Card -->
        <!-- Readonly Booking Information - Enhanced View -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-gradient-light text-dark d-flex align-items-center">
                <i class="la la-info-circle me-2 text-primary fs-4"></i>
                <h5 class="mb-0 fw-semibold">Booking Information (View Only)</h5>
            </div>

            <div class="card-body p-4">
                <dl class="row g-4 mb-0">
                    <!-- Row 1 -->
                    <div class="col-12">
                        <div class="row g-4 border-bottom pb-3 mb-3">
                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted fw-medium mb-1">Booking Date</dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->booking_date
                                    ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y')
                                    : '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted   fw-medium mb-1">Customer Name</dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->name ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted   fw-medium mb-1">Branch</dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->branch
                                    ? ($booking->branch->name ?? $booking->branch->abbr ?? '—')
                                    : '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <dt class="text-muted   fw-medium mb-1">Location</dt>
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

                    <!-- Row 2 - Vehicle Details -->
                    <div class="col-12">
                        <div class="row g-4">
                            <div class="col-md-4 col-sm-6">
                                <dt class="text-muted   fw-medium mb-1">
                                    <i class="la la-car-side me-1 text-primary"></i> Model
                                </dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->model ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <dt class="text-muted   fw-medium mb-1">
                                    <i class="la la-cogs me-1 text-primary"></i> Variant
                                </dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->variant ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <dt class="text-muted   fw-medium mb-1">
                                    <i class="la la-palette me-1 text-primary"></i> Color
                                </dt>
                                <dd class="fs-5 fw-semibold mb-0 text-dark">
                                    {{ $booking->color ?? '<span class="text-muted">—</span>' }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </dl>
            </div>

            <!-- Optional subtle footer for extra info if needed later -->
            <!-- <div class="card-footer bg-light   text-muted text-center py-2">
        Last updated: {{ $booking->updated_at?->diffForHumans() ?? 'N/A' }}
    </div> -->
        </div>
    </form>
</div>
@endsection

@push('after_scripts')
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<script>
    $(document).ready(function() {
            // Masks (आपके मूल कोड जैसा ही रखा है)
            function initMasks() {
                $('#panno').mask('AAAAA0000A', { placeholder: 'ABCDE1234F' });
                $('#adharno').mask('0000-0000-0000', { placeholder: '1234-5678-9012' });
                // अगर बाकी masks भी चाहिए तो यहाँ रख सकते हैं
                // $('#refrenceno').mask('AAAAAAAAAA', { placeholder: 'Booking Reference No.' });
                $('#gstn').mask('00AAAAA0000A0ZS', { placeholder: '12ABCDE1234F2ZK', clearIfNotMatch: false });
                // Receipt voucher mask (अगर इस page पर इस्तेमाल हो तो)
                // $('#receiptvoucherinput').mask('00000', { placeholder: '12345', reverse: true });
            }

            // Validation (आपके मूल कोड जैसा ही - सिर्फ जरूरी fields लिए हैं)
            function initValidation() {
                $.validator.addMethod('panFormat', function(value, element) {
                    return this.optional(element) || /^[A-Z]{5}[0-9]{4}[A-Z]$/.test(value);
                }, 'Please enter a valid PAN number e.g., ABCDE1234F');

                $.validator.addMethod('udaiFormat', function(value, element) {
                    return this.optional(element) || /^[2-9]{1}[0-9]{3}[ -]?[0-9]{4}[ -]?[0-9]{4}$/.test(value);
                }, 'Please enter a valid Aadhaar No. e.g., 1234-5678-9012');

                $.validator.addMethod('gstnFormat', function(value, element) {
                    return this.optional(element) || /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(value);
                }, 'Please enter a valid GSTIN e.g., 08CDBPB0580N2ZK');

                $('#kyc-form').validate({
                    rules: {
                        pan_no: { required: true, panFormat: true },
                        adhar_no: { required: true, udaiFormat: true },
                        gst_no: {
                            gstnFormat: true,
                            required: function() { return !$('#notrequiredgst').is(':checked'); }
                        }
                    },
                    messages: {
                        pan_no: { required: 'PAN No. is required', panFormat: 'Please enter a valid PAN e.g., ABCDE1234F' },
                        adhar_no: { required: 'Aadhaar No. is required', udaiFormat: 'Please enter a valid Aadhaar e.g., 1234-5678-9012' },
                        gst_no: { required: 'GST No. is required when not marked as optional', gstnFormat: 'Please enter a valid GSTIN e.g., 08CDBPB0580N2ZK' }
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('text-danger');
                        error.insertAfter(element);
                    },
                    highlight: function(element) {
                        $(element).removeClass('is-valid').addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid').addClass('is-valid');
                    },
                    onfocusout: function(element) {
                        this.element(element);
                    },
                    submitHandler: function(form) {
                        form.submit();  // Submit होने पर controller में redirect हो जाएगा
                    }
                });
            }

            // GST checkbox logic
            $('#notrequiredgst').on('change', function() {
                if (this.checked) {
                    $('#gstn').val('0').prop('disabled', true).hide();
                } else {
                    $('#gstn').val('').prop('disabled', false).show();
                }
                $('#kyc-form').validate().element('#gstn');
            }).trigger('change');

            // Initialize
            initMasks();
            initValidation();

            // PAN uppercase force
            $('#panno').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });
        });
</script>
@endpush