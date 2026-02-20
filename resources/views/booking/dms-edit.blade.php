@extends(backpack_view('blank'))

@section('title', 'Edit DMS - Booking #' . $booking->id)

@push('after_styles')
<link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .required-mark {
        color: #dc3545;
        margin-left: 4px;
    }
    .is-valid {
        border-color: #28a745 !important;
        box-shadow: 0 0 4px rgba(40,167,69,.4) !important;
    }
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 4px rgba(220,53,69,.4) !important;
    }
</style>
@endpush

@section('content')

<form id="dms-form" method="POST" action="{{ route('dms.update', $booking->id) }}">
    @csrf
    @method('PUT')

    <div class="row">

        <div class="col-md-4 form-group">
            <label>
                DMS Booking No.
                <span class="required-mark">*</span>
            </label>
            <input type="text" name="dms_no" id="dms_no" class="form-control"
                   value="{{ old('dms_no', $booking->dms_no) }}" required>
        </div>

        <div class="col-md-4 form-group">
            <label>
                DMS OTF No.
                <span class="required-mark">*</span>
            </label>
            <input type="text" name="dms_otf" id="dms_otf" class="form-control"
                   value="{{ old('dms_otf', $booking->dms_otf) }}" required>
        </div>

        <div class="col-md-4 form-group">
            <label>
                DMS OTF Date
                <span class="required-mark">*</span>
            </label>
            <input type="text" name="otf_date" id="otf_date" class="form-control flatpickr-date"
                   value="{{ old('otf_date', $booking->otf_date ? \Carbon\Carbon::parse($booking->otf_date)->format('d-m-Y') : '') }}"
                   required>
            <input type="hidden" name="hidden_otf_date" id="hidden_otf_date"
                   value="{{ old('hidden_otf_date', $booking->otf_date ? $booking->otf_date : '') }}">
        </div>

        @if($booking->order == 2)
        <div class="col-md-4 form-group">
            <label>
                DMS SO No.
                <span class="required-mark">*</span>
            </label>
            <input type="text" name="dms_so" id="dms_so" class="form-control"
                   value="{{ old('dms_so', $booking->dms_so) }}" required>
        </div>
        @endif

        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="la la-save"></i> Save Order Details
            </button>
        </div>

    </div>
</form>

@endsection

@push('after_scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>
$(document).ready(function () {

    // ── Date picker ───────────────────────────────────────
    $("#otf_date").flatpickr({
        dateFormat: "d-m-Y",
        maxDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates[0]) {
                $('#hidden_otf_date').val(instance.formatDate(selectedDates[0], 'Y-m-d'));
            }
        }
    });

    // ── Input masks (same as your previous file) ──────────
    $('#dms_no').mask('B-00000000', { placeholder: 'B-12345678' });
    $('#dms_otf').mask('OTF00A000000', { placeholder: 'OTF00A123456' });

    @if($booking->order == 2)
    $('#dms_so').mask('0000000000', { placeholder: '0111763881' });
    @endif

    // Force uppercase for DMS fields
    $('#dms_no, #dms_otf').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // ── jQuery Validation ─────────────────────────────────
    $.validator.addMethod("dmsFormat", function(value) {
        return this.optional(this.element) || /^B-\d{8}$/.test(value);
    }, "Format: B- followed by 8 digits");

    $.validator.addMethod("otfFormat", function(value) {
        return this.optional(this.element) || /^OTF\d{2}[A-Z]\d{6}$/.test(value);
    }, "Format: OTF00A123456");

    @if($booking->order == 2)
    $.validator.addMethod("soFormat", function(value) {
        return this.optional(this.element) || /^\d{10}$/.test(value);
    }, "10 digits required");
    @endif

    $('#dms-form').validate({
        rules: {
            dms_no: {
                required: true,
                dmsFormat: true
            },
            dms_otf: {
                required: true,
                otfFormat: true
            },
            otf_date: {
                required: true
            },
            @if($booking->order == 2)
            dms_so: {
                required: true,
                soFormat: true
            }
            @endif
        },
        messages: {
            dms_no: {
                required: "DMS No. is required",
                dmsFormat: "Invalid format (B-12345678)"
            },
            dms_otf: {
                required: "DMS OTF is required",
                otfFormat: "Invalid format (OTF00A123456)"
            },
            otf_date: "OTF Date is required",
            @if($booking->order == 2)
            dms_so: {
                required: "DMS SO is required",
                soFormat: "Must be 10 digits"
            }
            @endif
        },
        errorElement: 'span',
        errorClass: 'text-danger small d-block mt-1',
        highlight: function(element) {
            $(element).removeClass('is-valid').addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        }
    });
});
</script>
@endpush