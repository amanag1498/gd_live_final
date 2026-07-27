<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Host;
use App\Models\HostRequest;
use App\Services\NotifyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;

class HostRequestController extends Controller
{
  public function index(Request $request){
    $validated = $request->validate([
      'q' => 'nullable|string|max:120',
      'status' => 'nullable|in:pending,approved,rejected',
      'agency_id' => 'nullable|integer|exists:agencies,id',
      'date_from' => 'nullable|date_format:Y-m-d',
      'date_to' => [
        'nullable',
        'date_format:Y-m-d',
        Rule::when($request->filled('date_from'), 'after_or_equal:date_from'),
      ],
      'per_page' => 'nullable|integer|in:20,50,100',
    ]);

    $filters = [
      'q' => trim((string) ($validated['q'] ?? '')),
      'status' => (string) ($validated['status'] ?? ''),
      'agency_id' => isset($validated['agency_id']) ? (int) $validated['agency_id'] : null,
      'date_from' => (string) ($validated['date_from'] ?? ''),
      'date_to' => (string) ($validated['date_to'] ?? ''),
      'per_page' => (int) ($validated['per_page'] ?? 20),
    ];

    $requests = HostRequest::query()
      ->with(['user', 'agency'])
      ->when($filters['q'] !== '', function ($query) use ($filters) {
        $search = $filters['q'];
        $like = "%{$search}%";

        $query->where(function ($query) use ($search, $like) {
          $query
            ->where('stage_name', 'like', $like)
            ->orWhere('contact_phone', 'like', $like)
            ->orWhere('country', 'like', $like)
            ->orWhere('city', 'like', $like)
            ->orWhereHas('user', fn ($user) => $user
              ->where('name', 'like', $like)
              ->orWhere('email', 'like', $like))
            ->orWhereHas('agency', fn ($agency) => $agency->where('name', 'like', $like));

          if (ctype_digit($search)) {
            $query
              ->orWhere('id', (int) $search)
              ->orWhere('user_id', (int) $search);
          }
        });
      })
      ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
      ->when($filters['agency_id'], fn ($query) => $query->where('agency_id', $filters['agency_id']))
      ->when($filters['date_from'] !== '', fn ($query) => $query->whereDate('created_at', '>=', $filters['date_from']))
      ->when($filters['date_to'] !== '', fn ($query) => $query->whereDate('created_at', '<=', $filters['date_to']))
      ->latest()
      ->paginate($filters['per_page'])
      ->withQueryString();

    $agencies = Agency::query()->orderBy('name')->get(['id', 'name']);

    return view('admin.host_requests.index', compact('requests', 'agencies', 'filters'));
  }
  public function show(HostRequest $host_request){
    $host_request->load(['user', 'agency']);
    return view('admin.host_requests.show', compact('host_request'));
  }
  public function update(Request $request, HostRequest $host_request){
    $request->validate(['action'=>'required|in:approve,reject','notes'=>'nullable|string|max:1000']);
    if ($host_request->status!=='pending') return back()->with('err','Already reviewed.');

    if ($request->action==='reject'){
      $host_request->update([
        'status'=>'rejected','review_notes'=>$request->notes,'reviewed_by'=>$request->user()->id,'reviewed_at'=>now()
      ]);
      try {
        Redis::publish('users:notify', json_encode([
          'user_id' => (int) $host_request->user_id,
          'type'    => 'host_rejected',
          'title'   => 'Host request reviewed',
          'body'    => 'Unfortunately your host request was rejected.',
          'meta'    => ['notes'=>$request->notes],
          'at'      => now()->toIso8601String(),
        ]));
      } catch (\Throwable $e) {}

      try {
                NotifyUser::send((int) $host_request->user_id, [
                    'type'   => 'host_rejected',
                    'title'  => 'Host request reviewed',
                    'body'   => 'Unfortunately your host request was rejected.',
                    'meta'   => ['notes' => $request->notes],
                    'screen' => 'notifications',
                ], [
                    'push'    => true,
                    'persist' => true,
                ]);
            } catch (\Throwable $e) {}
      return back()->with('ok','Rejected.');
    }

    DB::transaction(function() use ($host_request,$request){
      $u = $host_request->user;
      $u->assignRole('host');
      Host::query()->updateOrCreate(
        ['user_id'=>$u->id],
        [
          'agency_id' => $host_request->agency_id,
          'stage_name'=>$host_request->stage_name,
          'contact_phone'=>$host_request->contact_phone,
          'country'=>$host_request->country,
          'city'=>$host_request->city,
          'bio'=>$host_request->about,
        ]
      );
      $host_request->update([
        'status'=>'approved','review_notes'=>$request->notes,'reviewed_by'=>$request->user()->id,'reviewed_at'=>now()
      ]);
      // 🔔 LIVE push to user (no persistence yet)
    try {
      Redis::publish('users:notify', json_encode([
        'user_id' => (int) $host_request->user_id,
        'type'    => 'host_approved',
        'title'   => 'Host request approved 🎉',
        'body'    => 'You can now host live rooms. Tap to get started!',
        'meta'    => ['notes'=>$request->notes],
        'at'      => now()->toIso8601String(),
      ]));
    } catch (\Throwable $e) {}
    try {
                NotifyUser::send((int) $host_request->user_id, [
                    'type'   => 'host_approved',
                    'title'  => 'Host request approved 🎉',
                    'body'   => 'You can now host live rooms. Tap to get started!',
                    'meta'   => ['notes' => $request->notes],
                    'screen' => 'notifications',
                ], [
                    'push'    => true,
                    'persist' => true,
                ]);
            } catch (\Throwable $e) {}
    });

    return redirect()->route('admin.host-requests.index')->with('ok','Approved.');
  }
}
