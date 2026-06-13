@extends('layouts.admin-tailadmin')
@section('title','Review Agency #'.$agency_request->id)

@php
  $inputClass = 'block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
  $surfaceClass = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60';
  $statusColor = $agency_request->status === 'pending' ? 'warning' : ($agency_request->status === 'approved' ? 'success' : 'error');
@endphp

@section('page_actions')
  <a href="{{ route('admin.agency-requests.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
    <i class="ti ti-arrow-left mr-2"></i>Back to list
  </a>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="flex flex-col gap-4 px-6 py-6 lg:flex-row lg:items-start lg:justify-between lg:px-8">
      <div>
        <div class="mb-3 flex items-center gap-2">
          <x-ui.badge :color="$statusColor">{{ ucfirst($agency_request->status) }}</x-ui.badge>
        </div>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Agency Request #{{ $agency_request->id }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Submitted {{ $agency_request->created_at?->diffForHumans() }}</p>
      </div>
      <div class="grid gap-3 sm:grid-cols-2 lg:min-w-[320px]">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Current Status</div>
          <div class="mt-2 text-base font-semibold text-gray-900 dark:text-white">{{ ucfirst($agency_request->status) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Submitted</div>
          <div class="mt-2 text-base font-semibold text-gray-900 dark:text-white">{{ $agency_request->created_at?->format('d M Y, H:i') ?? '—' }}</div>
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_360px]">
    <div class="space-y-6">
      <x-common.component-card>
        <x-slot:header>
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Applicant</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Identity of the user who submitted this agency application.</p>
          </div>
        </x-slot:header>
        <div class="flex items-start gap-4">
          <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-lg font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
            {{ strtoupper(substr($agency_request->user?->name ?? 'A', 0, 1)) }}
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $agency_request->user?->name ?? '—' }}</div>
              @if($agency_request->user?->email_verified_at)
                <x-ui.badge color="success">Verified</x-ui.badge>
              @endif
            </div>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $agency_request->user?->email ?? '—' }}</div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">User ID: {{ $agency_request->user?->id ?? '—' }}</div>
          </div>
        </div>
      </x-common.component-card>

      <x-common.component-card>
        <x-slot:header>
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Application Details</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Legal and public information submitted for review.</p>
          </div>
        </x-slot:header>
        <div class="grid gap-3 md:grid-cols-2">
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Agency Name</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $agency_request->agency_name }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Legal Name</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $agency_request->legal_name ?: '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Phone</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $agency_request->contact_phone ?: '—' }}</div></div>
          <div class="{{ $surfaceClass }}">
            <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Website</div>
            <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
              @if($agency_request->website)
                <a href="{{ $agency_request->website }}" target="_blank" class="text-brand-600 hover:text-brand-700 dark:text-brand-300">{{ $agency_request->website }}</a>
              @else
                —
              @endif
            </div>
          </div>
          <div class="md:col-span-2 {{ $surfaceClass }}">
            <div class="text-xs uppercase tracking-[0.18em] text-gray-400">About</div>
            <div class="mt-3 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{!! nl2br(e($agency_request->about ?? '—')) !!}</div>
          </div>
        </div>
      </x-common.component-card>
    </div>

    <div class="xl:sticky xl:top-24">
      <x-common.component-card>
        <x-slot:header>
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Review</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Approve or reject this request with an audit note.</p>
          </div>
        </x-slot:header>
        <div class="space-y-3">
          <div class="{{ $surfaceClass }} flex items-center justify-between gap-3">
            <span class="text-sm text-gray-500 dark:text-gray-400">Applied</span>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $agency_request->created_at?->format('d M Y, H:i') ?? '—' }}</span>
          </div>
          <div class="{{ $surfaceClass }} flex items-center justify-between gap-3">
            <span class="text-sm text-gray-500 dark:text-gray-400">Current Status</span>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($agency_request->status) }}</span>
          </div>
          @if($agency_request->reviewed_at)
            <div class="{{ $surfaceClass }} flex items-center justify-between gap-3">
              <span class="text-sm text-gray-500 dark:text-gray-400">Reviewed</span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $agency_request->reviewed_at?->format('d M Y, H:i') }}</span>
            </div>
          @endif

          @if($agency_request->status === 'pending')
            <form method="post" action="{{ route('admin.agency-requests.update',$agency_request) }}" class="space-y-4">
              @csrf
              @method('PUT')
              <input type="hidden" name="action" value="approve" id="actionField">

              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Review notes</label>
                <textarea name="notes" rows="4" class="{{ $inputClass }}" placeholder="Notes for audit trail"></textarea>
              </div>

              <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-success-500 px-4 py-3 text-sm font-semibold text-white hover:bg-success-600" onclick="document.getElementById('actionField').value='approve'">
                <i class="ti ti-check mr-2"></i>Approve
              </button>
              <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-error-500 px-4 py-3 text-sm font-semibold text-white hover:bg-error-600" onclick="document.getElementById('actionField').value='reject'">
                <i class="ti ti-x mr-2"></i>Reject
              </button>
              <a href="{{ route('admin.agency-requests.index') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Back to list</a>
            </form>
          @else
            <div class="rounded-2xl border border-blue-light-200 bg-blue-light-50 px-4 py-3 text-sm text-blue-light-700 dark:border-blue-light-500/30 dark:bg-blue-light-500/10 dark:text-blue-light-300">
              This request is already <strong>{{ $agency_request->status }}</strong>.
            </div>
            <a href="{{ route('admin.agency-requests.index') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Back to list</a>
          @endif
        </div>
      </x-common.component-card>
    </div>
  </section>
</div>
@endsection
