<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Booking Listings Configuration
    |--------------------------------------------------------------------------
    | Based on "Listing Headers.xlsx" file
    */

    'listings' => [

        'all_live' => [
            'title' => 'All Live Bookings',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'booking_age',
                'customer_name',
                'mobile',
                'customer_type',
                'branch',
                'location',
                'model',
                'variant',
                'color',
                'seating',
                'chassis_no',
                'booking_amount',
                'booking_source',
                'sales_consultant',
                'finance_mode',
                'financier',
                'loan_file_status',
                'dms_otf',
                'dms_so',
                'live_order',
                'stock_in_hand'
            ],
        ],

        'on_hold' => [
            'title' => 'On-Hold Bookings',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'booking_age',
                'customer_name',
                'mobile',
                'branch',
                'location',
                'model',
                'variant',
                'color',
                'chassis_no',
                'booking_amount',
                'sales_consultant'
            ],
        ],

        'cancelled' => [
            'title' => 'Cancelled Bookings',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'booking_age',
                'cancellation_date',
                'customer_name',
                'mobile',
                'branch',
                'location',
                'model',
                'variant',
                'color',
                'booking_amount',
                'sales_consultant'
            ],
        ],

        'invoiced' => [
            'title' => 'Invoiced Bookings',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'booking_age',
                'invoice_no',
                'invoice_date',
                'dealer_inv_no',
                'dealer_inv_date',
                'customer_name',
                'mobile',
                'branch',
                'location',
                'model',
                'variant',
                'color',
                'chassis_no',
                'booking_amount',
                'sales_consultant',
                'finance_mode',
                'financier'
            ],
        ],

        'refund_request' => [
            'title' => 'Refund Request',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'refund_request_date',
                'customer_name',
                'mobile',
                'booking_amount',
                'amount_to_refund',
                'sales_consultant',
                'branch'
            ],
        ],

        'refund_rejected' => [
            'title' => 'Refund Rejected',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'refund_request_date',
                'refund_rejection_date',
                'customer_name',
                'mobile',
                'booking_amount',
                'amount_to_refund',
                'sales_consultant'
            ],
        ],

        'refunded' => [
            'title' => 'Refunded',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'refund_request_date',
                'refund_date',
                'customer_name',
                'mobile',
                'booking_amount',
                'refunded_amount',
                'sales_consultant'
            ],
        ],

        'pending_dms' => [
            'title' => 'Pending DMS',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'booking_age',
                'customer_name',
                'mobile',
                'model',
                'variant',
                'dms_otf',
                'dms_so'
            ],
        ],

        'pending_so' => [
            'title' => 'Pending SO',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'model',
                'variant',
                'dms_otf',
                'dms_so'
            ],
        ],

        'pending_kyc' => [
            'title' => 'Pending KYC',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'pan_no',
                'adhar_no',
                'gstn'
            ],
        ],

        'pending_payment' => [
            'title' => 'Pending Payment',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'booking_amount',
                'receipt_no',
                'receipt_date'
            ],
        ],

        'pending_invoices' => [
            'title' => 'Pending Invoices',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'model',
                'variant',
                'booking_amount'
            ],
        ],

        'pending_insurance' => [
            'title' => 'Pending Insurance',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'insurance_source',
                'insurance_company'
            ],
        ],

        'pending_rto' => [
            'title' => 'Pending RTO',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'rto_sale_type',
                'rto_vh_rgn_no'
            ],
        ],

        'pending_deliveries' => [
            'title' => 'Pending Deliveries',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'del_date',
                'customer_name',
                'mobile',
                'model',
                'variant',
                'sales_consultant'
            ],
        ],

        'pending_reg_no' => [
            'title' => 'Pending Reg. No.',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'model',
                'variant',
                'rto_vh_rgn_no'
            ],
        ],

        'pending_do' => [
            'title' => 'Pending DO',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'model',
                'do_number',
                'expected_payout_pct'
            ],
        ],

        'dummy_bookings' => [
            'title' => 'Dummy Bookings',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile'
            ],
        ],

        'int_in_exch' => [
            'title' => 'Interested In Exchange',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'brand_make_1',
                'model_variant_1',
                'used_vehicle_exp_price'
            ],
        ],

        'int_in_scrappage' => [
            'title' => 'Interested In Scrappage',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile'
            ],
        ],

        'not_int_in_exch' => [
            'title' => 'Not Interested In Exchange',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile'
            ],
        ],

        'int_in_finance' => [
            'title' => 'Interested In Finance',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'finance_mode',
                'financier'
            ],
        ],

        'not_int_in_fin' => [
            'title' => 'Not Interested In Finance',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile'
            ],
        ],

        'retail_pending_fin_info' => [
            'title' => 'Retail Pending Fin Info',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'customer_name',
                'mobile',
                'finance_mode',
                'financier'
            ],
        ],

        'pending_payout' => [
            'title' => 'Pending Payout',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'invoice_no',
                'invoice_date',
                'customer_name',
                'mobile',
                'model',
                'variant',
                'do_number',
                'expected_payout_pct',
                'suggested_invoice_amount'
            ],
        ],

        'completed_payout' => [
            'title' => 'Completed Payout',
            'default_columns' => [
                's_no',
                'xb_no',
                'entry_date',
                'booking_date',
                'invoice_no',
                'invoice_date',
                'customer_name',
                'mobile',
                'do_number',
                'expected_payout_pct'
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | All Available Columns (Master Definition)
    |--------------------------------------------------------------------------
    | Key = internal key (use in default_columns & user selection)
    */
    'columns' => [

        's_no'                      => ['headerName' => 'S.No.',                    'field' => 'serial_no',             'width' => 80,   'sortable' => false, 'filter' => false],
        'xb_no'                     => ['headerName' => 'XB No.',                   'field' => 'booking_no',            'width' => 140,  'sortable' => true],
        'entry_date'                => ['headerName' => 'Entry Date',               'field' => 'created_at',            'width' => 110,  'type' => 'date'],
        'booking_date'              => ['headerName' => 'Booking Date',             'field' => 'booking_date',          'width' => 120,  'type' => 'date'],
        'booking_age'               => ['headerName' => 'Booking Age',              'field' => 'days_count',            'width' => 100,  'type' => 'number', 'cellClass' => 'text-right'],

        'cancellation_date'         => ['headerName' => 'Cancellation Date',        'field' => 'cancel_date',           'width' => 130,  'type' => 'date'],
        'refund_request_date'       => ['headerName' => 'Refund Request Date',      'field' => 'refund_request_date',   'width' => 140,  'type' => 'date'],
        'refund_date'               => ['headerName' => 'Refunded Date',            'field' => 'refund_date',           'width' => 130,  'type' => 'date'],
        'refund_rejection_date'     => ['headerName' => 'Refund Reject Date',       'field' => 'refund_rejection_date', 'width' => 140,  'type' => 'date'],

        'invoice_no'                => ['headerName' => 'Invoice No.',              'field' => 'invoice_no',            'width' => 130],
        'invoice_date'              => ['headerName' => 'Invoice Date',             'field' => 'invoice_date',          'width' => 120,  'type' => 'date'],
        'dealer_inv_no'             => ['headerName' => 'Dealer Invoice No.',       'field' => 'dealer_inv_no',         'width' => 140],
        'dealer_inv_date'           => ['headerName' => 'Dealer Invoice Date',      'field' => 'dealer_inv_date',       'width' => 140,  'type' => 'date'],

        'customer_name'             => ['headerName' => 'Customer Name',            'field' => 'name',                  'width' => 190,  'filter' => true],
        'customer_type'             => ['headerName' => 'Customer Type',            'field' => 'b_type',                'width' => 140],
        'mobile'                    => ['headerName' => 'Mobile No.',               'field' => 'mobile',                'width' => 130],
        'alt_mobile'                => ['headerName' => 'Alternate Mobile No.',     'field' => 'alt_mobile',            'width' => 140],
        'pan_no'                    => ['headerName' => 'PAN No.',                  'field' => 'pan_no',                'width' => 120],
        'adhar_no'                  => ['headerName' => 'Aadhaar No.',              'field' => 'adhar_no',              'width' => 140],
        'gstn'                      => ['headerName' => 'GSTIN',                    'field' => 'gstn',                  'width' => 130],
        'gender'                    => ['headerName' => 'Gender',                   'field' => 'gender',                'width' => 100],
        'occ'                       => ['headerName' => 'Occupation',               'field' => 'occ',                   'width' => 140],

        'branch'                    => ['headerName' => 'Branch',                   'field' => 'branch_name',           'width' => 150,  'filter' => true],
        'location'                  => ['headerName' => 'Location',                 'field' => 'location_name',         'width' => 160,  'filter' => true],

        'model'                     => ['headerName' => 'Model',                    'field' => 'model',                 'width' => 150],
        'variant'                   => ['headerName' => 'Variant',                  'field' => 'variant',               'width' => 160],
        'color'                     => ['headerName' => 'Color',                    'field' => 'color',                 'width' => 110],
        'seating'                   => ['headerName' => 'Seating',                  'field' => 'seating',               'width' => 100],
        'chassis_no'                => ['headerName' => 'Chassis No.',              'field' => 'chasis_no',             'width' => 150],

        'booking_amount'            => ['headerName' => 'Booking Amount',           'field' => 'booking_amount',        'width' => 140,  'type' => 'number', 'cellClass' => 'text-right'],
        'amount_to_refund'          => ['headerName' => 'Amount To Refund',         'field' => 'refund_amount',         'width' => 150,  'type' => 'number'],
        'refunded_amount'           => ['headerName' => 'Refunded Amount',          'field' => 'refunded_amount',       'width' => 150,  'type' => 'number'],

        'booking_source'            => ['headerName' => 'Booking Source',           'field' => 'b_source',              'width' => 140],
        'sales_consultant'          => ['headerName' => 'Sales Consultant',         'field' => 'consultant',            'width' => 170],

        'finance_mode'              => ['headerName' => 'Finance Mode',             'field' => 'fin_mode',              'width' => 140],
        'financier'                 => ['headerName' => 'Financier Name',           'field' => 'financier',             'width' => 160],
        'loan_file_status'          => ['headerName' => 'Loan File Status',         'field' => 'loan_status',           'width' => 150],

        'dms_otf'                   => ['headerName' => 'DMS OTF No.',              'field' => 'dms_otf',               'width' => 130],
        'dms_so'                    => ['headerName' => 'DMS SO No.',               'field' => 'dms_so',                'width' => 130],

        'live_order'                => ['headerName' => 'Live Order',               'field' => 'livecount',             'width' => 110,  'type' => 'number'],
        'stock_in_hand'             => ['headerName' => 'Stock In Hand',            'field' => 'stockcount',            'width' => 130,  'type' => 'number'],

        // Extra columns jo aapke Excel mein hain (add kar sakte ho)
        'del_date'                  => ['headerName' => 'Delivery Date',            'field' => 'del_date',              'width' => 130,  'type' => 'date'],
        'do_number'                 => ['headerName' => 'DO Number',                'field' => 'do_number',             'width' => 130],
        'expected_payout_pct'       => ['headerName' => 'Expected Payout %',        'field' => 'expected_payout_pct',   'width' => 150],
        'suggested_invoice_amount'  => ['headerName' => 'Suggested Invoice Amount', 'field' => 'suggested_invoice_amount', 'width' => 170, 'type' => 'number'],

        'brand_make_1'              => ['headerName' => 'Brand Make 1',             'field' => 'brand_make_1',          'width' => 140],
        'model_variant_1'           => ['headerName' => 'Model Variant 1',          'field' => 'model_variant_1',       'width' => 160],
        'used_vehicle_exp_price'    => ['headerName' => 'Used Vehicle Expected Price', 'field' => 'used_vehicle_exp_price', 'width' => 160, 'type' => 'number'],
        // ... agar aur chahiye to bata dena, main add kar dunga
    ]
];
