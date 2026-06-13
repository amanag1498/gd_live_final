@extends('layouts.admin-tailadmin')
@section('title','Notifications · Compose')

@php
  $aud = old('audience', request('audience','user'));
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $textareaClass = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.notifications.index') }}">Back to Recent</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Compose Notification</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Send stored inbox notifications, push notifications, or both to a single user, a role, or everyone.</p>
      </div>
    </x-slot:header>

    <form method="post" action="{{ route('admin.notifications.send') }}" class="space-y-6">
      @csrf

      <div class="grid gap-4 xl:grid-cols-4">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Audience</label>
          <select name="audience" class="{{ $inputClass }}" id="audienceSel" data-initial="{{ $aud }}">
            <option value="user" {{ $aud === 'user' ? 'selected' : '' }}>Single user</option>
            <option value="role" {{ $aud === 'role' ? 'selected' : '' }}>Role</option>
            <option value="all"  {{ $aud === 'all'  ? 'selected' : '' }}>All users</option>
          </select>
        </div>

        <div class="audience-user {{ $aud === 'user' ? '' : 'hidden' }}">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">User ID</label>
          <input type="number" class="{{ $inputClass }}" name="user_id" placeholder="e.g. 15" value="{{ old('user_id', request('user_id')) }}">
        </div>

        <div class="audience-role {{ $aud === 'role' ? '' : 'hidden' }}">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
          <input type="text" class="{{ $inputClass }}" name="role" placeholder="e.g. host" value="{{ old('role', request('role')) }}">
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
          <input type="text" class="{{ $inputClass }}" name="type" placeholder="host_approved" value="{{ old('type', request('type')) }}">
        </div>
      </div>

      <div class="grid gap-4">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
          <input type="text" class="{{ $inputClass }}" name="title" required value="{{ old('title', request('title')) }}">
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Body</label>
          <textarea class="{{ $textareaClass }}" name="body" rows="4">{{ old('body', request('body')) }}</textarea>
        </div>
      </div>

      <div class="grid gap-4 xl:grid-cols-3">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Deep-link screen</label>
          <input type="text" class="{{ $inputClass }}" name="screen" placeholder="notifications | room" value="{{ old('screen', request('screen')) }}">
          <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Use `notifications` to open inbox or `room` to deep-link to a room.</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Room ID</label>
          <input type="text" class="{{ $inputClass }}" name="room_id" placeholder="abc123" value="{{ old('room_id', request('room_id')) }}">
        </div>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60">
          <div class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Options</div>
          <input type="hidden" name="persist" value="0">
          <label class="mb-3 flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="persist" value="1" {{ old('persist', request('persist','1')) == '1' ? 'checked' : '' }}>
            <span>Store in user notifications (DB)</span>
          </label>

          <input type="hidden" name="push" value="0">
          <label class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="push" value="1" {{ old('push', request('push','1')) == '1' ? 'checked' : '' }}>
            <span>Send Firebase push</span>
          </label>
        </div>
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta (JSON, optional)</label>
        <textarea class="{{ $textareaClass }}" name="meta" rows="4" placeholder='{"foo":"bar"}'>{{ old('meta', request('meta')) }}</textarea>
      </div>

      <div class="flex flex-wrap justify-end gap-3">
        <x-ui.button variant="outline" href="{{ route('admin.notifications.index') }}" size="sm">Cancel</x-ui.button>
        <x-ui.button type="submit" size="sm">Send</x-ui.button>
      </div>
    </form>
  </x-common.component-card>
</div>
@endsection

@push('scripts')
<script>
  (function () {
    const sel = document.getElementById('audienceSel');
    const user = document.querySelector('.audience-user');
    const role = document.querySelector('.audience-role');
    if (!sel || !user || !role) return;

    function sync() {
      user.classList.toggle('hidden', sel.value !== 'user');
      role.classList.toggle('hidden', sel.value !== 'role');
    }

    sel.value = sel.dataset.initial || sel.value || 'user';
    sync();
    sel.addEventListener('change', sync);
  })();
</script>
@endpush
