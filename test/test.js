const M3U8AdSkipper = require('../src/index');
const M3U8Parser = require('../src/parser');
const AdRuleEngine = require('../src/rules');
const fs = require('fs');
const path = require('path');

let passed = 0;
let failed = 0;

function test(name, fn) {
  try {
    fn();
    console.log(`  ✅ ${name}`);
    passed++;
  } catch (e) {
    console.log(`  ❌ ${name}`);
    console.log(`     Error: ${e.message}`);
    failed++;
  }
}

function assert(condition, message) {
  if (!condition) {
    throw new Error(message || 'Assertion failed');
  }
}

function assertEqual(actual, expected, message) {
  if (actual !== expected) {
    throw new Error(message || `Expected ${expected}, got ${actual}`);
  }
}

console.log('\n========================================');
console.log('  m3u8-ad-skipper 测试套件');
console.log('========================================\n');

console.log('1. M3U8 解析器测试');
console.log('-------------------');

test('解析带广告的播放列表', () => {
  const parser = new M3U8Parser();
  const content = fs.readFileSync(path.join(__dirname, 'sample_with_ads.m3u8'), 'utf8');
  const playlist = parser.parseContent(content);
  
  assertEqual(playlist.version, 3, '版本号');
  assertEqual(playlist.targetDuration, 10, '目标时长');
  assertEqual(playlist.mediaSequence, 0, '媒体序列号');
  assert(playlist.segments.length > 0, '片段数量大于0');
  assert(playlist.endlist === true, '结束标记');
});

test('解析纯净播放列表', () => {
  const parser = new M3U8Parser();
  const content = fs.readFileSync(path.join(__dirname, 'sample_clean.m3u8'), 'utf8');
  const playlist = parser.parseContent(content);
  
  assertEqual(playlist.segments.length, 10, '10个内容片段');
});

test('解析主播放列表', () => {
  const parser = new M3U8Parser();
  const content = fs.readFileSync(path.join(__dirname, 'sample_master.m3u8'), 'utf8');
  const playlist = parser.parseContent(content);
  
  assert(playlist.isMaster === true, '是主播放列表');
  assertEqual(playlist.variants.length, 4, '4个清晰度');
  assert(playlist.variants[0].bandwidth > 0, '带宽信息');
  assert(playlist.variants[0].resolution, '分辨率信息');
});

test('解析 EXTINF 时长和标题', () => {
  const parser = new M3U8Parser();
  const content = fs.readFileSync(path.join(__dirname, 'sample_with_ads.m3u8'), 'utf8');
  const playlist = parser.parseContent(content);
  
  const firstSegment = playlist.segments[0];
  assertEqual(firstSegment.duration, 5.0, '时长正确');
  assert(firstSegment.title.includes('ad'), '标题包含广告关键词');
  assert(firstSegment.uri, '有URI');
});

console.log('\n2. 广告规则引擎测试');
console.log('-------------------');

test('短时长片段检测', () => {
  const engine = new AdRuleEngine({ minSegmentDuration: 3 });
  const segment = { duration: 2, uri: 'test.ts', title: '' };
  const result = engine.checkSegment(segment, 0, [segment]);
  
  assert(result.isAd === true, '短片段被识别为广告');
  assert(result.matchedRules.some(r => r.name === 'short-duration'), '匹配短时长规则');
});

test('长时长片段检测', () => {
  const engine = new AdRuleEngine({ maxSegmentDuration: 20, checkLongSegments: true });
  const segment = { duration: 25, uri: 'test.ts', title: '' };
  const result = engine.checkSegment(segment, 0, [segment]);
  
  assert(result.isAd === true, '长片段被识别为广告');
});

test('关键词匹配检测', () => {
  const engine = new AdRuleEngine({ adKeywords: ['ad', '广告'] });
  const segment = { duration: 10, uri: 'segment_001.ts', title: 'ad_pre_roll' };
  const result = engine.checkSegment(segment, 0, [segment]);
  
  assert(result.isAd === true, '关键词匹配成功');
});

test('文件名模式匹配检测', () => {
  const engine = new AdRuleEngine({
    adFilenamePatterns: [/^ad_/i]
  });
  const segment = { duration: 10, uri: 'ad_001.ts', title: '' };
  const result = engine.checkSegment(segment, 0, [segment]);
  
  assert(result.isAd === true, '文件名模式匹配成功');
});

test('正常内容不被误判', () => {
  const engine = new AdRuleEngine({
    minSegmentDuration: 2,
    maxSegmentDuration: 30,
    adKeywords: ['ad']
  });
  const segment = { duration: 8, uri: 'content_001.ts', title: 'main content' };
  const result = engine.checkSegment(segment, 0, [segment]);
  
  assert(result.isAd === false, '正常内容不被误判');
});

test('添加自定义规则', () => {
  const engine = new AdRuleEngine();
  let customChecked = false;
  
  engine.addRule({
    name: 'custom-rule',
    description: '自定义规则',
    check: () => {
      customChecked = true;
      return true;
    }
  });
  
  const segment = { duration: 5, uri: 'test.ts' };
  const result = engine.checkSegment(segment, 0, [segment]);
  
  assert(customChecked === true, '自定义规则被执行');
  assert(result.isAd === true, '自定义规则生效');
});

console.log('\n3. 广告过滤测试');
console.log('---------------');

test('过滤带广告的播放列表', async () => {
  const skipper = new M3U8AdSkipper();
  const result = await skipper.process(path.join(__dirname, 'sample_with_ads.m3u8'));
  
  assert(result.stats.totalSegments > result.stats.keptSegments, '移除了部分片段');
  assert(result.stats.removedSegments > 0, '有广告被移除');
  assert(result.stats.savedDuration > 0, '节省了时长');
});

test('纯净播放列表不被修改', async () => {
  const skipper = new M3U8AdSkipper();
  const result = await skipper.process(path.join(__dirname, 'sample_clean.m3u8'));
  
  assertEqual(result.stats.removedSegments, 0, '没有移除任何片段');
  assertEqual(result.stats.adPercentage, 0, '广告占比为0');
});

test('输出是有效的 M3U8 格式', async () => {
  const skipper = new M3U8AdSkipper();
  const result = await skipper.process(path.join(__dirname, 'sample_with_ads.m3u8'));
  
  assert(result.output.startsWith('#EXTM3U'), '以 EXTM3U 开头');
  assert(result.output.includes('#EXTINF:'), '包含 EXTINF 标签');
});

test('主播放列表处理', async () => {
  const skipper = new M3U8AdSkipper();
  const result = await skipper.process(path.join(__dirname, 'sample_master.m3u8'));
  
  assert(result.filtered.isMaster === true, '仍是主播放列表');
  assertEqual(result.filtered.variants.length, 4, '清晰度数量不变');
});

console.log('\n4. 统计信息测试');
console.log('---------------');

test('统计信息准确性', async () => {
  const skipper = new M3U8AdSkipper();
  const result = await skipper.process(path.join(__dirname, 'sample_with_ads.m3u8'));
  const stats = result.stats;
  
  assertEqual(
    stats.totalSegments,
    stats.keptSegments + stats.removedSegments,
    '总数 = 保留 + 移除'
  );
  assert(
    Math.abs(stats.originalDuration - stats.filteredDuration - stats.savedDuration) < 0.01,
    '原始时长 = 过滤后时长 + 节省时长'
  );
  assert(stats.adPercentage >= 0 && stats.adPercentage <= 100, '广告占比在0-100之间');
});

console.log('\n5. 输出生成器测试');
console.log('-----------------');

test('生成 JSON 格式输出', async () => {
  const M3U8AdSkipper = require('../src/index');
  const OutputGenerator = require('../src/output');
  
  const skipper = new M3U8AdSkipper();
  const result = await skipper.process(path.join(__dirname, 'sample_with_ads.m3u8'));
  
  const generator = new OutputGenerator();
  const jsonOutput = generator.generateJson(result.filtered);
  const parsed = JSON.parse(jsonOutput);
  
  assert(parsed.segments !== undefined, '有 segments 字段');
  assert(parsed.removedSegments !== undefined, '有 removedSegments 字段');
});

test('输出到文件', async () => {
  const OutputGenerator = require('../src/output');
  const M3U8Parser = require('../src/parser');
  
  const parser = new M3U8Parser();
  const content = fs.readFileSync(path.join(__dirname, 'sample_clean.m3u8'), 'utf8');
  const playlist = parser.parseContent(content);
  
  const generator = new OutputGenerator();
  const outputPath = path.join(__dirname, 'test_output.m3u8');
  generator.toFile(playlist, outputPath);
  
  assert(fs.existsSync(outputPath), '文件已创建');
  
  const fileContent = fs.readFileSync(outputPath, 'utf8');
  assert(fileContent.startsWith('#EXTM3U'), '文件内容是有效的 M3U8');
  
  fs.unlinkSync(outputPath);
});

console.log('\n========================================');
console.log(`  测试结果: ${passed} 通过, ${failed} 失败`);
console.log('========================================\n');

process.exit(failed > 0 ? 1 : 0);
