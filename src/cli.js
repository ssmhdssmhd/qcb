#!/usr/bin/env node

const M3U8AdSkipper = require('./index');

function parseArgs() {
  const args = process.argv.slice(2);
  const options = {
    input: null,
    output: null,
    format: 'm3u8',
    minDuration: 2,
    maxDuration: 30,
    smart: true,
    showStats: true,
    quiet: false
  };

  let i = 0;
  while (i < args.length) {
    const arg = args[i];
    
    if (arg === '-h' || arg === '--help') {
      printHelp();
      process.exit(0);
    }
    
    if (arg === '-v' || arg === '--version') {
      const pkg = require('../package.json');
      console.log(pkg.version);
      process.exit(0);
    }
    
    if (arg === '-o' || arg === '--output') {
      options.output = args[++i];
    } else if (arg === '-f' || arg === '--format') {
      options.format = args[++i];
    } else if (arg === '--min-duration') {
      options.minDuration = parseFloat(args[++i]);
    } else if (arg === '--max-duration') {
      options.maxDuration = parseFloat(args[++i]);
    } else if (arg === '--no-smart') {
      options.smart = false;
    } else if (arg === '--no-stats') {
      options.showStats = false;
    } else if (arg === '-q' || arg === '--quiet') {
      options.quiet = true;
    } else if (arg === '--json') {
      options.format = 'json';
    } else if (!arg.startsWith('-') && !options.input) {
      options.input = arg;
    }
    
    i++;
  }

  return options;
}

function printHelp() {
  console.log(`
m3u8-ad-skipper - M3U8 播放列表去广告工具

用法:
  m3u8-ad-skipper <input> [options]

参数:
  input                    M3U8 地址或本地文件路径

选项:
  -o, --output <file>      输出文件路径
  -f, --format <format>    输出格式: m3u8, json (默认: m3u8)
      --json               以 JSON 格式输出
      --min-duration <s>   最小片段时长，小于该值视为广告 (默认: 2)
      --max-duration <s>   最大片段时长，大于该值视为广告 (默认: 30)
      --no-smart           禁用智能广告聚类检测
      --no-stats           不显示统计信息
  -q, --quiet              静默模式
  -h, --help               显示帮助
  -v, --version            显示版本号

示例:
  m3u8-ad-skipper https://example.com/playlist.m3u8
  m3u8-ad-skipper input.m3u8 -o output.m3u8
  m3u8-ad-skipper input.m3u8 --json
  m3u8-ad-skipper input.m3u8 --min-duration 3 --max-duration 20
`);
}

async function main() {
  const options = parseArgs();

  if (!options.input) {
    console.error('错误: 请提供输入文件或 URL');
    console.log('使用 -h 查看帮助信息');
    process.exit(1);
  }

  if (!options.quiet) {
    console.log(`\n  m3u8-ad-skipper v${require('../package.json').version}`);
    console.log(`  正在处理: ${options.input}\n`);
  }

  try {
    const skipper = new M3U8AdSkipper({
      minSegmentDuration: options.minDuration,
      maxSegmentDuration: options.maxDuration,
      checkRepetitiveDuration: options.smart
    });

    const result = await skipper.process(options.input);

    let outputContent;
    if (options.format === 'json') {
      outputContent = JSON.stringify({
        input: options.input,
        stats: result.stats,
        playlist: options.output ? undefined : result.output
      }, null, 2);
    } else {
      outputContent = result.output;
    }

    if (options.output) {
      const fs = require('fs');
      fs.writeFileSync(options.output, outputContent, 'utf8');
      if (!options.quiet) {
        console.log(`  输出文件: ${options.output}`);
      }
    } else {
      console.log(outputContent);
    }

    if (options.showStats && !options.quiet) {
      printStats(result.stats);
    }

  } catch (error) {
    console.error(`\n  错误: ${error.message}`);
    if (!options.quiet) {
      console.error(`  堆栈: ${error.stack}`);
    }
    process.exit(1);
  }
}

function printStats(stats) {
  console.log(`
  ┌─────────────────────────────────────┐
  │          处理统计信息               │
  ├─────────────────────────────────────┤
  │  原始片段数:   ${String(stats.totalSegments).padEnd(18)}│
  │  保留片段数:   ${String(stats.keptSegments).padEnd(18)}│
  │  移除片段数:   ${String(stats.removedSegments).padEnd(18)}│
  ├─────────────────────────────────────┤
  │  原始时长:     ${String(stats.originalDuration + 's').padEnd(18)}│
  │  过滤后时长:   ${String(stats.filteredDuration + 's').padEnd(18)}│
  │  节省时长:     ${String(stats.savedDuration + 's').padEnd(18)}│
  │  广告占比:     ${String(stats.adPercentage + '%').padEnd(18)}│
  └─────────────────────────────────────┘
`);
}

main();
