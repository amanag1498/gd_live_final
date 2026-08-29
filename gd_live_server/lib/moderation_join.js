'use strict';

function moderationJoinErrorPayload(decision, context = {}) {
  const input = decision && typeof decision === 'object' ? decision : {};
  const decisionCode = String(input.code || input.error || '').trim().toUpperCase();
  const decisionReason = String(input.reason || input.message || '').trim();
  const explicitlyBlocked = decisionCode === 'HOST_BLOCKED'
    || decisionReason.toLowerCase() === 'you were blocked by this host.';

  return {
    room_id: String(context.roomId || ''),
    code: explicitlyBlocked
      ? 'HOST_BLOCKED'
      : (decisionCode || 'MODERATION_CHECK_FAILED'),
    message: explicitlyBlocked
      ? 'You were blocked by this host.'
      : (decisionReason || 'Unable to validate room access right now.'),
    host_user_id: context.host_user_id || null,
    target_user_id: context.target_user_id || null,
    at: context.at || new Date().toISOString(),
  };
}

module.exports = { moderationJoinErrorPayload };
