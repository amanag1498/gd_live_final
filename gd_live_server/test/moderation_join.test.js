'use strict';

const test = require('node:test');
const assert = require('node:assert/strict');
const { moderationJoinErrorPayload } = require('../lib/moderation_join');

const context = {
  roomId: 'room-1',
  host_user_id: 10,
  target_user_id: 20,
  at: '2026-08-29T00:00:00.000Z',
};

test('preserves an explicit host block decision', () => {
  const payload = moderationJoinErrorPayload({
    allow: false,
    code: 'HOST_BLOCKED',
    reason: 'You were blocked by this host.',
  }, context);

  assert.equal(payload.code, 'HOST_BLOCKED');
  assert.equal(payload.target_user_id, 20);
  assert.equal(payload.host_user_id, 10);
});

test('supports an older backend explicit host block reason', () => {
  const payload = moderationJoinErrorPayload({
    allow: false,
    reason: 'You were blocked by this host.',
  }, context);

  assert.equal(payload.code, 'HOST_BLOCKED');
});

test('does not mislabel an access-check failure as a host block', () => {
  const payload = moderationJoinErrorPayload({
    allow: false,
    code: 'MODERATION_CHECK_FAILED',
    reason: 'Unable to validate room access right now.',
  }, context);

  assert.equal(payload.code, 'MODERATION_CHECK_FAILED');
  assert.equal(payload.message, 'Unable to validate room access right now.');
});
