@extends(backpack_view('blank'))

@section('header')
<section class="container-fluid">
    <h2>
        <i class="la la-car text-success"></i> Pending RTO
        <small>Booking #{{ $booking->id }} — {{ $booking->name ?? 'N/A' }}</small>
    </h2>
</section>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div
                class="card-header bg-gradient-success d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="card-title mb-0 fw-bold text-black">
                    RTO / Registration Details — Booking #{{ $booking->id }}
                </h3>
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                    <i class="la la-arrow-left"></i> Back
                </a>
            </div>

            <div class="card-body">
                @include(backpack_view('inc.alerts'))

                <!-- ──────────────────────────────────────────────── -->
                <!--       Booking Information (View Only) Card         -->
                <!--       सबसे ऊपर जोड़ दिया - context के लिए जरूरी     -->
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

                            <!-- Row 2: Vehicle Details + Chassis + RTO Application No. -->
                            <div class="col-12">
                                <div class="row g-4 border-bottom pb-3 mb-3">
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

                            <!-- Row 3: Invoice + RTO Application No. -->
                            <div class="col-12">
                                <div class="row g-4">
                                    <div class="col-md-4 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-file-invoice me-1 text-primary"></i> Invoice Number
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->inv_no ?? $booking->dms_invoice_number ?? '<span
                                                class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-4 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-calendar-check me-1 text-primary"></i> Invoice Date
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            {{ $booking->inv_date
                                            ? \Carbon\Carbon::parse($booking->inv_date)->format('d M Y')
                                            : '<span class="text-muted">—</span>' }}
                                        </dd>
                                    </div>

                                    <div class="col-md-4 col-sm-6">
                                        <dt class="text-muted small fw-medium mb-1">
                                            <i class="la la-file-alt me-1 text-primary"></i> RTO Application No.
                                        </dt>
                                        <dd class="fs-5 fw-semibold mb-0 text-dark">
                                            @if($rto && $rto->app_no)
                                            <span class="text-uppercase">{{ $rto->app_no }}</span>
                                            @else
                                            <span class="text-muted">—</span>
                                            @endif
                                        </dd>
                                    </div>
                                </div>
                            </div>

                        </dl>
                    </div>
                </div>

                <form id="rtoForm" method="POST" action="{{ route('booking.rto.update', $booking->id) }}"
                    enctype="multipart/form-data" class="row g-3">
                    @csrf

                    <!-- Registration Data Section -->
                    <div class="col-12">
                        <h5 class="mb-3 border-bottom pb-2">Registration Data</h5>
                    </div>

                    <!-- Trade Used -->
                    <div class="col-md-3">
                        <label class="form-label">Trade Used</label>
                        <select name="trade_used" class="form-control">
                            <option value="">Select Trade Used</option>
                            <option value="1" {{ old('trade_used', $rto->trade_used ?? '') == '1' ? 'selected' : ''
                                }}>BKN AD User 1 (RJ0730024TC)</option>
                            <option value="2" {{ old('trade_used', $rto->trade_used ?? '') == '2' ? 'selected' : ''
                                }}>BKN AD User 2 (RJ0730024TC)</option>
                            <option value="3" {{ old('trade_used', $rto->trade_used ?? '') == '3' ? 'selected' : ''
                                }}>BKN AD User 3 (RJ0730024TC)</option>
                            <option value="4" {{ old('trade_used', $rto->trade_used ?? '') == '4' ? 'selected' : ''
                                }}>SUJ AD (RJ44C0012TC)</option>
                            <option value="5" {{ old('trade_used', $rto->trade_used ?? '') == '5' ? 'selected' : ''
                                }}>BKN LMM L5 (RJ07C0056TC)</option>
                            <option value="6" {{ old('trade_used', $rto->trade_used ?? '') == '6' ? 'selected' : ''
                                }}>BKN LMM L3 (RJ07TC0322)</option>
                        </select>
                        @error('trade_used') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Sale Type -->
                    <div class="col-md-3">
                        <label class="form-label">Sale Type</label>
                        <select name="sale_type" id="sale_type" class="form-control">
                            <option value="">Select Sale Type</option>
                            <option value="1" {{ old('sale_type', $rto->sale_type ?? '') == '1' ? 'selected' : ''
                                }}>Within State</option>
                            <option value="2" {{ old('sale_type', $rto->sale_type ?? '') == '2' ? 'selected' : ''
                                }}>Outside State</option>
                        </select>
                        @error('sale_type') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Permit -->
                    <div class="col-md-3">
                        <label class="form-label">Permit</label>
                        <select name="permit" id="permit" class="form-control">
                            <option value="">Select Permit</option>
                            <option value="1" {{ old('permit', $rto->permit ?? '') == '1' ? 'selected' : '' }}>Private -
                                U/C (4 Wheeler)</option>
                            <option value="2" {{ old('permit', $rto->permit ?? '') == '2' ? 'selected' : '' }}>Private -
                                BH (4 Wheeler)</option>
                            <option value="3" {{ old('permit', $rto->permit ?? '') == '3' ? 'selected' : '' }}>Private -
                                EV (4 Wheeler)</option>
                            <option value="4" {{ old('permit', $rto->permit ?? '') == '4' ? 'selected' : '' }}>Goods - G
                                (4 Wheeler)</option>
                            <option value="5" {{ old('permit', $rto->permit ?? '') == '5' ? 'selected' : '' }}>Goods - G
                                3 Ton+ (4 Wheeler)</option>
                            <option value="6" {{ old('permit', $rto->permit ?? '') == '6' ? 'selected' : '' }}>Goods - G
                                (3 Wheeler)</option>
                            <option value="7" {{ old('permit', $rto->permit ?? '') == '7' ? 'selected' : '' }}>Goods - G
                                EV (3 Wheeler)</option>
                            <option value="8" {{ old('permit', $rto->permit ?? '') == '8' ? 'selected' : '' }}>Taxi - T
                                (4 Wheeler)</option>
                            <option value="9" {{ old('permit', $rto->permit ?? '') == '9' ? 'selected' : '' }}>Passenger
                                - P (3 Wheeler)</option>
                            <option value="10" {{ old('permit', $rto->permit ?? '') == '10' ? 'selected' : ''
                                }}>Passenger - P EV (3 Wheeler)</option>
                            <option value="11" {{ old('permit', $rto->permit ?? '') == '11' ? 'selected' : ''
                                }}>Ambulance (Misc.)</option>
                        </select>
                        @error('permit') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Body Type -->
                    <div class="col-md-3">
                        <label class="form-label">Body Type</label>
                        <select name="body_type" id="body_type" class="form-control">
                            <option value="">Select Body Type</option>
                            <option value="1" {{ old('body_type', $rto->body_type ?? '') == '1' ? 'selected' : ''
                                }}>Complete</option>
                            <option value="2" {{ old('body_type', $rto->body_type ?? '') == '2' ? 'selected' : '' }}>CBC
                            </option>
                        </select>
                        @error('body_type') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Registration Type -->
                    <div class="col-md-3">
                        <label class="form-label">Registration Type</label>
                        <select name="registration_type" id="registration_type" class="form-control">
                            <option value="">Select Registration Type</option>
                            <option value="1" {{ old('registration_type', $rto->rgn_type ?? '') == '1' ? 'selected' : ''
                                }}>TRC Only</option>
                            <option value="2" {{ old('registration_type', $rto->rgn_type ?? '') == '2' ? 'selected' : ''
                                }}>Tax Only</option>
                            <option value="3" {{ old('registration_type', $rto->rgn_type ?? '') == '3' ? 'selected' : ''
                                }}>TRC + Tax</option>
                        </select>
                        @error('registration_type') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Registration No. Type -->
                    <div class="col-md-3">
                        <label class="form-label">Registration No. Type</label>
                        <select name="reg_no_type" id="reg_no_type" class="form-control">
                            <option value="">Select Registration No. Type</option>
                            <option value="1" {{ old('reg_no_type', $rto->rgn_no_type ?? '') == '1' ? 'selected' : ''
                                }}>Regular</option>
                            <option value="2" {{ old('reg_no_type', $rto->rgn_no_type ?? '') == '2' ? 'selected' : ''
                                }}>BH</option>
                            <option value="3" {{ old('reg_no_type', $rto->rgn_no_type ?? '') == '3' ? 'selected' : ''
                                }}>Special</option>
                        </select>
                        @error('reg_no_type') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Application No. -->
                    <div class="col-md-3" id="application_no_group">
                        <label class="form-label">RTO Application No.</label>
                        <input type="text" name="application_no" id="application_no" class="form-control text-uppercase"
                            value="{{ old('application_no', $rto->app_no ?? '') }}">
                        @error('application_no') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- TRC Number -->
                    <div class="col-md-3" id="trc_number_group">
                        <label class="form-label">TRC Number</label>
                        <input type="text" name="trc_number" id="trc_number" class="form-control text-uppercase"
                            value="{{ old('trc_number', $rto->trc_no ?? '') }}">
                        @error('trc_number') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- TRC Payment Ref. No. -->
                    <div class="col-md-3" id="bank_ref_no_group">
                        <label class="form-label">TRC Payment Ref. No.</label>
                        <input type="text" name="bank_ref_no" id="bank_ref_no" class="form-control text-uppercase"
                            value="{{ old('bank_ref_no', $rto->trc_payment_no ?? '') }}">
                        @error('bank_ref_no') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- TRC Copy (with preview) -->
                    <div class="col-md-6" id="trc_copy_group">
                        <label class="form-label">TRC Copy</label>
                        <input type="file" name="trc_copy" id="trc_copy" class="form-control" accept=".pdf">
                        @error('trc_copy') <span class="text-danger small">{{ $message }}</span> @enderror

                        <div class="mt-2 position-relative d-inline-block">
                            <img id="trc_preview" src="" alt="TRC Preview" style="max-width:200px; display:none;">
                            <i id="trc_pdf_icon" class="fas fa-file-pdf fa-3x text-danger" style="display:none;"></i>
                            <i id="discardTrc" class="fas fa-times-circle fa-2x text-danger"
                                style="display:none; position:absolute; top:-10px; right:-10px; cursor:pointer;"></i>
                        </div>
                    </div>

                    <!-- Tax Payment Ref. No. -->
                    <div class="col-md-3" id="tax_payment_ref_no_group">
                        <label class="form-label">Tax Payment Ref. No.</label>
                        <input type="text" name="tax_payment_ref_no" id="tax_payment_ref_no"
                            class="form-control text-uppercase"
                            value="{{ old('tax_payment_ref_no', $rto->tax_payment_bank_ref_no ?? '') }}">
                        @error('tax_payment_ref_no') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tax Receipt Copy -->
                    <div class="col-md-6" id="tax_receipt_copy_group">
                        <label class="form-label">Tax Receipt Copy</label>
                        <input type="file" name="tax_receipt_copy" id="tax_receipt_copy" class="form-control"
                            accept=".pdf">
                        @error('tax_receipt_copy') <span class="text-danger small">{{ $message }}</span> @enderror

                        <div class="mt-2 position-relative d-inline-block">
                            <img id="tax_preview" src="" alt="Tax Preview" style="max-width:200px; display:none;">
                            <i id="tax_pdf_icon" class="fas fa-file-pdf fa-3x text-danger" style="display:none;"></i>
                            <i id="discardTax" class="fas fa-times-circle fa-2x text-danger"
                                style="display:none; position:absolute; top:-10px; right:-10px; cursor:pointer;"></i>
                        </div>
                    </div>

                    <!-- Vehicle Registration No. (strict masking - no space) -->
                    <div class="col-md-4" id="vehicle_reg_no_group">
                        <label class="form-label">Vehicle Registration No.</label>
                        <input type="text" name="vehicle_reg_no" id="vehicle_reg_no" class="form-control text-uppercase"
                            value="{{ old('vehicle_reg_no', $rto->vh_rgn_no ?? '') }}">
                        @error('vehicle_reg_no') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success px-5 py-2">
                            <i class="la la-save"></i> Save RTO Details
                        </button>
                        <a href="{{ route('booking.pending-rto') }}" class="btn btn-secondary px-5 py-2 ms-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('after_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    $(document).ready(function() {

    // Masking apply + auto uppercase + no space
    function applyStrictMask(selector, maskPattern, placeholderText) {
        $(selector).mask(maskPattern, {
            placeholder: placeholderText
        }).on('input paste keyup', function() {
            let $this = $(this);
            let val = $this.val().toUpperCase().replace(/[^A-Z0-9]/g, ''); // सिर्फ A-Z, 0-9 allow + uppercase

            // Vehicle reg specific: max 10 chars, no space
            if ($this.attr('id') === 'vehicle_reg_no') {
                val = val.substring(0, 10);
            }

            $this.val(val);

            // Force mask re-apply after value change
            $this.unmask().mask(maskPattern, { placeholder: '' });
        }).on('blur', function() {
            // Blur पर placeholder वापस अगर empty
            if ($(this).val() === '') {
                $(this).attr('placeholder', placeholderText);
            }
        });
    }

    applyStrictMask('#trc_number', 'AAAAAAAAAAAAAAA', 'TRC123456789');
    applyStrictMask('#application_no', 'AAAAAAAAAAAAAAA', 'APP123456789');
    applyStrictMask('#bank_ref_no', 'AAAAAAAAAAAAAAAAAAAA', 'BANKREF123456789');
    applyStrictMask('#tax_payment_ref_no', 'AAAAAAAAAAAAAAAAAAAA', 'TAXREF123456789');

    $('#vehicle_reg_no').on('input paste keyup', function() {
        let $this = $(this);
        let val = $this.val().toUpperCase().replace(/\s+/g, ''); // remove spaces + uppercase
        $this.val(val);
    });
    // Real-time format validation + error below field
    function validateField(fieldId, regex, errorMsg) {
        const field = $('#' + fieldId);
        let error = field.next('.text-danger');

        if (error.length) error.remove();

        const val = field.val().trim();
        if (val && !regex.test(val)) {
            field.addClass('is-invalid');
            $('<span class="text-danger small d-block mt-1">' + errorMsg + '</span>').insertAfter(field);
        } else {
            field.removeClass('is-invalid');
        }
    }



    $('#trc_number').on('input change blur', function() {
        validateField('trc_number', /^[A-Z0-9]{10,15}$/,
            'TRC Number: 10-15 alphanumeric only');
    });

    $('#application_no').on('input change blur', function() {
        validateField('application_no', /^[A-Z0-9]{10,15}$/,
            'Application No.: 10-15 alphanumeric only');
    });

    $('#bank_ref_no, #tax_payment_ref_no').on('input change blur', function() {
        validateField(this.id, /^[A-Z0-9]{10,20}$/,
            'Ref No.: 10-20 alphanumeric only');
    });

    // Dynamic fields show/hide - strict matching
    const rulesData = @json($data['rto_rules'] ?? []);

    function updateFields() {
        const saleText = ($('#sale_type option:selected').text() || '').trim().toUpperCase();
        const permitText = ($('#permit option:selected').text() || '').trim().toUpperCase();
        const bodyText = ($('#body_type option:selected').text() || '').trim().toUpperCase();
        const regText = ($('#reg_no_type option:selected').text() || '').trim().toUpperCase();

        console.log('Selected:', { sale: saleText, permit: permitText, body: bodyText, reg: regText });

        const matchingRule = rulesData.find(rule => {
            return (rule.sale_type || '').trim().toUpperCase() === saleText &&
                   (rule.permit || '').trim().toUpperCase() === permitText &&
                   (rule.body_type || '').trim().toUpperCase() === bodyText &&
                   (rule.reg_no_type || '').trim().toUpperCase() === regText;
        });

        console.log('Matched Rule:', matchingRule);

        const groups = {
            'application_no_group': 'app_no',
            'trc_number_group': 'trc_number',
            'bank_ref_no_group': 'trc_pay',
            'trc_copy_group': 'trc_copy',
            'tax_payment_ref_no_group': 'tax_pay',
            'tax_receipt_copy_group': 'tax_copy',
            'vehicle_reg_no_group': 'veh_reg'
        };

        Object.keys(groups).forEach(id => {
            $('#' + id).hide();
        });

        if (matchingRule) {
            Object.keys(groups).forEach(id => {
                if ((matchingRule[groups[id]] || '').trim().toUpperCase() === 'YES') {
                    $('#' + id).show();
                }
            });
        }
    }

    $('#sale_type, #permit, #body_type, #reg_no_type').on('change', updateFields);
    updateFields(); // Initial
});
</script>
@endpush
