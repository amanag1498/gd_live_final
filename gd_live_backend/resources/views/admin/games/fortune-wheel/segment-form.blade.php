@php
  $rewardType = old('reward_type', $segment?->reward_type ?? 'coins');
@endphp

<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Label</label>
  <input class="{{ $inputClass }}" name="label" value="{{ old('label', $segment?->label) }}" required>
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Reward Type</label>
  <select class="{{ $inputClass }}" name="reward_type">
    @foreach(\App\Models\FortuneWheelSegment::REWARD_TYPES as $type)
      <option value="{{ $type }}" @selected($rewardType === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
    @endforeach
  </select>
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Coins</label>
  <input type="number" min="0" class="{{ $inputClass }}" name="reward_value_coins" value="{{ old('reward_value_coins', $segment?->reward_value_coins ?? 0) }}">
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Entry Pack</label>
  <select class="{{ $inputClass }}" name="entry_pack_id">
    <option value="">None</option>
    @foreach($entryPacks as $pack)
      <option value="{{ $pack->id }}" @selected((int) old('entry_pack_id', $segment?->entry_pack_id) === (int) $pack->id)>{{ $pack->name }}</option>
    @endforeach
  </select>
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Subscription</label>
  <select class="{{ $inputClass }}" name="subscription_plan_id">
    <option value="">None</option>
    @foreach($subscriptionPlans as $plan)
      <option value="{{ $plan->id }}" @selected((int) old('subscription_plan_id', $segment?->subscription_plan_id) === (int) $plan->id)>{{ $plan->name }}</option>
    @endforeach
  </select>
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Duration Hours</label>
  <input type="number" min="1" max="8760" class="{{ $inputClass }}" name="reward_duration_hours" value="{{ old('reward_duration_hours', $segment?->reward_duration_hours) }}">
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Weight</label>
  <input type="number" min="1" class="{{ $inputClass }}" name="weight" value="{{ old('weight', $segment?->weight ?? 1) }}" required>
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Sort</label>
  <input type="number" min="0" class="{{ $inputClass }}" name="sort_order" value="{{ old('sort_order', $segment?->sort_order ?? 0) }}" required>
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Color</label>
  <input class="{{ $inputClass }}" name="color" value="{{ old('color', $segment?->color) }}" placeholder="#f59e0b">
</div>
<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Icon URL</label>
  <input class="{{ $inputClass }}" name="icon_url" value="{{ old('icon_url', $segment?->icon_url) }}">
</div>
<div class="flex items-end">
  <label class="flex h-10 items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 dark:border-gray-700 dark:bg-gray-900">
    <input type="hidden" name="is_active" value="0">
    <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30" type="checkbox" name="is_active" value="1" @checked(old('is_active', $segment?->is_active ?? true))>
    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
  </label>
</div>
