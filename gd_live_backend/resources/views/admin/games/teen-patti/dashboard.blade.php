@extends('layouts.admin-tailadmin')

@section('title', 'Teen Patti')

@section('content')
  @php
    $round = $payload['current_round'] ?? null;
    $recentRounds = collect($payload['recent_rounds'] ?? []);
    $recentBets = collect($payload['recent_bets'] ?? []);
    $recentPayouts = collect($payload['recent_payouts'] ?? []);
    $recentFinancialLedgerEntries = collect($payload['recent_financial_ledger_entries'] ?? []);
    $companySummary = $payload['company_summary'] ?? [];
    $financialAccount = $payload['financial_account'] ?? [];

    $currentTotal = (int) data_get($round, 'totals.A', 0)
      + (int) data_get($round, 'totals.B', 0)
      + (int) data_get($round, 'totals.C', 0);
    $winnerPot = data_get($round, 'winning_pot');
    $currentStatus = (string) data_get($round, 'status', 'idle');

    $settledRounds = $recentRounds->where('status', 'settled')->count();
    $openRounds = $recentRounds->whereIn('status', ['open', 'locked'])->count();
    $recentBetVolume = (int) $recentBets->sum('amount');
    $recentPayoutVolume = (int) $recentPayouts->sum('payout_coins');
    $treasuryBalance = (int) data_get($financialAccount, 'treasury_balance_coins', 0);
    $companyCommissionBalance = (int) data_get($financialAccount, 'company_commission_balance_coins', 0);

    $statusTone = match ($currentStatus) {
      'open' => 'success',
      'locked' => 'warning',
      'settled' => 'primary',
      'cancelled' => 'danger',
      default => 'secondary',
    };
  @endphp

  <div class="admin-page-shell teen-patti-admin">
    <section class="admin-page-hero">
      <div class="row g-3 align-items-center">
        <div class="col-lg-8">
          <span class="admin-page-eyebrow"><i class="ti ti-device-gamepad-2"></i> Game Operations</span>
          <h1 class="admin-page-title">Teen Patti Control Room</h1>
          <p class="admin-page-subtitle">
            See whether the game is live, how much is in play, what changed recently, and jump directly to rounds, bets, payouts, or settings.
          </p>
        </div>
        <div class="col-lg-4">
          <div class="admin-page-actions">
            <a href="{{ route('admin.settings.games.edit', ['game' => 'teen_patti']) }}" class="btn btn-light border">Game Settings</a>
            <a href="{{ route('admin.games.teen-patti.report') }}" class="btn btn-light border">User Report</a>
            <a href="{{ route('admin.games.teen-patti.rounds') }}" class="btn btn-light border">Rounds</a>
            <a href="{{ route('admin.games.teen-patti.bets') }}" class="btn btn-light border">Bets</a>
            <a href="{{ route('admin.games.teen-patti.payouts') }}" class="btn btn-light border">Payouts</a>
            <form method="post" action="{{ route('admin.games.teen-patti.tick') }}">
              @csrf
              <button class="btn btn-primary"><i class="ti ti-player-play me-1"></i> Tick Round</button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <div class="row g-3">
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Current Status</span>
            <div class="tp-stat-value">{{ ucfirst($currentStatus === 'idle' ? 'not started' : $currentStatus) }}</div>
            <span class="badge text-bg-{{ $statusTone }}">{{ strtoupper($currentStatus) }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Current Round Exposure</span>
            <div class="tp-stat-value">{{ number_format($currentTotal) }}</div>
            <div class="tp-stat-meta">Total coins shown across A, B, and C</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Recent Bet Volume</span>
            <div class="tp-stat-value">{{ number_format($recentBetVolume) }}</div>
            <div class="tp-stat-meta">{{ $recentBets->count() }} recent ledger rows</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Recent Payout Volume</span>
            <div class="tp-stat-value">{{ number_format($recentPayoutVolume) }}</div>
            <div class="tp-stat-meta">{{ $recentPayouts->count() }} credited payouts</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Teen Patti Treasury</span>
            <div class="tp-stat-value {{ $treasuryBalance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($treasuryBalance) }}</div>
            <div class="tp-stat-meta">95% bet allocations minus payouts</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Company Commission</span>
            <div class="tp-stat-value text-primary">{{ number_format($companyCommissionBalance) }}</div>
            <div class="tp-stat-meta">5% rounded-up bet commission ledger</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Audit Entries</span>
            <div class="tp-stat-value">{{ number_format($recentFinancialLedgerEntries->count()) }}</div>
            <div class="tp-stat-meta">Latest financial ledger rows shown below</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Recovery State</span>
            <div class="tp-stat-value">{{ $treasuryBalance <= 0 ? 'Active' : 'Clear' }}</div>
            <div class="tp-stat-meta">{{ $treasuryBalance <= 0 ? 'Minimum-bet recovery mode' : 'Treasury-affordable mode' }}</div>
          </div>
        </div>
      </div>
    </div>

    @php $companyProfit = (int) data_get($companySummary, 'profit_amount', 0); @endphp
    <div class="row g-3 mt-1">
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Company Bet Volume</span>
            <div class="tp-stat-value">{{ number_format((int) data_get($companySummary, 'total_bet_amount', 0)) }}</div>
            <div class="tp-stat-meta">{{ data_get($companySummary, 'label', 'Last 30 days') }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Win Amount Given</span>
            <div class="tp-stat-value">{{ number_format((int) data_get($companySummary, 'total_win_amount', 0)) }}</div>
            <div class="tp-stat-meta">Payouts credited to users</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Refunded</span>
            <div class="tp-stat-value">{{ number_format((int) data_get($companySummary, 'refunded_amount', 0)) }}</div>
            <div class="tp-stat-meta">Refunded bets in same window</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Company Profit</span>
            <div class="tp-stat-value {{ $companyProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($companyProfit) }}</div>
            <div class="tp-stat-meta">Bet volume - payouts - refunds</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card tp-ledger-card">
      <div class="card-header tp-ledger-header">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="tp-ledger-icon"><i class="ti ti-list-details"></i></span>
            <h5 class="mb-0">Financial Ledger Audit</h5>
          </div>
          <div class="small text-muted">Immutable treasury and commission movements. Values below are stored coin amounts.</div>
        </div>
        <div class="tp-ledger-header-balances">
          <span><small>Treasury</small><strong class="{{ $treasuryBalance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($treasuryBalance) }}</strong></span>
          <span><small>Commission</small><strong>{{ number_format($companyCommissionBalance) }}</strong></span>
        </div>
      </div>

      <div class="tp-ledger-desktop table-responsive d-none d-lg-block">
        <table class="table align-middle mb-0 tp-ledger-table">
          <colgroup>
            <col class="tp-col-event">
            <col class="tp-col-round">
            <col class="tp-col-reference">
            <col class="tp-col-money">
            <col class="tp-col-money">
            <col class="tp-col-money">
            <col class="tp-col-money">
            <col class="tp-col-time">
          </colgroup>
          <thead>
            <tr>
              <th>Event</th>
              <th>Round</th>
              <th>References</th>
              <th class="text-end">Treasury Δ</th>
              <th class="text-end">Commission Δ</th>
              <th class="text-end">Treasury After</th>
              <th class="text-end">Commission After</th>
              <th>Occurred At</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentFinancialLedgerEntries as $entry)
              @php
                $eventLabel = ucfirst(str_replace('_', ' ', $entry->event_type));
                $eventTone = match ($entry->event_type) {
                  'bet_allocation' => 'success',
                  'payout_debit' => 'danger',
                  'bet_refund_reversal' => 'warning',
                  default => 'neutral',
                };
              @endphp
              <tr>
                <td>
                  <span class="tp-ledger-event tp-ledger-event-{{ $eventTone }}">{{ $eventLabel }}</span>
                  <div class="tp-ledger-id">Entry #{{ $entry->id }}</div>
                </td>
                <td><span class="tp-ledger-round">{{ $entry->round?->round_key ?? '—' }}</span></td>
                <td>
                  <div class="tp-ledger-refs">
                    <span>Bet <strong>#{{ $entry->teen_patti_bet_id ?? '—' }}</strong></span>
                    <span>Payout <strong>#{{ $entry->teen_patti_payout_id ?? '—' }}</strong></span>
                  </div>
                </td>
                <td class="text-end"><span class="tp-ledger-amount {{ (int) $entry->treasury_delta_coins >= 0 ? 'is-positive' : 'is-negative' }}">{{ (int) $entry->treasury_delta_coins > 0 ? '+' : '' }}{{ number_format((int) $entry->treasury_delta_coins) }}</span></td>
                <td class="text-end"><span class="tp-ledger-amount {{ (int) $entry->commission_delta_coins >= 0 ? 'is-positive' : 'is-negative' }}">{{ (int) $entry->commission_delta_coins > 0 ? '+' : '' }}{{ number_format((int) $entry->commission_delta_coins) }}</span></td>
                <td class="text-end"><span class="tp-ledger-balance">{{ number_format((int) $entry->treasury_balance_after_coins) }}</span></td>
                <td class="text-end"><span class="tp-ledger-balance">{{ number_format((int) $entry->commission_balance_after_coins) }}</span></td>
                <td><time class="tp-ledger-time" datetime="{{ optional($entry->occurred_at)->toIso8601String() }}">{{ optional($entry->occurred_at)->format('d M Y') ?? '—' }}<small>{{ optional($entry->occurred_at)->format('H:i:s') ?? '' }}</small></time></td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center text-muted py-5">No financial ledger entries yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="tp-ledger-mobile d-lg-none">
        @forelse($recentFinancialLedgerEntries as $entry)
          @php
            $eventLabel = ucfirst(str_replace('_', ' ', $entry->event_type));
            $eventTone = match ($entry->event_type) {
              'bet_allocation' => 'success',
              'payout_debit' => 'danger',
              'bet_refund_reversal' => 'warning',
              default => 'neutral',
            };
          @endphp
          <article class="tp-ledger-mobile-entry">
            <div class="tp-ledger-mobile-head">
              <div><span class="tp-ledger-event tp-ledger-event-{{ $eventTone }}">{{ $eventLabel }}</span><div class="tp-ledger-id">Entry #{{ $entry->id }}</div></div>
              <time class="tp-ledger-time">{{ optional($entry->occurred_at)->format('d M Y') ?? '—' }}<small>{{ optional($entry->occurred_at)->format('H:i:s') ?? '' }}</small></time>
            </div>
            <div class="tp-ledger-mobile-context">
              <div><small>Round</small><strong>{{ $entry->round?->round_key ?? '—' }}</strong></div>
              <div><small>Bet / Payout</small><strong>#{{ $entry->teen_patti_bet_id ?? '—' }} / #{{ $entry->teen_patti_payout_id ?? '—' }}</strong></div>
            </div>
            <div class="tp-ledger-money-grid">
              <div><small>Treasury Δ</small><strong class="{{ (int) $entry->treasury_delta_coins >= 0 ? 'text-success' : 'text-danger' }}">{{ (int) $entry->treasury_delta_coins > 0 ? '+' : '' }}{{ number_format((int) $entry->treasury_delta_coins) }}</strong></div>
              <div><small>Commission Δ</small><strong class="{{ (int) $entry->commission_delta_coins >= 0 ? 'text-success' : 'text-danger' }}">{{ (int) $entry->commission_delta_coins > 0 ? '+' : '' }}{{ number_format((int) $entry->commission_delta_coins) }}</strong></div>
              <div><small>Treasury After</small><strong>{{ number_format((int) $entry->treasury_balance_after_coins) }}</strong></div>
              <div><small>Commission After</small><strong>{{ number_format((int) $entry->commission_balance_after_coins) }}</strong></div>
            </div>
          </article>
        @empty
          <div class="text-center text-muted py-5">No financial ledger entries yet.</div>
        @endforelse
      </div>
    </div>

    <div class="row g-4">
      <div class="col-xl-4">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="mb-0">Live Round Snapshot</h5>
          </div>
          <div class="card-body">
            @if($round)
              <div class="tp-detail-list">
                <div class="tp-detail-row">
                  <span>Round key</span>
                  <strong>{{ $round['round_key'] ?? '—' }}</strong>
                </div>
                <div class="tp-detail-row">
                  <span>Winner</span>
                  <strong>{{ $winnerPot ? "Pot {$winnerPot}" : '—' }}</strong>
                </div>
                <div class="tp-detail-row">
                  <span>Display until</span>
                  <strong>{{ !empty($round['display_until']) ? \Illuminate\Support\Carbon::parse($round['display_until'])->format('d M H:i:s') : '—' }}</strong>
                </div>
                <div class="tp-detail-row">
                  <span>Bet count</span>
                  <strong>{{ data_get($round, 'total_bets_count', 0) }}</strong>
                </div>
                <div class="tp-detail-row">
                  <span>Participants</span>
                  <strong>{{ data_get($round, 'participant_count', 0) }}</strong>
                </div>
              </div>

              <div class="tp-pot-grid mt-3">
                <div class="tp-pot-card">
                  <span>Pot A</span>
                  <strong>{{ number_format((int) data_get($round, 'totals.A', 0)) }}</strong>
                </div>
                <div class="tp-pot-card">
                  <span>Pot B</span>
                  <strong>{{ number_format((int) data_get($round, 'totals.B', 0)) }}</strong>
                </div>
                <div class="tp-pot-card">
                  <span>Pot C</span>
                  <strong>{{ number_format((int) data_get($round, 'totals.C', 0)) }}</strong>
                </div>
              </div>

              <div class="mt-3 d-grid gap-2">
                <form method="post" action="{{ route('admin.games.teen-patti.tick') }}">
                  @csrf
                  <button class="btn btn-primary w-100">Refresh Game State</button>
                </form>
                <form method="post" action="{{ route('admin.games.teen-patti.rounds.reconcile', $round['id']) }}">
                  @csrf
                  <button class="btn btn-outline-primary w-100">Reconcile Current Round</button>
                </form>
              </div>
            @else
              <p class="text-muted mb-0">No current round payload is available yet.</p>
            @endif
          </div>
        </div>
      </div>

      <div class="col-xl-8">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="mb-0">Game Configuration Summary</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="tp-summary-panel">
                  <div class="tp-summary-title">Status</div>
                  <div class="tp-chip-row">
                    <span class="badge text-bg-{{ $payload['settings']['enabled'] ? 'success' : 'danger' }}">
                      {{ $payload['settings']['enabled'] ? 'Engine enabled' : 'Engine disabled' }}
                    </span>
                    <span class="badge text-bg-{{ $payload['settings']['visible_in_video_room_strip'] ? 'primary' : 'secondary' }}">
                      {{ $payload['settings']['visible_in_video_room_strip'] ? 'Visible in strip' : 'Hidden from strip' }}
                    </span>
                    <span class="badge text-bg-{{ $payload['settings']['fake_bets_enabled'] ? 'warning' : 'secondary' }}">
                      {{ $payload['settings']['fake_bets_enabled'] ? 'Fake bets on' : 'Fake bets off' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="tp-summary-panel">
                  <div class="tp-summary-title">Limits and timing</div>
                  <div class="tp-summary-copy">
                    Min bet {{ $payload['settings']['min_bet'] }}, max bet {{ $payload['settings']['max_bet'] }}, round {{ $payload['settings']['round_duration_seconds'] }}s, lock {{ $payload['settings']['betting_lock_seconds'] }}s before result, display {{ $payload['settings']['result_display_seconds'] }}s.
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="tp-summary-panel">
                  <div class="tp-summary-title">Payout rule</div>
                  <div class="tp-summary-copy">
                    Winners receive {{ $payload['settings']['payout_multiplier'] }}x. Strategy mode is <strong>{{ ucfirst(str_replace('_', ' ', $payload['settings']['winning_strategy_mode'])) }}</strong>.
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="tp-summary-panel">
                  <div class="tp-summary-title">Recent health</div>
                  <div class="tp-summary-copy">
                    {{ $settledRounds }} settled rounds, {{ $openRounds }} open or locked rounds visible in the recent sample.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-xl-7">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Rounds</h5>
            <a href="{{ route('admin.games.teen-patti.rounds') }}" class="btn btn-sm btn-light border">Open full ledger</a>
          </div>
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th>Round</th>
                  <th>Status</th>
                  <th>Totals</th>
                  <th>Winner</th>
                  <th>Window</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentRounds->take(8) as $recentRound)
                  <tr>
                    <td>
                      <div class="fw-semibold">{{ $recentRound->round_key }}</div>
                      <div class="small text-muted">#{{ $recentRound->id }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ ucfirst($recentRound->status) }}</span></td>
                    <td>A {{ $recentRound->total_bet_a }} · B {{ $recentRound->total_bet_b }} · C {{ $recentRound->total_bet_c }}</td>
                    <td>{{ $recentRound->winning_pot ?? '—' }}</td>
                    <td>
                      <div>{{ optional($recentRound->starts_at)->format('d M H:i:s') }}</div>
                      <div class="small text-muted">to {{ optional($recentRound->ends_at)->format('H:i:s') }}</div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-5">No rounds yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-xl-5">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Latest Money Movement</h5>
            <div class="small text-muted">Recent bets and payouts only</div>
          </div>
          <div class="card-body">
            <div class="tp-activity-list">
              @forelse($recentBets->take(4) as $bet)
                <div class="tp-activity-item">
                  <div>
                    <div class="fw-semibold">{{ $bet->user?->name ?? 'Unknown user' }}</div>
                    <div class="small text-muted">Bet {{ $bet->amount }} on pot {{ $bet->pot }}</div>
                  </div>
                  <span class="badge bg-light text-dark border">{{ ucfirst($bet->status) }}</span>
                </div>
              @empty
                <div class="text-muted">No recent bets.</div>
              @endforelse

              @forelse($recentPayouts->take(4) as $payout)
                <div class="tp-activity-item">
                  <div>
                    <div class="fw-semibold">{{ $payout->user?->name ?? 'Unknown user' }}</div>
                    <div class="small text-muted">Payout {{ $payout->payout_coins }} from {{ $payout->round?->round_key ?? '—' }}</div>
                  </div>
                  <span class="badge text-bg-success">{{ ucfirst($payout->status) }}</span>
                </div>
              @empty
                <div class="text-muted">No recent payouts.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .teen-patti-admin .tp-stat-card {
      border-radius: 18px;
    }

    .teen-patti-admin .tp-stat-label {
      display: block;
      color: var(--admin-muted);
      font-size: .8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: .45rem;
    }

    .teen-patti-admin .tp-stat-value {
      font-size: 1.7rem;
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: .45rem;
    }

    .teen-patti-admin .tp-stat-meta {
      color: var(--admin-muted);
      font-size: .88rem;
    }

    .teen-patti-admin .tp-detail-list {
      display: grid;
      gap: .7rem;
    }

    .teen-patti-admin .tp-detail-row {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      padding-bottom: .7rem;
      border-bottom: 1px solid rgba(148, 163, 184, 0.14);
    }

    .teen-patti-admin .tp-detail-row:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }

    .teen-patti-admin .tp-detail-row span {
      color: var(--admin-muted);
    }

    .teen-patti-admin .tp-pot-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: .75rem;
    }

    .teen-patti-admin .tp-pot-card,
    .teen-patti-admin .tp-summary-panel {
      border: 1px solid rgba(148, 163, 184, 0.16);
      border-radius: 16px;
      background: rgba(248, 250, 252, 0.72);
      padding: .9rem 1rem;
    }

    .teen-patti-admin .tp-pot-card span,
    .teen-patti-admin .tp-summary-title {
      display: block;
      color: var(--admin-muted);
      font-size: .78rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: .3rem;
    }

    .teen-patti-admin .tp-pot-card strong {
      font-size: 1.15rem;
      font-weight: 800;
    }

    .teen-patti-admin .tp-chip-row {
      display: flex;
      flex-wrap: wrap;
      gap: .45rem;
    }

    .teen-patti-admin .tp-summary-copy {
      color: var(--admin-text);
      line-height: 1.45;
      font-size: .92rem;
    }

    .teen-patti-admin .tp-activity-list {
      display: grid;
      gap: .8rem;
    }

    .teen-patti-admin .tp-activity-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      border: 1px solid rgba(148, 163, 184, 0.16);
      border-radius: 14px;
      padding: .85rem .95rem;
      background: rgba(248, 250, 252, 0.72);
    }

    .teen-patti-admin .tp-ledger-card {
      overflow: hidden;
      border-radius: 20px;
    }

    .teen-patti-admin .tp-ledger-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
      padding: 1rem 1.15rem;
    }

    .teen-patti-admin .tp-ledger-icon {
      display: inline-grid;
      place-items: center;
      width: 34px;
      height: 34px;
      border-radius: 11px;
      color: #4f46e5;
      background: rgba(79, 70, 229, .10);
      flex: 0 0 auto;
    }

    .teen-patti-admin .tp-ledger-header-balances {
      display: flex;
      align-items: stretch;
      flex-wrap: wrap;
      gap: .55rem;
    }

    .teen-patti-admin .tp-ledger-header-balances > span {
      display: grid;
      gap: .1rem;
      min-width: 112px;
      padding: .5rem .7rem;
      border: 1px solid rgba(148, 163, 184, .20);
      border-radius: 12px;
      background: rgba(148, 163, 184, .07);
    }

    .teen-patti-admin .tp-ledger-header-balances small,
    .teen-patti-admin .tp-ledger-money-grid small,
    .teen-patti-admin .tp-ledger-mobile-context small {
      color: var(--admin-muted);
      font-size: .68rem;
      font-weight: 800;
      letter-spacing: .04em;
      text-transform: uppercase;
    }

    .teen-patti-admin .tp-ledger-header-balances strong {
      font-size: .95rem;
      font-variant-numeric: tabular-nums;
    }

    .teen-patti-admin .tp-ledger-desktop {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .teen-patti-admin .tp-ledger-table {
      width: 100%;
      min-width: 1180px;
      table-layout: fixed;
    }

    .teen-patti-admin .tp-ledger-table .tp-col-event { width: 15%; }
    .teen-patti-admin .tp-ledger-table .tp-col-round { width: 15%; }
    .teen-patti-admin .tp-ledger-table .tp-col-reference { width: 13%; }
    .teen-patti-admin .tp-ledger-table .tp-col-money { width: 11%; }
    .teen-patti-admin .tp-ledger-table .tp-col-time { width: 13%; }

    .teen-patti-admin .tp-ledger-table th {
      padding: .75rem .8rem;
      color: var(--admin-muted);
      font-size: .68rem;
      font-weight: 800;
      letter-spacing: .045em;
      line-height: 1.25;
      text-transform: uppercase;
      white-space: normal;
      vertical-align: bottom;
    }

    .teen-patti-admin .tp-ledger-table td {
      padding: .9rem .8rem;
      vertical-align: middle;
      overflow: hidden;
    }

    .teen-patti-admin .tp-ledger-event {
      display: inline-flex;
      align-items: center;
      max-width: 100%;
      padding: .3rem .55rem;
      border-radius: 999px;
      font-size: .72rem;
      font-weight: 800;
      line-height: 1.2;
      white-space: normal;
      overflow-wrap: anywhere;
    }

    .teen-patti-admin .tp-ledger-event-success { color: #047857; background: rgba(16, 185, 129, .12); }
    .teen-patti-admin .tp-ledger-event-danger { color: #b91c1c; background: rgba(239, 68, 68, .12); }
    .teen-patti-admin .tp-ledger-event-warning { color: #b45309; background: rgba(245, 158, 11, .14); }
    .teen-patti-admin .tp-ledger-event-neutral { color: #475569; background: rgba(100, 116, 139, .12); }

    .teen-patti-admin .tp-ledger-id {
      margin-top: .35rem;
      color: var(--admin-muted);
      font-size: .72rem;
      white-space: nowrap;
    }

    .teen-patti-admin .tp-ledger-round {
      display: block;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
      font-size: .76rem;
      font-weight: 700;
      line-height: 1.35;
      overflow-wrap: anywhere;
    }

    .teen-patti-admin .tp-ledger-refs {
      display: grid;
      gap: .3rem;
      font-size: .75rem;
      white-space: nowrap;
    }

    .teen-patti-admin .tp-ledger-refs span {
      color: var(--admin-muted);
    }

    .teen-patti-admin .tp-ledger-refs strong {
      color: var(--admin-text);
    }

    .teen-patti-admin .tp-ledger-amount,
    .teen-patti-admin .tp-ledger-balance {
      display: inline-block;
      font-size: .84rem;
      font-weight: 800;
      font-variant-numeric: tabular-nums;
      white-space: nowrap;
    }

    .teen-patti-admin .tp-ledger-amount.is-positive { color: #059669; }
    .teen-patti-admin .tp-ledger-amount.is-negative { color: #dc2626; }

    .teen-patti-admin .tp-ledger-time {
      display: grid;
      gap: .15rem;
      font-size: .78rem;
      font-weight: 700;
      white-space: nowrap;
    }

    .teen-patti-admin .tp-ledger-time small {
      color: var(--admin-muted);
      font-size: .72rem;
      font-variant-numeric: tabular-nums;
    }

    .teen-patti-admin .tp-ledger-mobile {
      padding: .85rem;
      background: rgba(148, 163, 184, .04);
    }

    .teen-patti-admin .tp-ledger-mobile-entry {
      padding: .9rem;
      border: 1px solid rgba(148, 163, 184, .18);
      border-radius: 16px;
      background: var(--admin-surface, #fff);
    }

    .teen-patti-admin .tp-ledger-mobile-entry + .tp-ledger-mobile-entry {
      margin-top: .75rem;
    }

    .teen-patti-admin .tp-ledger-mobile-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: .75rem;
    }

    .teen-patti-admin .tp-ledger-mobile-context {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
      gap: .6rem;
      margin-top: .75rem;
    }

    .teen-patti-admin .tp-ledger-mobile-context > div,
    .teen-patti-admin .tp-ledger-money-grid > div {
      display: grid;
      min-width: 0;
      gap: .2rem;
      padding: .6rem;
      border-radius: 11px;
      background: rgba(148, 163, 184, .08);
    }

    .teen-patti-admin .tp-ledger-mobile-context strong {
      font-size: .76rem;
      overflow-wrap: anywhere;
    }

    .teen-patti-admin .tp-ledger-money-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .55rem;
      margin-top: .55rem;
    }

    .teen-patti-admin .tp-ledger-money-grid strong {
      font-size: .88rem;
      font-variant-numeric: tabular-nums;
      overflow-wrap: anywhere;
    }

    @media (max-width: 575.98px) {
      .teen-patti-admin .tp-ledger-header-balances {
        width: 100%;
      }

      .teen-patti-admin .tp-ledger-header-balances > span {
        flex: 1 1 calc(50% - .3rem);
        min-width: 0;
      }
    }
  </style>
@endsection
