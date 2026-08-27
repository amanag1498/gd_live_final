@php
  $formContext = $segment ? 'segment-'.$segment->id : 'new';
  $useOldInput = old('_segment_context') === $formContext;
  $fieldValue = static fn (string $field, mixed $default = null) => $useOldInput ? old($field, $default) : $default;
  $rewardType = $fieldValue('reward_type', $segment?->reward_type ?? 'coins');
  $color = $fieldValue('color', $segment?->color ?? '#7C3AED');
@endphp

<input type="hidden" name="_segment_context" value="{{ $formContext }}">

<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Label</label>
  <input class="{{ $inputClass }}" name="label" value="{{ $fieldValue('label', $segment?->label) }}" placeholder="e.g. 100 Coins" required>
</div>

<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Reward Type</label>
  <select class="{{ $inputClass }}" name="reward_type" data-fortune-reward-type>
    @foreach(\App\Models\FortuneWheelSegment::REWARD_TYPES as $type)
      <option value="{{ $type }}" @selected($rewardType === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
    @endforeach
  </select>
</div>

<div data-fortune-field="coins">
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Coin Reward</label>
  <input type="number" min="0" class="{{ $inputClass }}" name="reward_value_coins" value="{{ $fieldValue('reward_value_coins', $segment?->reward_value_coins ?? 0) }}">
  <p class="mt-1 text-xs text-gray-500">Zero is a valid reward and does not grant another spin.</p>
</div>

<div data-fortune-field="entry_pack">
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Entry Pack</label>
  <select class="{{ $inputClass }}" name="entry_pack_id">
    <option value="">Choose an active pack</option>
    @foreach($entryPacks as $pack)
      <option value="{{ $pack->id }}" @selected((int) $fieldValue('entry_pack_id', $segment?->entry_pack_id) === (int) $pack->id)>{{ $pack->name }}</option>
    @endforeach
  </select>
</div>

<div data-fortune-field="subscription">
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Subscription</label>
  <select class="{{ $inputClass }}" name="subscription_plan_id">
    <option value="">Choose an active plan</option>
    @foreach($subscriptionPlans as $plan)
      <option value="{{ $plan->id }}" @selected((int) $fieldValue('subscription_plan_id', $segment?->subscription_plan_id) === (int) $plan->id)>{{ $plan->name }}</option>
    @endforeach
  </select>
</div>

<div data-fortune-field="duration">
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Access Duration</label>
  <div class="relative">
    <input type="number" min="1" max="8760" class="{{ $inputClass }} pr-16" name="reward_duration_hours" value="{{ $fieldValue('reward_duration_hours', $segment?->reward_duration_hours) }}">
    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-gray-400">hours</span>
  </div>
</div>

<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Selection Weight</label>
  <input type="number" min="1" class="{{ $inputClass }}" name="weight" value="{{ $fieldValue('weight', $segment?->weight ?? 1) }}" required>
  <p class="mt-1 text-xs text-gray-500">Probability is this weight divided by all eligible weights.</p>
</div>

<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Display Order</label>
  <input type="number" min="0" class="{{ $inputClass }}" name="sort_order" value="{{ $fieldValue('sort_order', $segment?->sort_order ?? 0) }}" required>
</div>

<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Wheel Color</label>
  <div class="flex gap-2">
    <input type="color" value="{{ $color }}" class="h-10 w-12 cursor-pointer rounded-xl border border-gray-300 bg-white p-1 dark:border-gray-700 dark:bg-gray-900" data-fortune-color-picker aria-label="Choose wheel color">
    <input class="{{ $inputClass }} font-mono uppercase" name="color" value="{{ $color }}" placeholder="#7C3AED" maxlength="7" data-fortune-color-text>
  </div>
</div>

<div>
  <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Reward Icon URL</label>
  <input type="url" class="{{ $inputClass }}" name="icon_url" value="{{ $fieldValue('icon_url', $segment?->icon_url) }}" placeholder="https://.../reward.png">
  <p class="mt-1 text-xs text-gray-500">Optional PNG, JPG, WebP, or SVG shown on the wheel.</p>
</div>

<div class="flex items-end">
  <label class="flex h-10 w-full items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 dark:border-gray-700 dark:bg-gray-900">
    <input type="hidden" name="is_active" value="0">
    <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30" type="checkbox" name="is_active" value="1" @checked((bool) $fieldValue('is_active', $segment?->is_active ?? true))>
    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Selectable and visible</span>
  </label>
</div>
