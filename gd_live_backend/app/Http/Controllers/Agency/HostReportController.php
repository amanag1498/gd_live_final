<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Admin\ReportsController;
use App\Models\Agency;
use App\Models\Host;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HostReportController extends ReportsController
{
    protected function hostReportQuery(Request $request): Builder
    {
        return Host::query()->where('agency_id', $this->agency($request)->id);
    }

    protected function authorizeHostReport(Request $request, Host $host): void
    {
        abort_unless((int) $host->agency_id === (int) $this->agency($request)->id, 404);
    }

    protected function hostReportViewData(?Host $host = null): array
    {
        return [
            'reportLayout' => 'layouts.agency-tailadmin',
            'hostReportsRouteName' => 'agency.reports.hosts',
            'hostReportsCsvRouteName' => 'agency.reports.hosts.csv',
            'hostReportShowRouteName' => 'agency.reports.hosts.show',
            'hostActionRoute' => $host ? route('agency.hosts.show', $host) : null,
            'hostActionLabel' => 'View Host',
        ];
    }

    private function agency(Request $request): Agency
    {
        return Agency::query()
            ->where('owner_user_id', $request->user()->id)
            ->firstOrFail();
    }
}
