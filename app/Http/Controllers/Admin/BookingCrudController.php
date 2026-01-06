<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Http\Requests\BookingRequest;
use DataTables, auth;
use Illuminate\Validation\Rule;
use App\Http\Requests\MyBookingRequest; // We'll create this next
use App\Services\BookingService;             // New service class

use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Booking;
use App\Models\Bookingamount;
use App\Models\XVehicleMaster; // Import the model
use App\Models\Stock; // Import the Stock model
use App\Models\Branches; // Import the Branch model
use App\Models\Xessories;
use App\Models\Xl_Refunds;
use App\Models\Xl_DSA_Master;
use App\Models\X_Branch;
use App\Models\X_Location;
use App\Models\X_Vh_Stock;
use App\Models\X_Vh_Order;
use App\Models\EnumMaster;
use App\Models\PinCodes;
use App\Models\XExchange;
use App\Models\XFinance;
use App\Models\XlInsurer;

use App\Models\XlRto;

use App\Models\XlDelivery;

use App\Models\XlInsurance;
use App\Models\XlFinancier;

use App\Models\XlRtoRules;

use App\Helpers\CommonHelper;
use App\Helpers\XCommonHelper;
use App\Helpers\XpricingHelper;

use App\Helpers\ChatHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Booking::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/booking');
        CRUD::setEntityNameStrings('booking', 'bookings');
    }

    protected function setupListOperation()
    {
        $this->crud->setListView('booking.list');

        $bookings = \App\Models\Booking::query()
            ->with(['branch', 'location'])
            ->whereIn('status', [1, 8])
            // ->where('b_type', 'Active')
            ->orderBy('id', 'desc')
            ->get();

        $bookings = $bookings->map(function ($booking) {
            // Consultant name
            $consultant = \App\Models\User::find($booking->consultant);
            $consultantName = $consultant?->name ?? 'Unknown';

            // Collected By name
            $collectedByUser = $booking->col_by ? \App\Models\User::find($booking->col_by) : null;
            $collectedByName = $collectedByUser?->name ?? 'N/A';

            // Status badge (tumhare pass method hai toh use karo, warna simple text)
            $statusBadge = $this->getStatusBadge($booking->status ?? 8);

            // Booking No
            $bookingNo = $booking->sap_no
                ? 'SAP: ' . $booking->sap_no
                : ($booking->dms_no ? 'DMS: ' . $booking->dms_no : 'N/A');

            // Action buttons - PURE PHP (no Blade syntax!)
            $action = '<div class="btn-group" role="group">
            <a href="' . backpack_url("booking/{$booking->id}/edit") . '" class="btn btn-sm btn-link text-primary me-3" title="Edit">
                <i class="la la-edit"></i>
            </a>
            <a href="' . backpack_url("booking/{$booking->id}/show") . '" class="btn btn-sm btn-link text-info me-2" title="View">
                <i class="la la-eye"></i>
            </a>

        </div>';

            // Object return karo (array nahi!)
            $booking->id = $booking->id;
            $booking->booking_no = $bookingNo;
            $booking->booking_date = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') : '-';
            $booking->name = $booking->name ?? '-';
            $booking->mobile = $booking->mobile ?? '-';
            $booking->model = $booking->model ?? '-';
            $booking->variant = $booking->variant ?? '-';
            $booking->color = $booking->color ?? '-';
            $booking->booking_amount = $booking->booking_amount ? number_format($booking->booking_amount) : '0';
            $booking->booking_source = $booking->b_source ?? '-';
            $booking->collection_type = match ((int)$booking->col_type) {
                1 => 'Receipt',
                2 => 'Dealer',
                3 => 'DSA',
                default => 'N/A'
            };
            $booking->collected_by = $collectedByName;
            $booking->dsa_name = $booking->dsa_id ?? '-';
            $booking->fin_mode = $booking->fin_mode ?? '-';
            $booking->financier = $booking->financier ?? '-';
            $booking->consultant = $consultantName;
            $booking->branch = $booking->branch?->name ?? '-';
            $booking->location = $booking->location?->name ?? '-';
            $booking->b_mode = $booking->b_mode ?? '-';
            $booking->b_type = $booking->b_type ?? '-';
            $booking->status = $statusBadge;
            $booking->days_count = \Carbon\Carbon::parse($booking->created_at)->diffInDays(now());
            $booking->action = $action; // action field set

            return $booking;
        });

        $this->data['bookings'] = $bookings;
        $this->data['title'] = 'All Live Bookings';
    }

    private function getStatusBadge($status)
    {
        return match ((int)$status) {
            1 => '<span class="badge badge-success">Live</span>',
            2 => '<span class="badge badge-primary">Invoiced</span>',
            3 => '<span class="badge badge-danger">Cancelled</span>',
            4 => '<span class="badge badge-warning">Refund Queued</span>',
            5 => '<span class="badge badge-info">Refunded</span>',
            6 => '<span class="badge badge-warning text-dark">On Hold</span>',
            7 => '<span class="badge badge-dark">Refund Rejected</span>',
            8 => '<span class="badge badge-secondary">Pending</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(BookingRequest::class);
        $this->crud->setCreateView('booking.add');

        $data = [];

        // Yeh sab object banaye — arrow syntax ke liye
        $data['branches']       = collect(CommonHelper::getBranches())->map(fn($b) => (object) $b);
        $data['allusers']       = collect(XpricingHelper::selectUsers())->map(fn($u) => (object) $u);
        $data['financiers']     = collect(\App\Models\XlFinancier::select('id', 'name', 'short_name')->get()->toArray())->map(fn($f) => (object) $f);
        $data['saleconsultants'] = collect(XpricingHelper::selectfsc())->map(fn($s) => (object) $s);

        // Segments — sirf ek baar, object bana ke
        $data['segments']       = collect(XpricingHelper::getSegments())->map(fn($s) => (object) $s);

        // Yeh initially empty rahenge (AJAX se fill honge)
        $data['models']         = [];
        $data['variants']       = [];
        $data['colors']         = [];

        $data['locations']      = [];
        $data['person_id']      = auth()->id();

        // DSA Details — object bana do
        $data['dsa_details'] = \App\Models\Xl_DSA_Master::all()->map(function ($dsa) {
            return (object) [
                'id'       => $dsa->id,
                'name'     => $dsa->name,
                'mobile'   => $dsa->mobile,
                'email'    => $dsa->email,
                'location' => $dsa->dlocation,
            ];
        });

        // Enum Master
        $data['enum_master'] = \App\Models\EnumMaster::where('master_id', 94)
            ->where('status', 1)
            ->orderBy('value')
            ->get()
            ->map(fn($em) => (object) ['id' => $em->id, 'value' => $em->value]);

        // Final pass
        $this->data['data'] = $data;
    }


    public function store(Request $request)
    {
        // Debug ke liye (baad mein hata dena)
        // dd($request->all());

        $pending = 0;
        $pendingFields = [];

        // ====== VALIDATION (purane save() jaisi hi, lekin blade field names se) ======
        $validator = Validator::make($request->all(), [
            'customertype' => 'required|string|max:255',
            'user' => 'nullable',
            'hiddenbookingdate' => 'nullable|date',  // booking_date → hiddenbookingdate
            'refrenceno' => 'nullable|string|max:255',  // refrence_no → refrenceno
            'dsadetails' => 'nullable|string|max:255',  // dsa_details → dsadetails
            'branch' => 'required|integer',
            'location' => 'required|integer',
            'segmentid' => 'required|integer',  // segment_id → segmentid
            'model' => 'required|string|max:255',
            'variant' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'careof' => 'nullable|string|max:255',  // care_of → careof
            'careofname' => 'nullable|string|max:255',  // care_of_name → careofname
            'mobile' => 'required|string|max:15',
            'altmobile' => 'nullable|string|max:15',  // alt_mobile → altmobile
            'panno' => 'nullable|string|max:10',  // pan_no → panno
            'adharno' => 'nullable|string|max:15',  // adhar_no → adharno
            'dmsotf' => 'nullable|string|max:255',  // dms_otf → dmsotf (assuming blade mein yeh hai)
            'dmss o' => 'nullable|string|max:255',  // dms_so → dmss o (fix if needed)
            'chassis' => 'nullable|string|max:255',
            'deliverytype' => 'required|string|max:255',  // delivery_type → deliverytype
            'hiddenexpecteddeldate' => 'nullable|date',  // expected_del_date → hiddenexpecteddeldate
            'finmode' => 'required|string|max:255',  // fin_mode → finmode
            'financier' => 'nullable|string|max:255',
            'loanstatus' => 'nullable|string|max:255',  // loan_status → loanstatus
            'accessories' => 'nullable|array',
            'accessories.*' => 'integer',
            'saleconsultant' => 'required',
            'apackamount' => 'required',  // apack_amount → apackamount
            'seating' => 'nullable|integer',
            'details' => 'nullable|string',
            'referredby' => 'nullable|string|max:255',  // referred_by → referredby
            'refcustomername' => 'nullable|string|max:255',  // ref_customer_name → refcustomername
            'refmobileno' => 'nullable|string|max:15',  // ref_mobile_no → refmobileno
            'refexistingmodel' => 'nullable|string|max:255',  // ref_existing_model → refexistingmodel
            'refvariant' => 'nullable|string|max:255',
            'refchassisregno' => 'nullable|string|max:255',  // ref_chassis_reg_no → refchassisregno
        ]);

        // Dummy ke liye basic validation pass, Actual ke liye extra
        if ($request->customertype != "Dummy") {
            if ($validator->fails()) {
                return redirect()->back()->withInput()->with('error', $validator->messages()->first());
            } else {
                $validator = Validator::make($request->all(), [
                    'bookingsource' => 'required|string|max:255',  // booking_source → bookingsource
                    'hiddenbookingdate' => 'required|date',
                    'bookingamount' => 'required|numeric',  // booking_amount → bookingamount
                    'bookingmode' => 'required|string|max:255',  // booking_mode → bookingmode
                    'coltype' => 'required',  // col_type → coltype
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->with('error', $validator->messages()->first());
                }
            }
        }

        // Receipt/Col Type 1 validation
        if ($request->coltype === 1) {
            $validator = Validator::make($request->all(), [
                'receiptno' => 'required|string|max:255',  // receipt_no → receiptno
                'hiddenreceiptdate' => 'required|date',  // receipt_date → hiddenreceiptdate
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->with('error', $validator->messages()->first());
            }
        }

        // ====== PENDING FIELDS LOGIC (same as save()) ======
        if (is_null($request->input('receiptno'))) {
            $pending++;
            $pendingFields[] = 'Receipt number needs to be updated';
        }
        if (is_null($request->input('hiddenreceiptdate'))) {
            $pending++;
            $pendingFields[] = 'Receipt date needs to be updated';
        }
        if ($request->input('bookingmode') === 'Online') {
            if (is_null($request->input('refrenceno')) || $request->input('refrenceno') === '') {
                $pending++;
                $pendingFields[] = 'Online booking reference number needs to be updated';
            }
        }
        if (is_null($request->input('panno'))) {
            $pending++;
            $pendingFields[] = 'PAN number needs to be updated';
        }
        if (is_null($request->input('adharno'))) {
            $pending++;
            $pendingFields[] = 'Aadhar number needs to be updated';
        }
        if (is_null($request->input('dmsno'))) {  // dms_no → dmsno (assuming blade mein)
            $pending++;
            $pendingFields[] = 'Sales force number needs to be updated';
        }
        if (is_null($request->input('dmsotf'))) {
            $pending++;
            $pendingFields[] = 'DMS OTF needs to be updated';
        }
        if (is_null($request->input('hiddenotfdate'))) {  // hidden_otf_date → hiddenotfdate
            $pending++;
            $pendingFields[] = 'DMS OTF Date needs to be updated';
        }
        if ($request->makeorder == 1) {  // make_order → makeorder
            $pending++;
            $pendingFields[] = 'DMS SO number needs to be updated';
        }

        // ====== BOOKING SAVE (same logic, blade field names) ======
        $customerTypeInput = $request->input('customertype');
        $adhar_no_normalized = preg_replace('/[^0-9]/', '', $request->input('adharno', ''));
        $customerType = ($customerTypeInput === 'Actual' || $customerTypeInput === 'Active') ? 'Active' : $customerTypeInput;

        $booking = new Booking();
        $booking->b_type = $customerType;
        $booking->b_cat = $request->input('customercat');  // customer_cat → customercat
        $booking->b_mode = $request->input('bookingmode');  // booking_mode → bookingmode
        $booking->cpd = $request->input('hiddencpd');  // hidden_cpd → hiddencpd (if exists)
        $booking->col_type = $request->input('coltype') ?? 1;  // col_type → coltype
        $booking->col_by = $request->input('user');
        $booking->b_source = $request->input('bookingsource');  // booking_source → bookingsource
        $booking->dsa_id = $request->input('dsadetails');  // dsa_details → dsadetails
        $booking->online_bk_ref_no = $request->input('refrenceno');  // online_bk_ref_no → refrenceno
        $booking->booking_date = $request->input('hiddenbookingdate');  // booking_date → hiddenbookingdate
        $booking->receipt_no = $request->input('receiptno');  // receipt_no → receiptno
        $booking->receipt_date = $request->input('hiddenreceiptdate');  // receipt_date → hiddenreceiptdate
        $booking->booking_amount = $request->input('bookingamount');  // booking_amount → bookingamount
        $booking->branch_id = $request->input('branch');
        $booking->location_id = $request->input('location');
        $booking->location_other = $request->input('locationother');  // location_other → locationother
        $booking->c_dob = $request->input('hiddencustomerdob');  // c_dob → hiddencustomerdob
        $booking->segment_id = $request->input('segmentid');  // segment_id → segmentid
        $booking->model = $request->input('model');
        $booking->variant = $request->input('variant');
        $booking->color = $request->input('color');
        $booking->vh_id = $request->input('vhid');  // vh_id → vhid
        $booking->order = $request->input('makeorder');  // order → makeorder (assuming)
        $booking->seating = $request->input('seating');
        $booking->person_id = auth()->id();
        $booking->name = $request->input('name');
        $booking->care_of_type = $request->input('careof');  // care_of_type → careof
        $booking->care_of = $request->input('careofname');  // care_of → careofname
        $booking->mobile = $request->input('mobile');
        $booking->alt_mobile = $request->input('altmobile');  // alt_mobile → altmobile
        $booking->gender = $request->input('gender');
        $booking->occ = $request->input('occupation');  // occ → occupation
        $booking->buyer_type = $request->input('buyertype');  // buyer_type → buyertype
        $booking->exist_oem1 = $request->input('enummaster1');  // enummaster1 → enummaster1 (no _)
        $booking->exist_oem2 = $request->input('enummaster2');
        $booking->vh1_detail = $request->input('vehicledetails');  // vh1_detail → vehicledetails
        $booking->vh2_detail = $request->input('vehicledetails2');
        $booking->registration_no = $request->input('registrationno');  // registration_no → registrationno
        $booking->make_year = $request->input('manufacturingyear');  // manufacturing_year → manufacturingyear
        $booking->odo_reading = $request->input('odometerreading');  // odo_reading → odometerreading
        $booking->expected_price = $request->input('expectedprice');  // expected_price → expectedprice
        $booking->offered_price = $request->input('offeredprice');  // offered_price → offeredprice
        $booking->exchange_bonus = $request->input('exchangebonus');  // exchange_bonus → exchangebonus
        $booking->pan_no = $request->input('panno');  // pan_no → panno
        $booking->adhar_no = $adhar_no_normalized;
        $booking->gstn = $request->input('gstn');
        $booking->dms_otf = $request->input('dmsotf');  // dms_otf → dmsotf
        $booking->dms_so = $request->input('dmss o');  // dms_so → dmss o (fix blade if needed)
        $booking->dms_no = $request->input('dmsno');  // dms_no → dmsno
        $booking->otf_date = $request->input('hiddenotfdate');  // otf_date → hiddenotfdate
        $booking->mapped = 0;
        $booking->chasis_no = $request->input('chassis');  // chasis_no → chassis
        $booking->del_type = $request->input('deliverytype');  // del_type → deliverytype
        $booking->del_date = $request->input('hiddenexpecteddeldate');  // del_date → hiddenexpecteddeldate
        $booking->fin_mode = $request->input('finmode');  // fin_mode → finmode
        $booking->financier = $request->input('financier');
        $booking->loan_status = $request->input('loanstatus');  // loan_status → loanstatus

        // Accessories handling
        if (!empty($request->accessories)) {
            $booking->accessories = implode(',', $request->input('accessories'));
        }

        $booking->apack_amount = $request->input('apackamount');  // apack_amount → apackamount
        $booking->consultant = $request->input('saleconsultant');
        $booking->refferd = $request->input('referredby');  // refferd → referredby
        $booking->r_name = $request->input('refcustomername');  // r_name → refcustomername
        $booking->r_mobile = $request->input('refmobileno');  // r_mobile → refmobileno
        $booking->r_model = $request->input('refexistingmodel');  // r_model → refexistingmodel
        $booking->r_variant = $request->input('refvariant');
        $booking->r_chassis = $request->input('refchassisregno');  // r_chassis → refchassisregno

        $booking->pending = $pending;
        $booking->pending_remark = implode(' , ', $pendingFields);

        if ($pending > 0) {
            $booking->status = 8;  // Pending
        }

        // Dummy customer override
        if ($customerType === 'Dummy') {
            $booking->b_mode = 'Dealer';
            $booking->b_source = 'Dealer';
        }

        $booking->save();

        // ====== FILE UPLOAD (amount_proof - blade mein 'amountproof') ======
        if (isset($request->amountproof)) {  // amount_proof → amountproof
            $file = $request->file('amountproof');
            $fn1 = 'tf_ap' . date('d-m-Y') . "_" . time() . '.' . $file->extension();
            $fn2 = 'tf_ap2' . date('d-m-Y') . "_" . time() . '.' . $file->extension();
            $filepath = public_path('uploads/temp/');
            $file->move($filepath, $fn1);
            File::copy($filepath . $fn1, $filepath . $fn2);
        }

        // ====== BOOKINGAMOUNT ENTRY (col_type 1/4) ======
        $number = $request->input('receiptno') ?? $request->input('voucherno');  // voucherno if exists
        if (in_array($booking->col_type, [1, 4]) && $booking->booking_amount > 0 && $number) {
            $payment = new Bookingamount();
            $payment->bid = $booking->id;
            $payment->date = $request->input('hiddenreceiptdate') ?? now();
            $payment->amount = $booking->booking_amount;
            $payment->reciept = $number;
            $payment->voucher = ($booking->col_type == 4) ? 1 : 0;
            $payment->save();

            if ($request->hasFile('amountproof') && isset($filepath, $fn1)) {  // amount_proof → amountproof
                $payment->addMedia($filepath . $fn1)->toMediaCollection('amount-proof');
            }
        }

        // ====== XEXCHANGE ENTRY (if buyer_type == Exchange Buy) ======
        if ($request->has('buyertype') && $request->input('buyertype') === 'Exchange Buy') {
            $exchange = new XExchange();
            $exchange->bid = $booking->id;
            $exchange->vh_id = $booking->vh_id;
            $exchange->verification_status = 1; // Unverified
            $exchange->case_status = 1; // In-Process
            $exchange->purchase_type = $request->input('buyertype');  // buyer_type → buyertype
            $exchange->save();
        }

        // ====== XFINANCE ENTRY (if fin_mode == In-house) ======
        if ($request->has('finmode') && $request->input('finmode') === 'In-house') {
            $finance = new XFinance();
            $finance->bid = $booking->id;
            $finance->vh_id = $booking->vh_id;
            $finance->verification_status = 1; // Unverified
            $finance->case_status = 1; // In-Process
            $finance->save();
        }

        // ====== CHATHELPER (same as save()) ======
        ChatHelper::add_communication(3, "Booking Created", "Booking created successfully", $booking->id);
        $commid = ChatHelper::get_commid(3, $booking->id, "Booking Created");
        if (isset($request->amountproof)) {  // amount_proof → amountproof
            ChatHelper::add_followup($commid, $request->input('details'), "Booking Created", $filepath . $fn2, 1);
        } else {
            ChatHelper::add_followup($commid, $request->input('details'), "Booking Created", null, 1);
        }

        // Backpack redirect (index route pe jayega)
        return redirect()->backpack_url('booking')->with('success', 'Booking added successfully!');
    }

    protected function setupUpdateOperation()
    {
        // Same validation as create (reuse BookingRequest if needed)
        CRUD::setValidation(BookingRequest::class);

        // Custom edit view
        $this->crud->setEditView('booking.edit');

        // === ID se booking fetch karo ===
        $id = $this->crud->getCurrentEntryId() ?? $this->crud->getRequest()->id;
        $entry = $this->crud->getEntry($id);

        // === Pura data taiyar karo — bilkul old getFullBookingData() jaisa ===
        $data = [];

        // Basic helpers (same as create)
        $data['branches'] = collect(CommonHelper::getBranches())->map(fn($b) => (object)$b);
        $data['allusers'] = collect(XpricingHelper::selectUsers())->map(fn($u) => (object)$u);
        $data['saleconsultants'] = collect(XpricingHelper::selectfsc())->map(fn($s) => (object)$s);
        $data['financiers'] = collect(\App\Models\XlFinancier::select('id', 'name', 'short_name')->get()->toArray())
            ->map(fn($f) => (object)$f);

        $data['segments'] = collect(XpricingHelper::getSegments() ?? [])->map(function ($s) {
            $segId = $s['id'] ?? null;
            $segName = $s['name'] ?? CommonHelper::enumValueById($segId); // Extra safety

            // Final fallback if still empty
            if (empty($segName)) {
                $segName = 'Segment ID ' . $segId;
            }

            return [
                'id'    => $segId,
                'value' => $segName
            ];
        })->filter()->values();
        // dd($data['segments']);


        $data['models'] = [];
        if ($entry->segment_id) {
            $data['models'] = XpricingHelper::getModelsX($entry->segment_id) ?? [];
        }

        // Pre-load variants for current model
        $data['variants'] = [];
        if ($entry->model) {
            $data['variants'] = XpricingHelper::getVehiclesX($entry->model) ?? [];
        }

        // Pre-load colors for current variant (with data-code and data-vid)
        $data['colors'] = [];
        if ($entry->variant) {
            $data['colors'] = XpricingHelper::getColorX($entry->variant) ?? [];
        }

        // DSA Details
        $data['dsa_details'] = \App\Models\Xl_DSA_Master::all()->map(function ($dsa) {
            return (object)[
                'id'       => $dsa->id,
                'name'     => $dsa->name,
                'mobile'   => $dsa->mobile,
                'email'    => $dsa->email,
                'location' => $dsa->dlocation,
            ];
        });

        // Enum Master for OEM makes
        $data['enum_master'] = \App\Models\EnumMaster::where('master_id', 94)
            ->where('status', 1)
            ->orderBy('value')
            ->get()
            ->map(fn($em) => (object)['id' => $em->id, 'value' => $em->value]);

        // === Edit-specific data (jo dropdowns ko pre-populate karne ke liye chahiye) ===
        $data['locations'] = [];
        if ($entry->branch_id) {
            $locations = XCommonHelper::getLocations($entry->branch_id) ?? [];
            usort($locations, fn($a, $b) => strcmp(($a['name'] ?? '') . ' - ' . ($a['code'] ?? ''), ($b['name'] ?? '') . ' - ' . ($b['code'] ?? '')));
            $data['locations'] = $locations;
        }

        // Accessories dropdown (current segment/model/variant ke basis pe)
        $segmentName = CommonHelper::enumValueById($entry->segment_id ?? 0) ?? '';
        $data['accessories_dropdown'] = XpricingHelper::getAccessories(
            $segmentName,
            $entry->model ?? '',
            $entry->variant ?? ''
        ) ?? [];

        // Chassis dropdown ke liye (agar current chassis hai to uska model_code use karo)
        $data['chassis_list'] = [];
        if ($entry->chasis_no) {
            $stock = \App\Models\Stock::find($entry->chasis_no);
            if ($stock && $stock->model_code) {
                $data['chassis_list'] = \App\Models\Stock::where('model_code', $stock->model_code)
                    ->select('chasis_no', 'id')
                    ->get()
                    ->toArray();
            }
        }

        // Collector name display ke liye (old logic)
        $data['collector_name'] = '—';
        if ($entry->col_type == 2) {
            $user = $data['allusers']->firstWhere('id', $entry->col_by);
            $data['collector_name'] = $user ? $user->name . ' - (' . $user->emp_code . ')' : '—';
        } elseif ($entry->col_type == 3) {
            $dsa = $data['dsa_details']->firstWhere('id', $entry->col_by);
            $data['collector_name'] = $dsa ? $dsa->name . ' - ' . $dsa->mobile : '—';
        }

        // Final assign
        $this->data['entry'] = $entry;
        $this->data['data'] = $data;
        //dd($this->data);
    }


    public function update(Request $request, $id)
    {
        // dd($request->all()); // ← जैसा तुमने कहा था, यही रहेगा

        $booking = Booking::findOrFail($id);

        // Old values for comparison (remarks में सही old name दिखाने के लिए)
        $old_col_type = $booking->col_type;
        $old_col_by   = $booking->col_by;

        // Required data for lookups (consultants, users, DSA)
        $data['saleconsultants'] = XpricingHelper::selectfsc();
        $data['allusers']       = XpricingHelper::selectUsers();

        $dsaRecords = Xl_DSA_Master::all();
        $data['dsa_details'] = $dsaRecords->map(function ($dsa) {
            return [
                'id'       => $dsa->id,
                'name'     => $dsa->name,
                'mobile'   => $dsa->mobile,
                'email'    => $dsa->email,
                'location' => $dsa->dlocation,
            ];
        })->toArray();

        // Base validation rules (common for all bookings)
        $rules = [
            'branch'               => 'required|integer',
            'location_id'          => 'required|integer',
            'segment_id'           => 'required|integer',
            'model'                => 'required|string|max:255',
            'variant'              => 'required|string|max:255',
            'color'                => 'required|string|max:255',
            'name'                 => 'required|string|max:255',
            'care_of'              => 'nullable|string|max:255',
            'care_of_name'         => 'nullable|string|max:255',
            'mobile'               => 'required|string|max:15',
            'alt_mobile'           => 'nullable|string|max:15',
            'pan_no'               => 'nullable|string|max:10',
            'adhar_no'             => 'nullable|string|max:20',
            'dms_otf'              => 'nullable|string|max:255',
            'dms_so'               => 'nullable|string|max:255',
            'chassis'              => 'nullable|string|max:255',
            'delivery_type'        => 'required|string|in:Expected,Confirmed',
            'expected_del_date_actual' => 'nullable|date',
            'fin_mode'             => 'required|string|max:255',
            'financier'            => 'nullable|integer',
            'loan_status'          => 'nullable|string|max:255',
            'accessories'          => 'nullable|array',
            'accessories.*'        => 'integer',
            'saleconsultant'       => 'required|integer',
            'apack_amount'         => 'required|numeric',
            'seating'              => 'nullable|integer',
            'details'              => 'nullable|string',
            'referred_by'          => 'nullable|string|max:255',
            'ref_customer_name'    => 'nullable|string|max:255',
            'ref_mobile_no'        => 'nullable|string|max:15',
            'ref_existing_model'   => 'nullable|string|max:255',
            'ref_variant'          => 'nullable|string|max:255',
            'ref_chassis_reg_no'   => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Pending fields count & remarks
        $pending       = 0;
        $pendingFields = [];

        if ($request->input('booking_mode') === 'Online' && empty($request->input('refrence_no'))) {
            $pending++;
            $pendingFields[] = 'Online booking reference number needs to be updated';
        }

        if (empty($request->input('pan_no'))) {
            $pending++;
            $pendingFields[] = 'PAN number needs to be updated';
        }

        if (empty($request->input('adhar_no'))) {
            $pending++;
            $pendingFields[] = 'Aadhar number needs to be updated';
        }

        // Normalize Aadhar (only digits)
        $adhar_no_normalized = preg_replace('/[^0-9]/', '', $request->input('adhar_no', ''));

        // Remarks array
        $rem = [];

        // Helper for financier name
        $getFinancierName = function ($fid) {
            if (empty($fid)) return 'Null';
            $f = XlFinancier::find($fid);
            return $f ? $f->name : 'Unknown';
        };

        // All field comparisons & remarks
        if ($booking->b_type != $request->customer_type) {
            $rem[] = "Customer Type Changed from " . ($booking->b_type ?? 'null') . " to " . $request->customer_type;
            $booking->b_type = $request->customer_type;
        }

        if ($booking->b_mode != $request->booking_mode) {
            $rem[] = "Booking Mode Changed from " . ($booking->b_mode ?? 'null') . " to " . $request->booking_mode;
            $booking->b_mode = $request->booking_mode;
        }

        if ($old_col_type != $request->col_type) {
            $colTypeMap = [
                '1' => 'Receipt',
                '2' => 'Field Collection By Sales Team',
                '3' => 'Field Collection By DSA',
                '4' => 'Used Car Purchase'
            ];
            $oldName = $colTypeMap[$old_col_type] ?? 'null';
            $newName = $colTypeMap[$request->col_type] ?? 'null';
            $rem[] = "Collection Type Changed from {$oldName} to {$newName}";
        }

        if ($old_col_by != $request->user) {
            $oldUser = 'null';
            $newUser = 'null';

            if ($old_col_type == 2) {
                $u = collect($data['allusers'])->firstWhere('id', $old_col_by);
                $oldUser = $u ? $u['name'] . ' - (' . $u['emp_code'] . ')' : 'null';
            } elseif ($old_col_type == 3) {
                $d = collect($data['dsa_details'])->firstWhere('id', $old_col_by);
                $oldUser = $d ? $d['name'] . ' - ' . $d['mobile'] : 'null';
            }

            if ($request->col_type == 2) {
                $u = collect($data['allusers'])->firstWhere('id', $request->user);
                $newUser = $u ? $u['name'] . ' - (' . $u['emp_code'] . ')' : 'null';
            } elseif ($request->col_type == 3) {
                $d = collect($data['dsa_details'])->firstWhere('id', $request->user);
                $newUser = $d ? $d['name'] . ' - ' . $d['mobile'] : 'null';
            }

            $rem[] = "Collected By Changed from {$oldUser} to {$newUser}";
        }

        if ($booking->consultant != $request->saleconsultant) {
            $oldC = collect($data['saleconsultants'])->firstWhere('id', $booking->consultant);
            $newC = collect($data['saleconsultants'])->firstWhere('id', $request->saleconsultant);
            $rem[] = "Sale Consultant Changed from " . ($oldC['name'] ?? 'null') . " to " . ($newC['name'] ?? 'null');
            $booking->consultant = $request->saleconsultant;
        }

        if ($booking->cpd != $request->cpd_actual) {
            $rem[] = "CPD Date Changed from " . ($booking->cpd ? Carbon::parse($booking->cpd)->format('d-M-Y') : 'null') . " to " . $request->cpd_actual;
            $booking->cpd = $request->cpd_actual;
        }

        if ($booking->refrence_no != $request->refrence_no) {
            $rem[] = "Reference No Changed from " . ($booking->refrence_no ?? '0') . " to " . $request->refrence_no;
            $booking->refrence_no = $request->refrence_no;
        }

        if ($booking->b_source != $request->booking_source) {
            $rem[] = "Booking Source Changed from " . ($booking->b_source ?? 'null') . " to " . $request->booking_source;
            $booking->b_source = $request->booking_source;
        }

        if ($booking->booking_date != $request->booking_date_actual) {
            $rem[] = "Booking Date Changed from " . ($booking->booking_date ? Carbon::parse($booking->booking_date)->format('d-M-Y') : 'null') . " to " . Carbon::parse($request->booking_date_actual)->format('d-M-Y');
            $booking->booking_date = $request->booking_date_actual;
        }

        if ($booking->booking_amount != $request->booking_amount) {
            $rem[] = "Booking Amount Changed from " . ($booking->booking_amount ?? '0') . " to " . $request->booking_amount;
            $booking->booking_amount = $request->booking_amount;
        }

        if ($booking->branch_id != $request->branch) {
            $oldBranch = X_Branch::find($booking->branch_id)?->name ?? 'null';
            $newBranch = X_Branch::find($request->branch)?->name ?? 'null';
            $rem[] = "Branch Changed from {$oldBranch} to {$newBranch}";
            $booking->branch_id = $request->branch;
        }

        if ($booking->location_id != $request->location_id) {
            $rem[] = "Location Changed from " . $booking->location_id . " to " . $request->location_id;
            $booking->location_id = $request->location_id;
        }

        if ($booking->location_other != $request->location_other) {
            $rem[] = "Location Other Changed from " . ($booking->location_other ?? '0') . " to " . $request->location_other;
            $booking->location_other = $request->location_other;
        }

        if ($booking->segment_id != $request->segment_id) {
            $rem[] = "Segment Changed from " . CommonHelper::enumValueById($booking->segment_id) . " to " . CommonHelper::enumValueById($request->segment_id);
            $booking->segment_id = $request->segment_id;
        }

        if ($booking->model != $request->model) {
            $rem[] = "Model Changed from " . $booking->model . " to " . $request->model;
            $booking->model = $request->model;
        }

        if ($booking->variant != $request->variant) {
            $rem[] = "Variant Changed from " . $booking->variant . " to " . $request->variant;
            $booking->variant = $request->variant;
        }

        if ($booking->color != $request->color) {
            $rem[] = "Color Changed from " . $booking->color . " to " . $request->color;
            $booking->color = $request->color;
        }

        if ($booking->seating != $request->seating) {
            $rem[] = "Seating Changed from " . ($booking->seating ?? '0') . " to " . $request->seating;
            $booking->seating = $request->seating;
        }

        if ($booking->name != $request->name) {
            $rem[] = "Name Changed from " . $booking->name . " to " . $request->name;
            $booking->name = $request->name;
        }

        if ($booking->care_of_type != $request->care_of) {
            $rem[] = "Care Of Type Changed";
            $booking->care_of_type = $request->care_of;
        }

        if ($booking->care_of != $request->care_of_name) {
            $oldCare = $booking->care_of ?? 'None';
            $newCare = $request->care_of_name ?? 'None';
            $rem[] = "Care Of Changed from {$oldCare} to {$newCare}";
            $booking->care_of = $request->care_of_name;
        }

        if ($booking->mobile != $request->mobile) {
            $rem[] = "Mobile Changed from " . $booking->mobile . " to " . $request->mobile;
            $booking->mobile = $request->mobile;
        }

        if ($booking->alt_mobile != $request->alt_mobile) {
            $rem[] = "Alt Mobile Changed from " . ($booking->alt_mobile ?? '0') . " to " . $request->alt_mobile;
            $booking->alt_mobile = $request->alt_mobile;
        }

        if ($booking->gender != $request->gender) {
            $rem[] = "Gender Changed from " . ($booking->gender ?? '0') . " to " . $request->gender;
            $booking->gender = $request->gender;
        }

        if ($booking->occ != $request->occupation) {
            $rem[] = "Occupation Changed from " . ($booking->occ ?? '0') . " to " . $request->occupation;
            $booking->occ = $request->occupation;
        }

        if ($booking->buyer_type != $request->buyer_type) {
            $rem[] = "Buyer Type Changed from " . ($booking->buyer_type ?? '0') . " to " . $request->buyer_type;
            $booking->buyer_type = $request->buyer_type;
        }

        // Purchase fields (exchange/scrappage etc.) – तुम्हारे नामों के हिसाब से
        if ($booking->exist_oem1 != $request->enum_master1) {
            $rem[] = "Brand (Make 1) Changed";
            $booking->exist_oem1 = $request->enum_master1;
        }

        if ($booking->vh1_detail != $request->vehicle_details) {
            $rem[] = "Model & Variant 1 Changed";
            $booking->vh1_detail = $request->vehicle_details;
        }

        if ($booking->exist_oem2 != $request->enum_master2) {
            $rem[] = "Brand (Make 2) Changed";
            $booking->exist_oem2 = $request->enum_master2;
        }

        if ($booking->vh2_detail != $request->vehicle_details2) {
            $rem[] = "Model & Variant 2 Changed";
            $booking->vh2_detail = $request->vehicle_details2;
        }

        if ($booking->registration_no != $request->registration_no) {
            $rem[] = "Vehicle Registration No Changed";
            $booking->registration_no = $request->registration_no;
        }

        if ($booking->make_year != $request->manufacturing_year) {
            $rem[] = "Manufacturing Year Changed";
            $booking->make_year = $request->manufacturing_year;
        }

        if ($booking->odo_reading != $request->odometer_reading) {
            $rem[] = "Odometer Reading Changed";
            $booking->odo_reading = $request->odometer_reading;
        }

        if ($booking->expected_price != $request->expected_price) {
            $rem[] = "Expected Price Changed";
            $booking->expected_price = $request->expected_price;
        }

        if ($booking->offered_price != $request->offered_price) {
            $rem[] = "Offered Price Changed";
            $booking->offered_price = $request->offered_price;
        }

        if ($booking->exchange_bonus != $request->exchange_bonus) {
            $rem[] = "Exchange Bonus Changed";
            $booking->exchange_bonus = $request->exchange_bonus;
        }

        if ($booking->pan_no != $request->pan_no) {
            $rem[] = "Pan No Changed from " . ($booking->pan_no ?? '0') . " to " . $request->pan_no;
            $booking->pan_no = $request->pan_no;
        }

        if ($booking->adhar_no != $adhar_no_normalized) {
            $rem[] = "Adhar No Changed from " . ($booking->adhar_no ?? '0') . " to " . $adhar_no_normalized;
            $booking->adhar_no = $adhar_no_normalized;
        }

        if ($booking->c_dob != $request->hidden_customer_dob) {
            $oldDob = $booking->c_dob ? Carbon::parse($booking->c_dob)->format('d-M-Y') : 'null';
            $newDob = $request->hidden_customer_dob ? Carbon::parse($request->hidden_customer_dob)->format('d-M-Y') : 'null';
            $rem[] = "Customer D.O.B. Changed from {$oldDob} to {$newDob}";
            $booking->c_dob = $request->hidden_customer_dob;
        }

        if ($booking->del_type != $request->delivery_type) {
            $rem[] = "Delivery Type Changed from " . ($booking->del_type ?? 'null') . " to " . $request->delivery_type;
            $booking->del_type = $request->delivery_type;
        }

        if ($booking->del_date != $request->expected_del_date_actual) {
            $oldDate = $booking->del_date ? Carbon::parse($booking->del_date)->format('d-M-Y') : 'null';
            $newDate = $request->expected_del_date_actual ? Carbon::parse($request->expected_del_date_actual)->format('d-M-Y') : 'null';
            $rem[] = "Expected Delivery Date Changed from {$oldDate} to {$newDate}";
            $booking->del_date = $request->expected_del_date_actual;
        }

        if ($booking->fin_mode != $request->fin_mode) {
            $rem[] = "Fin Mode Changed from " . ($booking->fin_mode ?? 'null') . " to " . $request->fin_mode;
            $booking->fin_mode = $request->fin_mode;
        }

        if ($booking->financier != $request->financier) {
            $oldF = $getFinancierName($booking->financier);
            $newF = $getFinancierName($request->financier);
            $rem[] = "Financier Changed from {$oldF} to {$newF}";
            $booking->financier = $request->financier;
        }

        if ($booking->loan_status != $request->loan_status) {
            $rem[] = "Loan Status Changed from " . ($booking->loan_status ?? 'null') . " to " . ($request->loan_status ?? 'null');
            $booking->loan_status = $request->loan_status;
        }

        $accessoriesString = $request->has('accessories') && $request->accessories ? implode(',', $request->accessories) : null;
        if ($booking->accessories != $accessoriesString) {
            $rem[] = "Accessories Changed";
            $booking->accessories = $accessoriesString;
        }

        if ($booking->apack_amount != $request->apack_amount) {
            $rem[] = "Apack Amount Changed from " . ($booking->apack_amount ?? '0') . " to " . $request->apack_amount;
            $booking->apack_amount = $request->apack_amount;
        }

        if ($booking->chasis_no != $request->chassis) {
            $rem[] = "Chasis No Changed from " . ($booking->chasis_no ?? 'null') . " to " . ($request->chassis ?? 'null');
            $booking->chasis_no = $request->chassis;
        }

        if ($booking->dms_otf != $request->dms_otf) {
            $rem[] = "DMS OTF Changed";
            $booking->dms_otf = $request->dms_otf;
        }



        // Referred by fields
        if ($booking->r_name != $request->r_name) {
            $rem[] = "Referred Name Changed";
            $booking->r_name = $request->r_name;
        }

        if ($booking->r_mobile != $request->r_mobile) {
            $rem[] = "Referred Mobile Changed";
            $booking->r_mobile = $request->r_mobile;
        }

        if ($booking->r_model != $request->r_model) {
            $rem[] = "Referred Model Changed";
            $booking->r_model = $request->r_model;
        }

        if ($booking->r_variant != $request->r_variant) {
            $rem[] = "Referred Variant Changed";
            $booking->r_variant = $request->r_variant;
        }

        if ($booking->r_chassis != $request->r_chassis) {
            $rem[] = "Referred Chassis Changed";
            $booking->r_chassis = $request->r_chassis;
        }

        if ($booking->order != $request->input('make_order')) {
            if ($booking->order == 0 && $request->input('make_order') == 1) {
                $rem[] = "Requested to order";
            } elseif ($booking->order == 1 && $request->input('make_order') == 0) {
                $rem[] = "Cancelled request for order";
            }
            $booking->order = $request->input('make_order');
        }

        // Final delayed updates
        $booking->col_type = $request->col_type;
        $booking->col_by   = $request->user;
        $booking->vh_id    = $request->input('vh_id');

        // Pending status
        $booking->pending        = $pending;
        $booking->pending_remark = !empty($pendingFields) ? implode(' , ', $pendingFields) : null;
        if ($pending > 0) {
            $booking->status = 8;
        }

        $booking->save();

        // Create XExchange if Exchange Buy and not exists
        if ($request->filled('buyer_type') && $request->buyer_type === 'Exchange Buy') {
            if (!XExchange::where('bid', $booking->id)->exists()) {
                XExchange::create([
                    'bid'                 => $booking->id,
                    'vh_id'               => $booking->vh_id,
                    'verification_status' => 1,
                    'case_status'         => 1,
                    'purchase_type'       => $request->buyer_type,
                ]);
                $rem[] = "New exchange entry created with Verification Status: Unverified and Case Status: In-Process for Exchange Buy";
            }
        }

        // Create XFinance if In-house and not exists
        if ($request->fin_mode === 'In-house') {
            if (!XFinance::where('bid', $booking->id)->exists()) {
                XFinance::create([
                    'bid'                 => $booking->id,
                    'vh_id'               => $booking->vh_id,
                    'fin_mode'            => 'In-house',
                    'verification_status' => 1,
                    'case_status'         => 1,
                ]);
                $rem[] = "New finance entry created with Verification Status: Unverified and Case Status: In-Process for In-house financing";
            }
        }

        // Log remarks in chat
        if (!empty($rem)) {
            $commid = ChatHelper::get_commid(3, $booking->id, "Booking Created");
            ChatHelper::add_followup($commid, "Booking Updated" . $request->details, implode(" , ", $rem), null, 1);
        }

        return redirect(config('backpack.base.route_prefix', 'admin') . '/booking')
            ->with('success', 'Booking updated successfully!');
    }


    protected function setupShowOperation()
    {
        $this->crud->setShowView('booking.show');
    }
    public function getModels($segment_id)
    {
        $models = XpricingHelper::getModelsX($segment_id);
        return response()->json($models);
    }

    public function CheckReceipt($rn)
    {
        $count = XpricingHelper::checkReceiptX($rn);
        return response()->json((int)$count > 0 ? 1 : 0);
    }

    public function getVariants($model)
    {
        $variants = XpricingHelper::getVehiclesX($model);
        return response()->json($variants);
    }

    public function getColors($variant)
    {
        $colors = XpricingHelper::getColorX($variant);
        return response()->json($colors);
    }

    public function getChassisNumbers($modelCode)
    {
        $chassisNumbers = Stock::where('model_code', $modelCode)->select('chasis_no', 'id')->get()->toArray();
        //print_r($chassisNumbers);
        return response()->json($chassisNumbers);
    }

    public function getBranchLocation($bids)
    {
        $data = CommonHelper::getLocations($bids);
        //print_r($data);
        return $data;
    }
    public function getAccessories($segment, $model, $variant)
    {
        $accessories = XpricingHelper::getAccessories($segment, $model, $variant);
        return response()->json($accessories);
    }
    public function getLocations($state_id)
    {

        $locations = XCommonHelper::getLocationsByState($state_id);


        return response()->json($locations);
    }
    public function getLocationsByPincode($pincode)
    {
        $locations = PinCodes::where('pincode', $pincode)->get(['id', 'name']);

        if ($locations->isNotEmpty()) {
            return response()->json($locations);
        } else {
            return response()->json([]);
        }
    }
    public function getStateByLocation($location_id)
    {
        $location = PinCodes::where('id', $location_id)->first(['id', 'parent', 'level']);

        if (!$location) {
            return response()->json(null);
        }

        // Keep finding the parent until we reach the STATE level
        while ($location && $location->level !== 'STATE') {
            $location = PinCodes::where('id', $location->parent)->first(['id', 'parent', 'level']);
        }

        return response()->json([
            'state_id' => $location ? $location->id : null
        ]);
    }
}
