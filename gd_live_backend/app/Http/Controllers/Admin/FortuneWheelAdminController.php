<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FortuneWheelSegment;
use App\Services\FortuneWheelService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FortuneWheelAdminController extends Controller
{
    public function __construct(private FortuneWheelService $fortuneWheel) {}

    public function dashboard()
    {
        return view('admin.games.fortune-wheel.dashboard', [
            'payload' => $this->fortuneWheel->adminDashboardPayload(),
        ]);
    }

    public function storeSegment(Request $request)
    {
        $data = $this->validatedSegment($request);
        FortuneWheelSegment::query()->create($data);

        return back()->with('ok', 'Fortune Wheel segment created.');
    }

    public function updateSegment(Request $request, FortuneWheelSegment $segment)
    {
        $segment->update($this->validatedSegment($request));

        return back()->with('ok', 'Fortune Wheel segment updated.');
    }

    public function destroySegment(FortuneWheelSegment $segment)
    {
        $segment->delete();

        return back()->with('ok', 'Fortune Wheel segment deleted.');
    }

    private function validatedSegment(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'reward_type' => ['required', 'string', Rule::in(FortuneWheelSegment::REWARD_TYPES)],
            'reward_value_coins' => ['nullable', 'integer', 'min:0'],
            'entry_pack_id' => ['nullable', 'integer', 'exists:entry_packs,id'],
            'subscription_plan_id' => ['nullable', 'integer', 'exists:subscription_plans,id'],
            'reward_duration_hours' => ['nullable', 'integer', 'min:1', 'max:8760'],
            'weight' => ['required', 'integer', 'min:1', 'max:100000'],
            'color' => ['nullable', 'string', 'max:32'],
            'icon_url' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $data['reward_value_coins'] = (int) ($data['reward_value_coins'] ?? 0);
        $data['entry_pack_id'] = $data['reward_type'] === FortuneWheelSegment::REWARD_ENTRY_PACK
            ? $data['entry_pack_id']
            : null;
        $data['subscription_plan_id'] = $data['reward_type'] === FortuneWheelSegment::REWARD_SUBSCRIPTION
            ? $data['subscription_plan_id']
            : null;
        $data['reward_duration_hours'] = in_array($data['reward_type'], [FortuneWheelSegment::REWARD_ENTRY_PACK, FortuneWheelSegment::REWARD_SUBSCRIPTION], true)
            ? $data['reward_duration_hours']
            : null;

        if ($data['reward_type'] === FortuneWheelSegment::REWARD_ENTRY_PACK && empty($data['entry_pack_id'])) {
            throw ValidationException::withMessages([
                'entry_pack_id' => 'Choose an entry pack reward.',
            ]);
        }

        if ($data['reward_type'] === FortuneWheelSegment::REWARD_SUBSCRIPTION && empty($data['subscription_plan_id'])) {
            throw ValidationException::withMessages([
                'subscription_plan_id' => 'Choose a subscription reward.',
            ]);
        }

        if (in_array($data['reward_type'], [FortuneWheelSegment::REWARD_ENTRY_PACK, FortuneWheelSegment::REWARD_SUBSCRIPTION], true) && empty($data['reward_duration_hours'])) {
            throw ValidationException::withMessages([
                'reward_duration_hours' => 'Reward duration is required for timed rewards.',
            ]);
        }

        return $data;
    }
}
