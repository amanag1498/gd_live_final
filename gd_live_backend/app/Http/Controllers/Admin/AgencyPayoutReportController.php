<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\AgencyPayoutReport;
use App\Models\AgencyPayoutReportItem;
use App\Services\AgencyWeeklyPayoutReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AgencyPayoutReportController extends Controller
{
    public function __construct(private AgencyWeeklyPayoutReportService $service)
    {
    }

    public function index(Request $request)
    {
        $reports = AgencyPayoutReport::query()
            ->with(['agency.owner', 'items', 'publishedByAdmin'])
            ->when($request->filled('agency_id'), fn ($query) => $query->where('agency_id', (int) $request->integer('agency_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('week_start'), fn ($query) => $query->whereDate('period_start', $request->date('week_start')->toDateString()))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('period_start', '>=', $request->date('date_from')->toDateString()))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('period_end', '<=', $request->date('date_to')->toDateString()))
            ->latest('period_start')
            ->paginate(20)
            ->withQueryString();

        $summaryQuery = AgencyPayoutReport::query()
            ->when($request->filled('agency_id'), fn ($query) => $query->where('agency_id', (int) $request->integer('agency_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('week_start'), fn ($query) => $query->whereDate('period_start', $request->date('week_start')->toDateString()))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('period_start', '>=', $request->date('date_from')->toDateString()))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('period_end', '<=', $request->date('date_to')->toDateString()));

        return view('admin.agency-payout-reports.index', [
            'reports' => $reports,
            'agencies' => Agency::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => ['generated', 'pending_review', 'approved', 'paid', 'rejected'],
            'summary' => [
                'reports' => (clone $summaryQuery)->count(),
                'gross_earnings' => (int) (clone $summaryQuery)->sum('gross_earnings'),
                'agency_commission' => (int) (clone $summaryQuery)->sum('agency_commission'),
                'final_payable' => (int) (clone $summaryQuery)->sum('final_payable'),
                'paid' => (clone $summaryQuery)->where('status', 'paid')->count(),
                'published' => (clone $summaryQuery)->whereNotNull('published_at')->count(),
            ],
        ]);
    }

    public function show(AgencyPayoutReport $agency_payout_report)
    {
        $agency_payout_report->load(['agency.owner', 'items.host.user', 'publishedByAdmin']);

        return view('admin.agency-payout-reports.show', [
            'report' => $agency_payout_report,
        ]);
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date',
            'agency_id' => 'nullable|integer|exists:agencies,id',
            'force' => 'nullable|boolean',
        ]);

        try {
            [$start, $end] = $this->service->resolvePeriod(
                $data['start'] ?? null,
                $data['end'] ?? null,
            );

            $result = $this->service->generate(
                periodStart: $start,
                periodEnd: $end,
                agencyId: isset($data['agency_id']) ? (int) $data['agency_id'] : null,
                force: (bool) ($data['force'] ?? false),
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['generate' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.agency-payout-reports.index', [
                'date_from' => $start->toDateString(),
                'date_to' => $end->toDateString(),
                'agency_id' => $data['agency_id'] ?? null,
            ])
            ->with('status', 'Generated ' . $result['generated_count'] . ' payout report(s).');
    }

    public function review(Request $request, AgencyPayoutReport $agency_payout_report)
    {
        $data = $request->validate([
            'deductions' => 'nullable|integer|min:0',
            'admin_remarks' => 'nullable|string|max:5000',
        ]);

        try {
            $this->service->markPendingReview(
                report: $agency_payout_report,
                deductions: (int) ($data['deductions'] ?? 0),
                remarks: $data['admin_remarks'] ?? null,
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['review' => $e->getMessage()]);
        }

        return redirect()->route('admin.agency-payout-reports.show', $agency_payout_report)->with('status', 'Report moved to pending review.');
    }

    public function approve(Request $request, AgencyPayoutReport $agency_payout_report)
    {
        $data = $request->validate([
            'deductions' => 'nullable|integer|min:0',
            'admin_remarks' => 'nullable|string|max:5000',
        ]);

        try {
            $this->service->approve(
                report: $agency_payout_report,
                deductions: (int) ($data['deductions'] ?? 0),
                remarks: $data['admin_remarks'] ?? null,
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['approve' => $e->getMessage()]);
        }

        return redirect()->route('admin.agency-payout-reports.show', $agency_payout_report)->with('status', 'Report approved.');
    }

    public function publish(Request $request, AgencyPayoutReport $agency_payout_report)
    {
        $data = $request->validate([
            'admin_remarks' => 'nullable|string|max:5000',
        ]);

        try {
            $this->service->publish(
                report: $agency_payout_report,
                remarks: $data['admin_remarks'] ?? null,
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['publish' => $e->getMessage()]);
        }

        return redirect()->route('admin.agency-payout-reports.show', $agency_payout_report)->with('status', 'Report published to agency dashboard.');
    }

    public function reject(Request $request, AgencyPayoutReport $agency_payout_report)
    {
        $data = $request->validate([
            'admin_remarks' => 'required|string|max:5000',
        ]);

        try {
            $this->service->reject(
                report: $agency_payout_report,
                remarks: $data['admin_remarks'],
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['reject' => $e->getMessage()]);
        }

        return redirect()->route('admin.agency-payout-reports.show', $agency_payout_report)->with('status', 'Report rejected.');
    }

    public function markPaid(Request $request, AgencyPayoutReport $agency_payout_report)
    {
        $data = $request->validate([
            'admin_remarks' => 'nullable|string|max:5000',
        ]);

        try {
            $this->service->markPaid(
                report: $agency_payout_report,
                remarks: $data['admin_remarks'] ?? null,
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['mark_paid' => $e->getMessage()]);
        }

        return redirect()->route('admin.agency-payout-reports.show', $agency_payout_report)->with('status', 'Report marked as paid.');
    }

    public function export(AgencyPayoutReport $agency_payout_report)
    {
        return $this->pdfResponse($agency_payout_report, false);
    }

    public function preview(AgencyPayoutReport $agency_payout_report)
    {
        return $this->pdfResponse($agency_payout_report, true);
    }

    private function pdfResponse(AgencyPayoutReport $agency_payout_report, bool $inline)
    {
        $agency_payout_report->load(['agency.owner', 'items.host.user', 'publishedByAdmin']);
        $data = ['report' => $agency_payout_report];

        if (class_exists(Pdf::class) || app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('pdf.agency-payout-report', $data)
                ->setPaper('a4', 'landscape');

            $filename = 'agency-payout-report-' . $agency_payout_report->id . '.pdf';
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($inline ? 'inline' : 'attachment') . '; filename="' . $filename . '"',
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
                'Pragma' => 'public',
            ];
            if (! $inline) {
                $headers['X-Download-Options'] = 'noopen';
            }

            return response($pdf->output(), 200, $headers);
        }

        return response()
            ->view('pdf.agency-payout-report', $data)
            ->header('X-Agency-Payout-Report-Fallback', 'print-view');
    }

    public function updateItem(Request $request, AgencyPayoutReport $agency_payout_report, AgencyPayoutReportItem $agency_payout_report_item)
    {
        $data = $request->validate([
            'video_room_minutes' => 'required|integer|min:0',
            'video_gift_coins' => 'required|integer|min:0',
            'pk_gift_coins' => 'required|integer|min:0',
            'video_call_coins' => 'required|integer|min:0',
            'video_call_minutes' => 'required|integer|min:0',
            'bonus_coins' => 'required|integer|min:0',
            'host_payout_inr' => 'nullable|numeric|min:0',
            'agency_commission_inr' => 'nullable|numeric|min:0',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        try {
            $updatedReport = $this->service->updateItem(
                report: $agency_payout_report,
                item: $agency_payout_report_item,
                payload: $data,
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => ['update_item' => [$e->getMessage()]],
                ], 422);
            }

            return back()->withInput()->withErrors(['update_item' => $e->getMessage()]);
        }

        if ($request->expectsJson()) {
            $updatedItem = $updatedReport->items->firstWhere('id', $agency_payout_report_item->id);

            return response()->json([
                'message' => 'Host payout row updated. Approved reports return to pending review after edits.',
                'item' => [
                    'id' => $updatedItem->id,
                    'video_room_minutes' => $updatedItem->video_room_minutes,
                    'video_gift_coins' => $updatedItem->video_gift_coins,
                    'pk_gift_coins' => $updatedItem->pk_gift_coins,
                    'video_call_coins' => $updatedItem->video_call_coins,
                    'video_call_minutes' => $updatedItem->video_call_minutes,
                    'bonus_coins' => $updatedItem->bonus_coins,
                    'total_coins' => $updatedItem->total_coins,
                    'host_payout_inr' => $updatedItem->host_payout_inr,
                    'agency_commission_inr' => $updatedItem->agency_commission_inr,
                    'total_inr' => $updatedItem->total_inr,
                    'admin_note' => $updatedItem->admin_note,
                ],
                'report' => $this->reportPayload($updatedReport),
            ]);
        }

        return redirect()
            ->route('admin.agency-payout-reports.show', $agency_payout_report)
            ->with('status', 'Host payout row updated. Approved reports return to pending review after edits.');
    }

    public function destroyItem(Request $request, AgencyPayoutReport $agency_payout_report, AgencyPayoutReportItem $agency_payout_report_item)
    {
        try {
            $updatedReport = $this->service->deleteItem(
                report: $agency_payout_report,
                item: $agency_payout_report_item,
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => ['delete_item' => [$e->getMessage()]],
                ], 422);
            }

            return back()->withErrors(['delete_item' => $e->getMessage()]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Host payout row deleted. Report totals were recalculated.',
                'deleted_item_id' => $agency_payout_report_item->id,
                'report' => $this->reportPayload($updatedReport),
            ]);
        }

        return redirect()
            ->route('admin.agency-payout-reports.show', $agency_payout_report)
            ->with('status', 'Host payout row deleted. Report totals were recalculated.');
    }

    public function destroy(Request $request, AgencyPayoutReport $agency_payout_report)
    {
        $data = $request->validate([
            'admin_remarks' => 'nullable|string|max:5000',
        ]);

        try {
            $this->service->deleteReport(
                report: $agency_payout_report,
                remarks: $data['admin_remarks'] ?? null,
                actor: $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['delete_report' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.agency-payout-reports.index')
            ->with('status', 'Payout report deleted.');
    }

    private function reportPayload(AgencyPayoutReport $report): array
    {
        return [
            'id' => $report->id,
            'status' => $report->status,
            'status_label' => str($report->status)->replace('_', ' ')->title()->toString(),
            'published' => (bool) $report->published_at,
            'visibility_label' => $report->published_at ? 'Published' : 'Draft only',
            'totals' => [
                'total_hosts' => $report->total_hosts,
                'active_hosts_count' => $report->active_hosts_count,
                'total_coins' => $report->total_coins,
                'final_payable' => $report->final_payable,
                'total_video_room_minutes' => $report->total_video_room_minutes,
                'total_video_gift_coins' => $report->total_video_gift_coins,
                'total_pk_gift_coins' => $report->total_pk_gift_coins,
                'total_video_call_coins' => $report->total_video_call_coins,
                'total_video_call_minutes' => $report->total_video_call_minutes,
                'total_bonus_coins' => $report->total_bonus_coins,
                'total_host_payout_inr' => $report->total_host_payout_inr,
                'total_agency_commission_inr' => $report->total_agency_commission_inr,
                'total_inr' => $report->total_inr,
            ],
            'actions' => [
                'can_review' => in_array($report->status, ['generated', 'pending_review'], true),
                'can_approve' => in_array($report->status, ['generated', 'pending_review'], true),
                'can_publish' => $report->status === 'approved' && ! $report->published_at,
                'can_mark_paid' => $report->status === 'approved' && (bool) $report->published_at,
                'can_reject' => in_array($report->status, ['generated', 'pending_review'], true),
            ],
        ];
    }
}
