@extends(backpack_view('blank'))

@section('title', 'Pending RTO - Booking #' . $booking->id)

@push('after_styles')
<style>
    .proof-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 6px;
        color: #fff;
        font-size: 13px;
    }

    .proof-chip i {
        margin-right: 6px;
    }

    .file-name {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .btn-action {
        background: none;
        border: none;
        color: #fff;
        cursor: pointer;
    }

    .btn-download {
        color: #fff;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    .required-mark {
        color: #dc3545;
        margin-left: 4px;
    }

    .form-group.readonly-field {
        margin-bottom: 1.25rem;
    }

    .readonly-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.35rem;
        display: block;
    }

    .readonly-value {
        padding: 0.375rem 0.75rem;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: 38px;
        display: flex;
        align-items: center;
    }

    .readonly-value.text-danger {
        font-weight: 600;
    }
</style>
@endpush

@section('content')

<div class="container-fluid">

    @include(backpack_view('inc.alerts'))

    <!-- Invoice Details Card - Top (View Only) -->
    <div class="card card-body shadow-sm mb-4">
        <h2 class="mb-3"><i class="la la-file-invoice text-primary"></i> Invoice Details (RTO)</h2>
        <div class="row">

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">XB No.</label>
                <div class="readonly-value">
                    {{ $booking->sap_no ?? $booking->dms_no ?? $booking->id ?? '—' }}
                </div>
            </div>

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">Booking Date</label>
                <div class="readonly-value">
                    {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '—' }}
                </div>
            </div>

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">DMS OTF No.</label>
                <div class="readonly-value">
                    {{ $booking->dms_otf ?? '—' }}
                </div>
            </div>

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">Customer Name</label>
                <div class="readonly-value">
                    {{ $booking->name ?? '—' }}
                    @if($booking->care_of)
                    <small class="text-muted d-block mt-1">(C/o: {{ $booking->care_of }})</small>
                    @endif
                </div>
            </div>

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">Branch</label>
                <div class="readonly-value">
                    {{ $data['branch'] ?? '—' }}
                </div>
            </div>

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">Location</label>
                <div class="readonly-value">
                    @if($booking->location_id)
                    {{ $booking->location?->name ?? '—' }}
                    @else
                    {{ $booking->location_other ?: '—' }}
                    @endif
                </div>
            </div>

            <div class="col-md-4 form-group readonly-field">
                <label class="readonly-label">Model</label>
                <div class="readonly-value">
                    {{ $booking->model ?? '—' }}
                </div>
            </div>

            <div class="col-md-4 form-group readonly-field">
                <label class="readonly-label">Variant</label>
                <div class="readonly-value">
                    {{ $booking->variant ?? '—' }}
                </div>
            </div>

            <div class="col-md-4 form-group readonly-field">
                <label class="readonly-label">Color</label>
                <div class="readonly-value">
                    {{ $booking->color ?? '—' }}
                </div>
            </div>

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">Chassis No.</label>
                <div class="readonly-value">
                    {{ $booking->chassis_no ?? $booking->chasis_no ?? '—' }}
                </div>
            </div>

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">Invoice No.</label>
                <div class="readonly-value">
                    {{ $booking->inv_no ?? $booking->dms_invoice_number ?? '—' }}
                </div>
            </div>

            <div class="col-md-3 form-group readonly-field">
                <label class="readonly-label">Invoice Date</label>
                <div class="readonly-value">
                    {{ $booking->inv_date ? \Carbon\Carbon::parse($booking->inv_date)->format('d M Y') : '—' }}
                </div>
            </div>

        </div>
    </div>

    <!-- RTO Edit Form Card -->
    <div class="card card-body shadow-sm mb-4">
        <h2 class="mb-3">RTO / Registration Details</h2>

        <form id="rtoForm" method="POST" action="{{ route('booking.rto.update', $booking->id) }}"
            enctype="multipart/form-data">
            @csrf

            <div class="row g-3">

                <!-- Registration Data Section Title -->
                {{-- <div class="col-12">
                    <h5 class="mb-3 border-bottom pb-2">Registration Data</h5>
                </div> --}}

                <div class="col-md-3">
                    <label class="form-label">Trade Used</label>
                    <select name="trade_used" class="form-control form-select">
                        <option value="">Select Trade Used</option>
                        <option value="1" {{ old('trade_used', $rto->trade_used ?? '') == '1' ? 'selected' : '' }}>BKN
                            AD User 1 (RJ0730024TC)</option>
                        <option value="2" {{ old('trade_used', $rto->trade_used ?? '') == '2' ? 'selected' : '' }}>BKN
                            AD User 2 (RJ0730024TC)</option>
                        <option value="3" {{ old('trade_used', $rto->trade_used ?? '') == '3' ? 'selected' : '' }}>BKN
                            AD User 3 (RJ0730024TC)</option>
                        <option value="4" {{ old('trade_used', $rto->trade_used ?? '') == '4' ? 'selected' : '' }}>SUJ
                            AD (RJ44C0012TC)</option>
                        <option value="5" {{ old('trade_used', $rto->trade_used ?? '') == '5' ? 'selected' : '' }}>BKN
                            LMM L5 (RJ07C0056TC)</option>
                        <option value="6" {{ old('trade_used', $rto->trade_used ?? '') == '6' ? 'selected' : '' }}>BKN
                            LMM L3 (RJ07TC0322)</option>
                    </select>
                    @error('trade_used') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sale Type</label>
                    <select name="sale_type" id="sale_type" class="form-control form-select">
                        <option value="">Select Sale Type</option>
                        <option value="1" {{ old('sale_type', $rto->sale_type ?? '') == '1' ? 'selected' : '' }}>Within
                            State</option>
                        <option value="2" {{ old('sale_type', $rto->sale_type ?? '') == '2' ? 'selected' : '' }}>Outside
                            State</option>
                    </select>
                    @error('sale_type') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Permit</label>
                    <select name="permit" id="permit" class="form-control form-select">
                        <option value="">Select Permit</option>
                        <option value="1" {{ old('permit', $rto->permit ?? '') == '1' ? 'selected' : '' }}>Private - U/C
                            (4 Wheeler)</option>
                        <option value="2" {{ old('permit', $rto->permit ?? '') == '2' ? 'selected' : '' }}>Private - BH
                            (4 Wheeler)</option>
                        <option value="3" {{ old('permit', $rto->permit ?? '') == '3' ? 'selected' : '' }}>Private - EV
                            (4 Wheeler)</option>
                        <option value="4" {{ old('permit', $rto->permit ?? '') == '4' ? 'selected' : '' }}>Goods - G (4
                            Wheeler)</option>
                        <option value="5" {{ old('permit', $rto->permit ?? '') == '5' ? 'selected' : '' }}>Goods - G 3
                            Ton+ (4 Wheeler)</option>
                        <option value="6" {{ old('permit', $rto->permit ?? '') == '6' ? 'selected' : '' }}>Goods - G (3
                            Wheeler)</option>
                        <option value="7" {{ old('permit', $rto->permit ?? '') == '7' ? 'selected' : '' }}>Goods - G EV
                            (3 Wheeler)</option>
                        <option value="8" {{ old('permit', $rto->permit ?? '') == '8' ? 'selected' : '' }}>Taxi - T (4
                            Wheeler)</option>
                        <option value="9" {{ old('permit', $rto->permit ?? '') == '9' ? 'selected' : '' }}>Passenger - P
                            (3 Wheeler)</option>
                        <option value="10" {{ old('permit', $rto->permit ?? '') == '10' ? 'selected' : '' }}>Passenger -
                            P EV (3 Wheeler)</option>
                        <option value="11" {{ old('permit', $rto->permit ?? '') == '11' ? 'selected' : '' }}>Ambulance
                            (Misc.)</option>
                    </select>
                    @error('permit') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Body Type</label>
                    <select name="body_type" id="body_type" class="form-control form-select">
                        <option value="">Select Body Type</option>
                        <option value="1" {{ old('body_type', $rto->body_type ?? '') == '1' ? 'selected' : ''
                            }}>Complete</option>
                        <option value="2" {{ old('body_type', $rto->body_type ?? '') == '2' ? 'selected' : '' }}>CBC
                        </option>
                    </select>
                    @error('body_type') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Registration Type</label>
                    <select name="registration_type" id="registration_type" class="form-control form-select">
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

                <div class="col-md-3">
                    <label class="form-label">Registration No. Type</label>
                    <select name="reg_no_type" id="reg_no_type" class="form-control form-select">
                        <option value="">Select Registration No. Type</option>
                        <option value="1" {{ old('reg_no_type', $rto->rgn_no_type ?? '') == '1' ? 'selected' : ''
                            }}>Regular</option>
                        <option value="2" {{ old('reg_no_type', $rto->rgn_no_type ?? '') == '2' ? 'selected' : '' }}>BH
                        </option>
                        <option value="3" {{ old('reg_no_type', $rto->rgn_no_type ?? '') == '3' ? 'selected' : ''
                            }}>Special</option>
                    </select>
                    @error('reg_no_type') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3" id="application_no_group">
                    <label class="form-label">RTO Application No.</label>
                    <input type="text" name="application_no" id="application_no" class="form-control text-uppercase"
                        value="{{ old('application_no', $rto->app_no ?? '') }}">
                    @error('application_no') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3" id="trc_number_group">
                    <label class="form-label">TRC Number</label>
                    <input type="text" name="trc_number" id="trc_number" class="form-control text-uppercase"
                        value="{{ old('trc_number', $rto->trc_no ?? '') }}">
                    @error('trc_number') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3" id="bank_ref_no_group">
                    <label class="form-label">TRC Payment Ref. No.</label>
                    <input type="text" name="bank_ref_no" id="bank_ref_no" class="form-control text-uppercase"
                        value="{{ old('bank_ref_no', $rto->trc_payment_no ?? '') }}">
                    @error('bank_ref_no') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- <div class="col-md-6" id="trc_copy_group">
                    <label class="form-label">TRC Copy</label>
                    <input type="file" name="trc_copy" id="trc_copy" class="form-control" accept=".pdf">
                    @error('trc_copy') <span class="text-danger small">{{ $message }}</span> @enderror
                    <div class="mt-2 position-relative d-inline-block">
                        <img id="trc_preview" src="" alt="TRC Preview" style="max-width:200px; display:none;">
                        <i id="trc_pdf_icon" class="fas fa-file-pdf fa-3x text-danger" style="display:none;"></i>
                        <i id="discardTrc" class="fas fa-times-circle fa-2x text-danger"
                            style="display:none; position:absolute; top:-10px; right:-10px; cursor:pointer;"></i>
                    </div>
                </div> --}}
                <div class="col-md-3" id="trc_copy_group">

                    <label class="form-label">TRC Copy</label>

                    <input type="file" name="trc_copy" id="trc_copy" class="form-control" accept=".pdf">

                    @error('trc_copy')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror

                    <div id="trc_copy_chip" class="mt-2"></div>

                </div>

                <div class="col-md-3" id="tax_payment_ref_no_group">
                    <label class="form-label">Tax Payment Ref. No.</label>
                    <input type="text" name="tax_payment_ref_no" id="tax_payment_ref_no"
                        class="form-control text-uppercase"
                        value="{{ old('tax_payment_ref_no', $rto->tax_payment_bank_ref_no ?? '') }}">
                    @error('tax_payment_ref_no') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- <div class="col-md-6" id="tax_receipt_copy_group">
                    <label class="form-label">Tax Receipt Copy</label>
                    <input type="file" name="tax_receipt_copy" id="tax_receipt_copy" class="form-control" accept=".pdf">
                    @error('tax_receipt_copy') <span class="text-danger small">{{ $message }}</span> @enderror
                    <div class="mt-2 position-relative d-inline-block">
                        <img id="tax_preview" src="" alt="Tax Preview" style="max-width:200px; display:none;">
                        <i id="tax_pdf_icon" class="fas fa-file-pdf fa-3x text-danger" style="display:none;"></i>
                        <i id="discardTax" class="fas fa-times-circle fa-2x text-danger"
                            style="display:none; position:absolute; top:-10px; right:-10px; cursor:pointer;"></i>
                    </div>
                </div> --}}
                <div class="col-md-3" id="tax_receipt_copy_group">

                    <label class="form-label">Tax Receipt Copy</label>

                    <input type="file" name="tax_receipt_copy" id="tax_receipt_copy" class="form-control" accept=".pdf">

                    @error('tax_receipt_copy')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror

                    <div id="tax_receipt_copy_chip" class="mt-2"></div>

                </div>

                <div class="col-md-4" id="vehicle_reg_no_group">
                    <label class="form-label">Vehicle Registration No.</label>
                    <input type="text" name="vehicle_reg_no" id="vehicle_reg_no" class="form-control text-uppercase"
                        value="{{ old('vehicle_reg_no', $rto->vh_rgn_no ?? '') }}">
                    @error('vehicle_reg_no') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="col-12 mt-4 text-center">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="la la-save"></i> Save RTO Details
                    </button>
                    <a href="{{ route('booking.pending-rto') }}" class="btn btn-secondary btn-lg px-5 ms-3">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

</div>
<!-- Proof Preview Modal -->
<div class="modal fade" id="proofPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="proofFileName">Preview</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body text-center">

                <img id="proofImage" src="" class="img-fluid" style="display:none; max-height:600px;">

                <iframe id="proofPdf" src="" width="100%" height="600" style="display:none;">
                </iframe>

            </div>

            <div class="modal-footer">

                <a id="proofDownloadBtn" class="btn btn-success" download>
                    <i class="fas fa-download"></i> Download
                </a>

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>

            </div>

        </div>
    </div>
</div>

@endsection

@push('after_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Your existing JavaScript remains completely unchanged -->
<script>
    $(document).ready(function() {

        function handleFileUpload(input) {

            const file = input.files[0];
            if(!file) return;

            if(file.size > 2 * 1024 * 1024){

    Swal.fire({
        icon: 'error',
        title: 'File Too Large',
        text: 'File size must be less than 2MB',
        confirmButtonColor: '#3085d6'
    });

    input.value = "";
    return;
}

            let containerId = "";

            if(input.id === 'trc_copy') containerId = 'trc_copy_chip';
            if(input.id === 'tax_receipt_copy') containerId = 'tax_receipt_copy_chip';

            const container = document.getElementById(containerId);

            const url = URL.createObjectURL(file);
            const name = file.name;

            const chip = document.createElement("div");
chip.className = "proof-chip bg-primary mt-2";
chip.style.cursor = "pointer";

chip.innerHTML = `
<i class="fas fa-file-pdf text-danger"></i>
<span class="file-name">${name}</span>

<button type="button"
class="btn-action btn-download ms-2 download-chip">
<i class="fas fa-download"></i>
</button>

<button type="button"
class="btn-action text-danger ms-1 remove-chip">✖</button>
`;

chip.onclick = () => openProofPreview(url,'pdf',name);

chip.querySelector('.download-chip').onclick = function(e){
    e.stopPropagation();
    downloadFile(url,name);
}

chip.querySelector('.remove-chip').onclick = function(e){
    e.stopPropagation();
    input.value = "";
    container.innerHTML = "";
}

            container.innerHTML = "";
            container.appendChild(chip);

        }
        function openProofPreview(url,type,fileName){

    document.getElementById('proofFileName').innerText = fileName;

    // ⭐ download button set
    const downloadBtn = document.getElementById('proofDownloadBtn');
    downloadBtn.href = url;
    downloadBtn.setAttribute("download", fileName);

    if(type === 'pdf'){
        document.getElementById('proofImage').style.display = "none";

        const pdf = document.getElementById('proofPdf');
        pdf.style.display = "block";
        pdf.src = url;

    }else{

        document.getElementById('proofPdf').style.display = "none";

        const img = document.getElementById('proofImage');
        img.style.display = "block";
        img.src = url;
    }

    const modal = new bootstrap.Modal(document.getElementById('proofPreviewModal'), {
        backdrop:false
    });

    modal.show();
}

function downloadFile(url,name){
    const link = document.createElement('a');
    link.href = url;
    link.download = name;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

        document.getElementById('trc_copy').addEventListener('change', function(){
            handleFileUpload(this);
        });

        document.getElementById('tax_receipt_copy').addEventListener('change', function(){
            handleFileUpload(this);
        });
        // Masking apply + auto uppercase + no space
        function applyStrictMask(selector, maskPattern, placeholderText) {
            $(selector).mask(maskPattern, {
                placeholder: placeholderText
            }).on('input paste keyup', function() {
                let $this = $(this);
                let val = $this.val().toUpperCase().replace(/[^A-Z0-9]/g, '');
                if ($this.attr('id') === 'vehicle_reg_no') {
                    val = val.substring(0, 10);
                }
                $this.val(val);
                $this.unmask().mask(maskPattern, { placeholder: '' });
            }).on('blur', function() {
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
            let val = $this.val().toUpperCase().replace(/\s+/g, '');
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

        $('#trc_number').on('input change blur', function() { validateField('trc_number', /^[A-Z0-9]{10,15}$/, 'TRC Number: 10-15 alphanumeric only'); });
        $('#application_no').on('input change blur', function() { validateField('application_no', /^[A-Z0-9]{10,15}$/, 'Application No.: 10-15 alphanumeric only'); });
        $('#bank_ref_no, #tax_payment_ref_no').on('input change blur', function() { validateField(this.id, /^[A-Z0-9]{10,20}$/, 'Ref No.: 10-20 alphanumeric only'); });

        // Dynamic fields show/hide
        const rulesData = @json($data['rto_rules'] ?? []);
        function updateFields() {
    const normalize = str => (str || '').trim().replace(/\s+/g, ' ').toUpperCase();

    const saleText   = normalize($('#sale_type option:selected').text());
    const permitText = normalize($('#permit option:selected').text());
    const bodyText   = normalize($('#body_type option:selected').text());
    const regText    = normalize($('#reg_no_type option:selected').text());

    console.log('Selected (normalized):', { saleText, permitText, bodyText, regText });

    const matchingRule = rulesData.find(rule => {
        const ruleSale   = normalize(rule.sale_type);
        const rulePermit = normalize(rule.permit);
        const ruleBody   = normalize(rule.body_type);
        const ruleReg    = normalize(rule.reg_no_type);

        const isMatch = (ruleSale === saleText) &&
                        (rulePermit === permitText) &&
                        (ruleBody === bodyText) &&
                        (ruleReg === regText);

        // Optional: log first few mismatches for debugging
        if (!isMatch && rulesData.indexOf(rule) < 3) {
            console.log('Compared to rule:', { ruleSale, rulePermit, ruleBody, ruleReg });
        }

        return isMatch;
    });

    console.log('Matching rule found:', matchingRule ? 'YES' : 'NO', matchingRule);

    const groups = {
        'application_no_group':   'app_no',
        'trc_number_group':       'trc_number',
        'bank_ref_no_group':      'trc_pay',
        'trc_copy_group':         'trc_copy',
        'tax_payment_ref_no_group': 'tax_pay',
        'tax_receipt_copy_group': 'tax_copy',
        'vehicle_reg_no_group':   'veh_reg'
    };

    // Hide all first
    Object.keys(groups).forEach(id => $('#' + id).hide());

    if (matchingRule) {
        console.log('Showing fields based on rule:', matchingRule);
        Object.keys(groups).forEach(id => {
            const key = groups[id];
            if (String(matchingRule[key] || '').trim().toUpperCase() === 'YES') {
                $('#' + id).show();
            }
        });
    }
}

        $('#sale_type, #permit, #body_type, #reg_no_type').on('change', updateFields);
        updateFields(); // Initial call
    });
</script>
@endpush
