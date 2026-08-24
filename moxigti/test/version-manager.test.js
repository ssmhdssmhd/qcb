import { test } from 'node:test';
import assert from 'node:assert/strict';
import { VersionManager, versionFromRevision, formatVersion, parseVersion, STEP } from '../src/core/version-manager.js';

test('初始版本为 v.0.0.1', () => {
  const mgr = new VersionManager();
  assert.equal(mgr.current, 'v.0.0.1');
  assert.equal(mgr.revision, 1);
});

test('patch 逐次 +1 (v.0.0.1 -> v.0.0.2)', () => {
  const mgr = new VersionManager();
  mgr.bump();
  assert.equal(mgr.current, 'v.0.0.2');
});

test('累计到100仍为 v.0.0.100', () => {
  const mgr = new VersionManager();
  for (let i = 1; i < STEP; i++) mgr.bump(); // 再推 99 次
  assert.equal(mgr.current, 'v.0.0.100');
});

test('满100进位为 v.0.1.0', () => {
  const mgr = new VersionManager();
  for (let i = 0; i < STEP; i++) mgr.bump(); // 从 v.0.0.1 推 100 次
  assert.equal(mgr.current, 'v.0.1.0');
});

test('进位后从 v.0.1.1 继续递增', () => {
  const mgr = new VersionManager();
  for (let i = 0; i < STEP; i++) mgr.bump(); // -> v.0.1.0
  mgr.bump();
  assert.equal(mgr.current, 'v.0.1.1');
});

test('再一组进化为 v.0.2.0 (第二个minor周期需 STEP+1 次)', () => {
  const mgr = new VersionManager();
  for (let i = 0; i < STEP; i++) mgr.bump(); // -> v.0.1.0
  for (let i = 0; i < STEP + 1; i++) mgr.bump(); // -> v.0.2.0
  assert.equal(mgr.current, 'v.0.2.0');
});

test('revision 与 bump 保持一致', () => {
  const mgr = new VersionManager();
  const revisions = [];
  for (let i = 0; i < 250; i++) {
    mgr.bump();
    revisions.push(mgr.revision);
  }
  // 修订计数应严格为 2..251 的连续整数
  assert.deepEqual(revisions, Array.from({ length: 250 }, (_, i) => i + 2));
});

test('versionFromRevision 与 VersionManager 版本一致', () => {
  const mgr = new VersionManager();
  for (let i = 0; i < 350; i++) {
    const expected = mgr.current;
    const tuple = versionFromRevision(mgr.revision);
    assert.equal(formatVersion(tuple), expected, `#${mgr.revision}`);
    mgr.bump();
  }
});

test('parseVersion 校验非法输入', () => {
  assert.throws(() => parseVersion('abc'), Error);
  assert.throws(() => parseVersion('v1.0'), Error);
  assert.deepEqual(parseVersion('v.1.2.3'), [1, 2, 3]);
  assert.deepEqual(parseVersion('0.0.1'), [0, 0, 1]);
});

test('bump 记录 from/to/reason 到日志', () => {
  const mgr = new VersionManager();
  const e = mgr.bump('新增图表功能');
  assert.equal(e.from, 'v.0.0.1');
  assert.equal(e.to, 'v.0.0.2');
  assert.equal(e.reason, '新增图表功能');
  assert.equal(mgr.lastEntry, e);
});