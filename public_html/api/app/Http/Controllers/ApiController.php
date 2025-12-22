<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    // Budget upload (lumpsum amount or file). POST /api/tenders/{id}/budget
    public function uploadBudget(Request $request, $id)
    {
        // find tender (model or DB fallback)
        $t = $this->findRecord('Tender', 'tenders', $id);
        if (!$t) return response()->json(['error' => 'Tender not found'], 404);

        $rules = [
            'type' => 'nullable|in:lumpsum,file',
            'amount' => 'nullable|numeric',
            'budgetFile' => 'nullable|file|max:10240'
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        $type = $request->input('type') ?: ($request->hasFile('budgetFile') ? 'file' : 'lumpsum');
        $filePath = null;
        if ($request->hasFile('budgetFile')) {
            $filePath = $request->file('budgetFile')->store('budgets', 'public');
        }
        $update = [];
        if ($filePath) $update['budget_file'] = $filePath;
        if ($request->filled('amount')) $update['budget_amount'] = $request->input('amount');
        $update['budget_type'] = $type;

        $updated = $this->updateRecord('Tender', 'tenders', $id, $update);
        return response()->json($updated);
    }

    // Signed work order upload. POST /api/tenders/{id}/upload-signed
    public function uploadSignedWorkOrder(Request $request, $id)
    {
        $t = $this->findRecord('Tender', 'tenders', $id);
        if (!$t) return response()->json(['error' => 'Tender not found'], 404);

        if (!$request->hasFile('signedFile')) {
            return response()->json(['error' => 'signedFile required'], 422);
        }

        $path = $request->file('signedFile')->store('signed_work_orders', 'public');
        $updated = $this->updateRecord('Tender', 'tenders', $id, ['signed_work_order' => $path]);
        return response()->json(['message' => 'uploaded', 'file' => $path, 'tender' => $updated]);
    }

    // mobile application minimal list. GET /api/mobile/tenders
    public function mobileTenders()
    {
        $tenders = Tender::select('id','title','status','due_date')->get();
        return response()->json(['total' => $tenders->count(), 'data' => $tenders]);
    }

    // OTP via WhatsApp placeholder. POST /api/auth/request-otp and /api/auth/verify-otp
    public function requestOtp(Request $request)
    {
        $phone = $request->input('phone');
        if (!$phone) return response()->json(['error' => 'phone required'], 422);
        $code = random_int(100000, 999999);
        Cache::put("otp:{$phone}", $code, now()->addMinutes(5));
        // Placeholder: implement actual WhatsApp provider integration here.
        Log::info("[WHATSAPP-OTP] to={$phone} code={$code}");
        return response()->json(['message' => 'otp_sent']);
    }

    public function verifyOtp(Request $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $cached = Cache::get("otp:{$phone}");
        if (!$cached || (string)$cached !== (string)$code) {
            return response()->json(['error' => 'invalid_or_expired'], 400);
        }
        Cache::forget("otp:{$phone}");
        return response()->json(['message' => 'verified', 'phone' => $phone]);
    }

    // Vendor partial registration (short form before paywall). POST /api/vendors/partial
    public function vendorPartial(Request $request)
    {
        $data = $request->only(['name','email','mobile']);
        $data['partial'] = true;
        $vendor = $this->createRecord('Vendor', 'vendors', $data);

        // record history table (always use DB)
        DB::table('vendor_histories')->insert([
            'vendor_id' => $vendor->id ?? $vendor->id ?? null,
            'event' => 'partial_registered',
            'data' => json_encode($data),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json($vendor, 201);
    }

    // Vendor complete registration (after paywall). POST /api/vendors/complete (Admin)
    public function vendorComplete(Request $request)
    {
        $id = $request->input('id');
        $vendor = $this->findRecord('Vendor', 'vendors', $id);
        if (!$vendor) return response()->json(['error' => 'Vendor not found'], 404);

        if (!auth()->check() || !method_exists(auth()->user(), 'hasRole') || !auth()->user()->hasRole('Admin')) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $updateData = $request->only(['address','documents','extra']);
        $updateData['partial'] = false;
        $updatedVendor = $this->updateRecord('Vendor', 'vendors', $id, $updateData);

        DB::table('vendor_histories')->insert([
            'vendor_id' => $id,
            'event' => 'completed_registration',
            'data' => json_encode($request->all()),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json($updatedVendor);
    }

    // Buyer registration requiring admin approval. POST /api/buyers/register
    public function buyerRegister(Request $request)
    {
        $b = DB::table('buyers')->insertGetId([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'data' => json_encode($request->all()),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['id' => $b, 'status' => 'pending'], 201);
    }

    // Admin approves buyer. POST /api/buyers/{id}/approve
    public function buyerApprove($id)
    {
        if (!auth()->check() || !auth()->user()->hasRole('Admin')) {
            return response()->json(['error' => 'forbidden'], 403);
        }
        DB::table('buyers')->where('id',$id)->update(['status' => 'approved', 'updated_at' => now()]);
        return response()->json(['id' => $id, 'status' => 'approved']);
    }

    // Yearly subscription (simple). POST /api/subscribe
    public function subscribe(Request $request)
    {
        $userId = $request->input('user_id');
        $start = now();
        $end = now()->addYear();
        $id = DB::table('subscriptions')->insertGetId([
            'user_id' => $userId,
            'type' => 'yearly',
            'start' => $start,
            'end' => $end,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(DB::table('subscriptions')->find($id), 201);
    }

    // Create invoice (admin). POST /api/invoices
    public function createInvoice(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole('Admin')) {
            return response()->json(['error' => 'forbidden'], 403);
        }
        $id = DB::table('invoices')->insertGetId([
            'user_id' => $request->input('user_id'),
            'amount' => $request->input('amount',0),
            'registration_date' => $request->input('registration_date', now()),
            'due_date' => $request->input('due_date', now()->addDays(14)),
            'status' => $request->input('status','pending'),
            'meta' => json_encode($request->input('meta',[])),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(DB::table('invoices')->find($id), 201);
    }

    // Calendar events: tender due and invoice due with conditional flags. GET /api/calendar
    public function calendar()
    {
        // fetch tenders: prefer model if exists
        $cls = $this->modelClass('Tender');
        if ($cls && class_exists($cls)) {
            $tenders = $cls::whereNotNull('due_date')->get(['id','title','due_date','status']);
        } else {
            $tenders = DB::table('tenders')->whereNotNull('due_date')->get(['id','title','due_date','status']);
        }
        $invoices = DB::table('invoices')->get(['id','due_date','status']);
        $events = [];

        foreach ($tenders as $t) {
            $due = is_object($t) ? $t->due_date : $t['due_date'] ?? null;
            $events[] = [
                'id' => $t->id,
                'title' => $t->title,
                'date' => $due,
                'type' => 'tender',
                'status' => $t->status,
                'dueSoon' => (strtotime($due) - time()) <= 7*24*3600
            ];
        }
        foreach ($invoices as $i) {
            $events[] = [
                'id' => $i->id,
                'title' => 'Invoice '.$i->id,
                'date' => $i->due_date,
                'type' => 'invoice',
                'status' => $i->status,
                'dueSoon' => (strtotime($i->due_date) - time()) <= 7*24*3600
            ];
        }

        return response()->json($events);
    }

    // Dashboard counts & reports. GET /api/admin/dashboard
    public function dashboard()
    {
        if (!auth()->check() || !method_exists(auth()->user(), 'hasRole') || !auth()->user()->hasRole('Admin')) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        // users count
        $clsUser = $this->modelClass('User');
        $users = ($clsUser && class_exists($clsUser)) ? $clsUser::count() : DB::table('users')->count();

        // tenders count
        $clsTender = $this->modelClass('Tender');
        $tendersCount = ($clsTender && class_exists($clsTender)) ? $clsTender::count() : DB::table('tenders')->count();

        // status counts
        $statusCounts = DB::table('tenders')->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total','status');

        return response()->json([
            'users' => $users,
            'tenders' => $tendersCount,
            'tender_status' => $statusCounts
        ]);
    }

    /**
     * Helper: check if an Eloquent model class exists.
     */
    protected function modelClass(string $short): ?string
    {
        $map = [
            'Tender' => '\\App\\Models\\Tender',
            'Vendor' => '\\App\\Models\\Vendor',
            'User'   => '\\App\\Models\\User',
        ];
        return $map[$short] ?? null;
    }

    /**
     * Helper: find record by id using Eloquent model if available, otherwise DB table.
     * Returns object (Eloquent model) or stdClass from DB or null.
     */
    protected function findRecord(string $modelShort, string $table, $id)
    {
        $cls = $this->modelClass($modelShort);
        if ($cls && class_exists($cls)) {
            return $cls::find($id);
        }
        $rec = DB::table($table)->where('id', $id)->first();
        return $rec;
    }

    /**
     * Helper: update record using Eloquent model if available, otherwise DB table.
     * $data is associative array of columns/values.
     */
    protected function updateRecord(string $modelShort, string $table, $id, array $data)
    {
        $cls = $this->modelClass($modelShort);
        if ($cls && class_exists($cls)) {
            $model = $cls::find($id);
            if (!$model) return null;
            $model->fill($data);
            $model->save();
            return $model;
        }
        DB::table($table)->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));
        return DB::table($table)->where('id', $id)->first();
    }

    /**
     * Helper: create record using Eloquent model if available, otherwise DB table.
     */
    protected function createRecord(string $modelShort, string $table, array $data)
    {
        $cls = $this->modelClass($modelShort);
        if ($cls && class_exists($cls)) {
            return $cls::create($data);
        }
        $id = DB::table($table)->insertGetId(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
        return DB::table($table)->where('id', $id)->first();
    }
}
