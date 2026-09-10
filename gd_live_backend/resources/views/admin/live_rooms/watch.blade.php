@extends('layouts.admin-tailadmin')
@section('title', 'Silent Watch · '.$live_room->room_id)

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.live-rooms.show', $live_room) }}">Back to room</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Admin Observer</x-ui.badge>
            <x-ui.badge :color="$live_room->status === 'live' ? 'success' : 'warning'">{{ ucfirst($live_room->status) }}</x-ui.badge>
            <x-ui.badge color="warning">Hidden participant</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $live_room->title ?: $live_room->room_id }}</h2>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            Silent subscribe-only monitoring for room <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs dark:bg-gray-800">{{ $live_room->room_id }}</code>.
            No mobile presence, app audience count, join animation, or room chat event is created by this page.
          </p>
        </div>
        <div class="flex flex-wrap gap-3">
          <button id="observerStart" type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
            <i class="ti ti-eye"></i>
            Start silent watch
          </button>
          <button id="observerStop" type="button" class="hidden inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
            Stop
          </button>
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
    <div class="rounded-3xl border border-gray-200 bg-gray-950 p-3 shadow-theme-lg dark:border-gray-800">
      <div id="observerStage" class="grid min-h-[520px] place-items-center overflow-hidden rounded-2xl bg-black text-center text-sm text-gray-400">
        <div id="observerEmpty" class="max-w-sm px-6">
          <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-white/10 text-white">
            <i class="ti ti-video text-2xl"></i>
          </div>
          <p class="font-semibold text-white">Ready to watch silently</p>
          <p class="mt-2 text-gray-400">Click start. The page will subscribe to host and speaker tracks without publishing anything.</p>
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <x-common.component-card title="Observer status" desc="This state is local to the admin browser.">
        <div class="space-y-3 text-sm">
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-500 dark:text-gray-400">Connection</span>
            <x-ui.badge color="dark"><span id="observerStatus">Idle</span></x-ui.badge>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-500 dark:text-gray-400">Visible to users</span>
            <x-ui.badge color="success">No</x-ui.badge>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-500 dark:text-gray-400">Can publish</span>
            <x-ui.badge color="success">No</x-ui.badge>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-500 dark:text-gray-400">Tracks</span>
            <span id="observerTrackCount" class="font-semibold text-gray-900 dark:text-white">0</span>
          </div>
        </div>
      </x-common.component-card>

      <x-common.component-card title="Room" desc="Live room metadata.">
        <div class="space-y-3 text-sm">
          <div class="flex items-start justify-between gap-3">
            <span class="text-gray-500 dark:text-gray-400">Host</span>
            <span class="text-right font-semibold text-gray-900 dark:text-white">{{ $live_room->host?->user?->name ?? '—' }}</span>
          </div>
          <div class="flex items-start justify-between gap-3">
            <span class="text-gray-500 dark:text-gray-400">LiveKit room</span>
            <code class="text-right text-xs text-gray-700 dark:text-gray-200">{{ $live_room->room_id }}</code>
          </div>
          <div class="flex items-start justify-between gap-3">
            <span class="text-gray-500 dark:text-gray-400">Started</span>
            <span class="text-right font-semibold text-gray-900 dark:text-white">{{ $live_room->started_at?->format('d M Y H:i') ?? '—' }}</span>
          </div>
        </div>
      </x-common.component-card>
    </div>
  </section>
</div>
@endsection

@push('scripts')
<script
  src="https://cdn.jsdelivr.net/npm/livekit-client@2.5.1/dist/livekit-client.umd.min.js"
  integrity="sha384-33/ircfwMJCm1GLQ7AtQqMJP2NkDNfaPjfNrxlXLbdr/SFLrCTG7/EbQ6IA9g1Ul"
  crossorigin="anonymous"
></script>
<script>
(() => {
  const tokenUrl = @json(route('admin.live-rooms.observer-token', $live_room));
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const stage = document.getElementById('observerStage');
  const empty = document.getElementById('observerEmpty');
  const startButton = document.getElementById('observerStart');
  const stopButton = document.getElementById('observerStop');
  const statusNode = document.getElementById('observerStatus');
  const trackCountNode = document.getElementById('observerTrackCount');
  const trackElements = new Map();
  let room = null;
  let expiryTimer = null;
  let stopping = false;

  const setStatus = (value) => {
    statusNode.textContent = value;
  };

  const syncTrackCount = () => {
    trackCountNode.textContent = String(trackElements.size);
    empty?.classList.toggle('hidden', trackElements.size > 0);
    if (trackElements.size === 0) {
      stage.classList.add('place-items-center');
      stage.classList.remove('grid-cols-1', 'gap-3', 'md:grid-cols-2');
    }
  };

  const addTrack = (track, participant) => {
    if (!track || trackElements.has(track.sid)) return;

    const wrapper = document.createElement('div');
    wrapper.dataset.trackSid = track.sid;
    wrapper.className = 'relative min-h-[240px] overflow-hidden rounded-2xl bg-gray-900';

    const media = track.attach();
    media.autoplay = true;
    media.playsInline = true;
    media.className = track.kind === 'video'
      ? 'h-full min-h-[240px] w-full object-cover'
      : 'hidden';
    wrapper.appendChild(media);

    const label = document.createElement('div');
    label.className = 'absolute bottom-3 left-3 rounded-full bg-black/60 px-3 py-1 text-xs font-semibold text-white';
    label.textContent = participant?.name || participant?.identity || 'Participant';
    wrapper.appendChild(label);

    if (track.kind === 'audio') {
      wrapper.classList.add('hidden');
    }

    if (stage.classList.contains('place-items-center')) {
      stage.classList.remove('place-items-center');
      stage.classList.add('grid-cols-1', 'gap-3', 'md:grid-cols-2');
    }

    stage.appendChild(wrapper);
    trackElements.set(track.sid, { track, wrapper });
    syncTrackCount();
  };

  const removeTrack = (track) => {
    if (!track || !trackElements.has(track.sid)) return;
    const item = trackElements.get(track.sid);
    track.detach().forEach((element) => element.remove());
    item.wrapper.remove();
    trackElements.delete(track.sid);
    syncTrackCount();
  };

  const attachExistingTracks = () => {
    room.remoteParticipants.forEach((participant) => {
      participant.trackPublications.forEach((publication) => {
        if (publication.track) {
          addTrack(publication.track, participant);
        }
      });
    });
  };

  const clearTracks = () => {
    trackElements.forEach(({ track, wrapper }) => {
      track.detach().forEach((element) => element.remove());
      wrapper.remove();
    });
    trackElements.clear();
    syncTrackCount();
  };

  const stop = async (status = 'Stopped') => {
    if (stopping) return;
    stopping = true;
    if (expiryTimer) {
      window.clearTimeout(expiryTimer);
      expiryTimer = null;
    }

    const activeRoom = room;
    room = null;
    if (activeRoom) await activeRoom.disconnect();
    clearTracks();
    setStatus(status);
    startButton.disabled = false;
    startButton.classList.remove('hidden');
    stopButton.classList.add('hidden');
    stopping = false;
  };

  startButton.addEventListener('click', async () => {
    if (!window.LivekitClient) {
      setStatus('LiveKit client failed to load');
      return;
    }

    startButton.disabled = true;
    setStatus('Requesting token…');

    try {
      const response = await fetch(tokenUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        credentials: 'same-origin',
      });
      const payload = await response.json();
      if (!response.ok || !payload.ok) {
        throw new Error(payload.message || 'Unable to start observer.');
      }

      const { Room, RoomEvent } = window.LivekitClient;
      room = new Room({
        adaptiveStream: true,
        dynacast: false,
      });

      room
        .on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
          addTrack(track, participant);
        })
        .on(RoomEvent.TrackUnsubscribed, removeTrack)
        .on(RoomEvent.Disconnected, () => {
          if (!stopping) void stop('Disconnected');
        });

      setStatus('Connecting…');
      await room.connect(payload.ws_url, payload.token, { autoSubscribe: true });
      if (typeof room.startAudio === 'function') {
        await room.startAudio().catch(() => {});
      }
      attachExistingTracks();
      expiryTimer = window.setTimeout(
        () => void stop('Session expired'),
        Math.max(1, Number(payload.expires_in || 900)) * 1000,
      );
      setStatus('Watching silently');
      startButton.classList.add('hidden');
      stopButton.classList.remove('hidden');
    } catch (error) {
      await stop(error?.message || 'Observer failed');
    }
  });

  stopButton.addEventListener('click', () => void stop());
  window.addEventListener('beforeunload', () => {
    if (expiryTimer) window.clearTimeout(expiryTimer);
    if (room) void room.disconnect();
  });
})();
</script>
@endpush
