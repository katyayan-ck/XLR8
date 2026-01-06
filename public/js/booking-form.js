/**
 * BOOKING FORM CUSTOM JAVASCRIPT
 * Handles all custom logic, validations, and dynamic field management
 * To be included in the Backpack CRUD view
 */

document.addEventListener('DOMContentLoaded', function() {
    initBookingForm();
});

/**
 * Initialize all form components
 */
function initBookingForm() {
    initFlatpickr();
    initSelect2();
    initMasks();
    initValidation();
    initUppercaseInputs();
    bindEventListeners();
    setInitialState();
}

/**
 * Initialize Flatpickr date pickers
 */
function initFlatpickr() {
    const dateElements = document.querySelectorAll('.flatpickr-date');
    dateElements.forEach(el => {
        flatpickr(el, {
            format: 'd-m-Y',
            dateFormat: 'd-m-Y',
            altFormat: 'd-m-Y',
            altInput: true,
            minDate: 'today',
            enableTime: false,
            defaultDate: new Date()
        });
    });
}

/**
 * Initialize Select2 dropdowns
 */
function initSelect2() {
    const select2Elements = document.querySelectorAll('.select2');
    select2Elements.forEach(el => {
        $(el).select2({
            theme: 'bootstrap',
            width: '100%',
            language: {
                inputTooShort: function() {
                    return 'Type to search...';
                },
                noResults: function() {
                    return 'No matching results';
                }
            },
            allowClear: true,
            placeholder: 'Select an option'
        });
    });
}

/**
 * Initialize input masks for phone, PAN, Aadhar, etc.
 */
function initMasks() {
    // Phone mask - 10 digits
    const phoneMasks = document.querySelectorAll('.phone-mask');
    phoneMasks.forEach(el => {
        IMask(el, {
            mask: '0000000000',
            lazy: false
        });
    });

    // PAN mask - AAAAA9999A
    const panMasks = document.querySelectorAll('.pan-mask');
    panMasks.forEach(el => {
        IMask(el, {
            mask: /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/,
            lazy: false
        });
    });

    // Aadhar mask - 12 digits
    const aadharMasks = document.querySelectorAll('.aadhar-mask');
    aadharMasks.forEach(el => {
        IMask(el, {
            mask: '000000000000',
            lazy: false
        });
    });
}

/**
 * Initialize form validation
 */
function initValidation() {
    const form = document.querySelector('form');
    if (!form) return;

    // jQuery validation if available
    if (jQuery && jQuery.validator) {
        jQuery(form).validate({
            rules: {
                name: { required: true, minlength: 2 },
                mobile: { required: true, digits: true, minlength: 10, maxlength: 10 },
                booking_date: { required: true },
                booking_amount: { required: true, number: true, min: 0 },
                receipt_no: { required: true },
                receipt_date: { required: true },
                customer_type: { required: true },
                customer_cat: { required: true },
                col_type: { required: true },
                booking_mode: { required: true },
                fin_mode: { required: true },
                location: { required: true },
            },
            messages: {
                name: { required: 'Customer name is required' },
                mobile: {
                    required: 'Mobile number is required',
                    digits: 'Please enter 10 digits',
                    minlength: 'Mobile must be 10 digits'
                },
                booking_date: { required: 'Booking date is required' },
                booking_amount: { required: 'Booking amount is required', number: 'Enter a valid amount' },
            },
            errorClass: 'is-invalid',
            validClass: 'is-valid'
        });
    }
}

/**
 * Convert text inputs to uppercase
 */
function initUppercaseInputs() {
    const uppercaseFields = ['name', 'care_of_name', 'ref_customer_name', 'occupation'];
    uppercaseFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', function() {
                this.value = capitalizeWords(this.value);
            });
        }
    });
}

/**
 * Bind event listeners for dynamic fields
 */
function bindEventListeners() {
    const customerDobInput = document.getElementById('customer_dob');
    if (customerDobInput) {
        customerDobInput.addEventListener('change', calculateAge);
    }

    const expectedPriceInput = document.getElementById('expected_price');
    const offeredPriceInput = document.getElementById('offered_price');
    if (expectedPriceInput) expectedPriceInput.addEventListener('input', calculatePriceGap);
    if (offeredPriceInput) offeredPriceInput.addEventListener('input', calculatePriceGap);

    const mobileInput = document.getElementById('mobile');
    if (mobileInput) {
        mobileInput.addEventListener('change', attachDuplicateCheck);
    }

    const collectionTypeSelect = document.getElementById('col_type');
    if (collectionTypeSelect) {
        collectionTypeSelect.addEventListener('change', toggleCollectionFields);
    }

    const bookingModeSelect = document.getElementById('booking_mode');
    if (bookingModeSelect) {
        bookingModeSelect.addEventListener('change', togglePurchaseFields);
    }

    const finModeSelect = document.getElementById('fin_mode');
    if (finModeSelect) {
        finModeSelect.addEventListener('change', toggleFinanceFields);
    }

    const accessoriesSelect = document.getElementById('accessories');
    if (accessoriesSelect) {
        accessoriesSelect.addEventListener('change', updateAccessoriesAmount);
    }

    const financierSelect = document.getElementById('financier');
    if (financierSelect) {
        financierSelect.addEventListener('change', updateFinancierShortName);
    }

    const segmentSelect = document.getElementById('segment_id');
    if (segmentSelect) {
        segmentSelect.addEventListener('change', updateModels);
    }

    const modelSelect = document.getElementById('model');
    if (modelSelect) {
        modelSelect.addEventListener('change', updateVariants);
    }
}

/**
 * Set initial visibility state based on form values
 */
function setInitialState() {
    const collectionType = document.getElementById('col_type');
    const bookingMode = document.getElementById('booking_mode');
    const finMode = document.getElementById('fin_mode');
    const customerType = document.getElementById('customer_type');

    if (collectionType && collectionType.value) {
        toggleCollectionFields();
    }
    if (bookingMode && bookingMode.value) {
        togglePurchaseFields();
    }
    if (finMode && finMode.value) {
        toggleFinanceFields();
    }
    if (customerType && customerType.value) {
        toggleCustomerFields();
    }
}

/**
 * Calculate age from date of birth
 */
function calculateAge() {
    const dobInput = document.getElementById('customer_dob');
    const ageInput = document.getElementById('customer_age');

    if (!dobInput || !dobInput.value) return;

    try {
        // Parse date format: dd-mm-yyyy
        const parts = dobInput.value.split('-');
        if (parts.length !== 3) return;

        const dob = new Date(parts[2], parts[1] - 1, parts[0]);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        if (ageInput) {
            ageInput.value = age >= 0 ? age : '';
        }
    } catch (error) {
        console.error('Error calculating age:', error);
    }
}

/**
 * Calculate price gap between expected and offered price
 */
function calculatePriceGap() {
    const expectedPrice = parseFloat(document.getElementById('expected_price')?.value || 0);
    const offeredPrice = parseFloat(document.getElementById('offered_price')?.value || 0);
    const differenceInput = document.getElementById('difference');

    if (differenceInput) {
        const difference = offeredPrice - expectedPrice;
        differenceInput.value = difference.toFixed(2);
    }
}

/**
 * Toggle collection type specific fields
 */
function toggleCollectionFields() {
    const collectionType = document.getElementById('col_type')?.value;

    // Hide all collection specific wrappers first
    const dsaWrapper = document.getElementById('dsa_details_wrapper');
    if (dsaWrapper) dsaWrapper.style.display = 'none';

    // Show based on collection type
    if (collectionType === '3') { // Field Collection (DSA)
        if (dsaWrapper) dsaWrapper.style.display = 'block';
        toggleRequiredMark('dsa_details', true);
    } else {
        toggleRequiredMark('dsa_details', false);
    }
}

/**
 * Toggle purchase type specific fields
 */
function togglePurchaseFields() {
    const bookingMode = document.getElementById('booking_mode')?.value;

    // Hide all exchange/purchase wrappers
    const exchangeWrappers = [
        'vh_id_wrapper',
        'registration_no_wrapper',
        'chassis_wrapper',
        'odometer_reading_wrapper',
        'exchange_bonus_wrapper',
        'make_order_wrapper'
    ];

    const newVehicleWrappers = [
        'segment_wrapper',
        'model_wrapper',
        'variant_wrapper',
        'accessories_wrapper',
        'apack_amount',
        'expected_price',
        'offered_price',
        'expected_del_date'
    ];

    exchangeWrappers.forEach(wrapperId => {
        const wrapper = document.getElementById(wrapperId);
        if (wrapper) wrapper.style.display = 'none';
    });

    newVehicleWrappers.forEach(wrapperId => {
        const wrapper = document.getElementById(wrapperId);
        if (wrapper) wrapper.parentElement.style.display = 'none';
    });

    // Show relevant fields based on mode
    if (bookingMode === 'Exchange' || bookingMode === 'Used Car Purchase') {
        exchangeWrappers.forEach(wrapperId => {
            const wrapper = document.getElementById(wrapperId);
            if (wrapper) wrapper.style.display = 'block';
        });
        if (bookingMode === 'Exchange') {
            document.getElementById('exchange_bonus_wrapper').style.display = 'block';
        }
    } else if (bookingMode === 'New') {
        newVehicleWrappers.forEach(wrapperId => {
            const wrapper = document.getElementById(wrapperId);
            if (wrapper) wrapper.parentElement.style.display = 'block';
        });
        toggleRequiredMark('segment_id', true);
        toggleRequiredMark('model', true);
        toggleRequiredMark('variant', true);
        toggleRequiredMark('expected_price', true);
    }
}

/**
 * Toggle finance mode specific fields
 */
function toggleFinanceFields() {
    const financeMode = document.getElementById('fin_mode')?.value;
    const financierWrapper = document.getElementById('financier_wrapper');

    if (financierWrapper) {
        financierWrapper.style.display = financeMode === 'Finance' ? 'block' : 'none';
    }

    toggleRequiredMark('financier', financeMode === 'Finance');
}

/**
 * Toggle customer type specific fields
 */
function toggleCustomerFields() {
    const customerType = document.getElementById('customer_type')?.value;
    const customerCat = document.getElementById('customer_cat')?.value;

    // Show GST fields for Firm category
    const gstWrapper = document.getElementById('gstn')?.parentElement;
    if (gstWrapper) {
        gstWrapper.style.display = (customerCat === 'Firm') ? 'block' : 'none';
    }
}

/**
 * Toggle generic required mark and validation
 */
function toggleRequiredMark(fieldId, isRequired) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const wrapper = field.closest('.form-group');
    const label = wrapper?.querySelector('label');

    if (label) {
        const requiredMark = label.querySelector('.required-mark');
        if (isRequired && !requiredMark) {
            label.innerHTML += ' <span class="required-mark" style="color: red;">*</span>';
        } else if (!isRequired && requiredMark) {
            requiredMark.remove();
        }
    }

    field.required = isRequired;
}

/**
 * Toggle field visibility and required state
 */
function toggleFields() {
    const referredBy = document.getElementById('referred_by')?.value;

    const referralWrappers = [
        'ref_customer_name_wrapper',
        'ref_mobile_no_wrapper',
        'ref_existing_model_wrapper',
        'ref_variant_wrapper',
        'ref_chassis_reg_no_wrapper',
        'dsa_details_wrapper'
    ];

    referralWrappers.forEach(wrapperId => {
        const wrapper = document.getElementById(wrapperId);
        if (wrapper) {
            wrapper.style.display = (referredBy === 'Existing Customer' || referredBy === 'DSA') ? 'block' : 'none';
        }
    });
}

/**
 * Update accessories amount based on selected items
 */
function updateAccessoriesAmount() {
    const accessoriesSelect = document.getElementById('accessories');
    const amountInput = document.getElementById('apack_amount');

    if (!accessoriesSelect || !amountInput) return;

    const selectedOptions = accessoriesSelect.selectedOptions;
    let totalAmount = 0;

    Array.from(selectedOptions).forEach(option => {
        const amount = parseFloat(option.dataset.amount || 0);
        totalAmount += amount;
    });

    amountInput.value = totalAmount.toFixed(2);
}

/**
 * Update financier short name when selected
 */
function updateFinancierShortName() {
    const financierSelect = document.getElementById('financier');
    const shortNameInput = document.getElementById('financier_short_name');

    if (!financierSelect || !shortNameInput) return;

    const selectedOption = financierSelect.selectedOptions[0];
    if (selectedOption) {
        shortNameInput.value = selectedOption.dataset.shortName || '';
    }
}

/**
 * Update models based on selected segment
 */
function updateModels() {
    const segmentSelect = document.getElementById('segment_id');
    const modelSelect = document.getElementById('model');

    if (!segmentSelect || !modelSelect) return;

    const segmentId = segmentSelect.value;
    if (!segmentId) {
        $(modelSelect).empty().trigger('change');
        return;
    }

    // AJAX call to fetch models
    $.ajax({
        url: '/api/models-by-segment/' + segmentId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $(modelSelect).empty();
            data.forEach(model => {
                $(modelSelect).append(
                    $('<option></option>')
                        .attr('value', model.id)
                        .text(model.name)
                );
            });
            $(modelSelect).trigger('change');
        },
        error: function(error) {
            console.error('Error fetching models:', error);
            handleAjaxError(error);
        }
    });
}

/**
 * Update variants based on selected model
 */
function updateVariants() {
    const modelSelect = document.getElementById('model');
    const variantSelect = document.getElementById('variant');

    if (!modelSelect || !variantSelect) return;

    const modelId = modelSelect.value;
    if (!modelId) {
        $(variantSelect).empty().trigger('change');
        return;
    }

    // AJAX call to fetch variants
    $.ajax({
        url: '/api/variants-by-model/' + modelId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $(variantSelect).empty();
            data.forEach(variant => {
                $(variantSelect).append(
                    $('<option></option>')
                        .attr('value', variant.id)
                        .text(variant.name)
                        .data('seating', variant.seating)
                        .data('year', variant.manufacturing_year)
                );
            });
            $(variantSelect).trigger('change');
        },
        error: function(error) {
            console.error('Error fetching variants:', error);
            handleAjaxError(error);
        }
    });
}

/**
 * Attach duplicate mobile number check
 */
function attachDuplicateCheck() {
    const mobileInput = document.getElementById('mobile');
    if (!mobileInput || !mobileInput.value) return;

    $.ajax({
        url: '/api/check-duplicate-mobile',
        type: 'POST',
        data: { mobile: mobileInput.value },
        success: function(response) {
            if (response.exists) {
                mobileInput.classList.add('is-invalid');
                showErrorModal('Duplicate Mobile', 'This mobile number is already registered in the system.');
                resetDuplicateState();
            } else {
                mobileInput.classList.remove('is-invalid');
            }
        },
        error: function(error) {
            console.error('Error checking mobile:', error);
        }
    });
}

/**
 * Reset duplicate check state
 */
function resetDuplicateState() {
    const mobileInput = document.getElementById('mobile');
    if (mobileInput) {
        setTimeout(() => {
            mobileInput.classList.remove('is-invalid');
        }, 3000);
    }
}

/**
 * Capitalize words in text
 */
function capitalizeWords(str) {
    if (!str) return '';
    return str
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

/**
 * Show error modal
 */
function showErrorModal(title, message) {
    // Bootstrap modal if available
    const modalHtml = `
        <div class="modal fade" id="errorModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">${message}</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    let modal = document.getElementById('errorModal');
    if (!modal) {
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        modal = document.getElementById('errorModal');
    }

    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

/**
 * Handle AJAX errors
 */
function handleAjaxError(error) {
    console.error('AJAX Error:', error);
    let message = 'An error occurred while loading data.';

    if (error.responseJSON && error.responseJSON.message) {
        message = error.responseJSON.message;
    }

    showErrorModal('Error', message);
}

/**
 * Reset all form fields
 */
function resetFields() {
    const form = document.querySelector('form');
    if (form) {
        form.reset();
        setInitialState();
    }
}

/**
 * Reset accessories
 */
function resetAccessories() {
    const accessoriesSelect = document.getElementById('accessories');
    if (accessoriesSelect) {
        $(accessoriesSelect).val(null).trigger('change');
        updateAccessoriesAmount();
    }
}

/**
 * Preview file before upload
 */
function previewFile(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (input && input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Clear uploaded image
 */
function clearImage(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (input) input.value = '';
    if (preview) preview.style.display = 'none';
}

/**
 * Populate select dropdown from array
 */
function populateSelect(selectId, data, valueKey = 'id', textKey = 'name') {
    const select = document.getElementById(selectId);
    if (!select) return;

    $(select).empty();
    data.forEach(item => {
        $(select).append(
            $('<option></option>')
                .attr('value', item[valueKey])
                .text(item[textKey])
        );
    });
    $(select).trigger('change');
}
