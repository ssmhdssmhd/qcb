const http = require('http');
const url = require('url');
const M3U8AdSkipper = require('./index');

const DEFAULT_PORT = process.env.PORT || 3000;

class M3U8AdSkipperServer {
  constructor(options = {}) {
    this.port = options.port || DEFAULT_PORT;
    this.skipperOptions = options.skipperOptions || {};
    this.server = null;
    this.skipper = null;
  }

  start() {
    this.skipper = new M3U8AdSkipper(this.skipperOptions);
    
    this.server = http.createServer((req, res) => {
      this.handleRequest(req, res);
    });

    return new Promise((resolve, reject) => {
      this.server.listen(this.port, (err) => {
        if (err) {
          reject(err);
        } else {
          console.log(`\n  m3u8-ad-skipper server running`);
          console.log(`  本地访问: http://localhost:${this.port}`);
          console.log(`  网络访问: http://0.0.0.0:${this.port}`);
          console.log(`\n  使用方式:`);
          console.log(`    http://localhost:${this.port}/?url=<m3u8地址>`);
          console.log(`    http://localhost:${this.port}/api/skip?url=<m3u8地址>`);
          console.log(`\n  健康检查:`);
          console.log(`    http://localhost:${this.port}/health`);
          console.log('');
          resolve(this.server);
        }
      });
    });
  }

  stop() {
    return new Promise((resolve, reject) => {
      if (this.server) {
        this.server.close((err) => {
          if (err) reject(err);
          else resolve();
        });
      } else {
        resolve();
      }
    });
  }

  handleRequest(req, res) {
    this.setCorsHeaders(res);

    if (req.method === 'OPTIONS') {
      res.writeHead(204);
      res.end();
      return;
    }

    const parsedUrl = url.parse(req.url, true);
    const pathname = parsedUrl.pathname;

    if (pathname === '/health' || pathname === '/api/health') {
      this.handleHealth(req, res);
      return;
    }

    if (pathname === '/' || pathname === '/api/skip') {
      this.handleSkip(req, res, parsedUrl);
      return;
    }

    this.sendJson(res, 404, {
      error: 'Not Found',
      message: '接口不存在',
      availableEndpoints: [
        { path: '/', method: 'GET', description: '去广告接口（同 /api/skip）' },
        { path: '/api/skip', method: 'GET', description: '去广告接口' },
        { path: '/health', method: 'GET', description: '健康检查' }
      ]
    });
  }

  handleHealth(req, res) {
    this.sendJson(res, 200, {
      status: 'ok',
      service: 'm3u8-ad-skipper',
      version: require('../package.json').version,
      timestamp: new Date().toISOString()
    });
  }

  async handleSkip(req, res, parsedUrl) {
    try {
      const query = parsedUrl.query || {};
      const m3u8Url = query.url;

      if (!m3u8Url) {
        this.sendJson(res, 400, {
          error: 'Bad Request',
          message: '缺少 url 参数',
          example: '/?url=https://example.com/playlist.m3u8'
        });
        return;
      }

      if (!/^https?:\/\//i.test(m3u8Url) && !m3u8Url.startsWith('#')) {
        const fs = require('fs');
        const path = require('path');
        const localPath = path.resolve(m3u8Url);
        if (!fs.existsSync(localPath) && !m3u8Url.includes('\n')) {
          this.sendJson(res, 400, {
            error: 'Bad Request',
            message: '无效的 URL 或文件路径',
            url: m3u8Url
          });
          return;
        }
      }

      const startTime = Date.now();
      const result = await this.skipper.process(m3u8Url);
      const duration = Date.now() - startTime;

      this.sendJson(res, 200, {
        success: true,
        input: m3u8Url,
        processTime: duration + 'ms',
        stats: result.stats,
        playlist: {
          m3u8: result.output,
          format: 'm3u8',
          segmentCount: result.filtered.segments?.length || 0
        },
        removed: result.filtered.removedSegments?.map(s => ({
          uri: s.uri,
          duration: s.duration,
          title: s.title,
          matchedRules: s.adInfo?.matchedRules?.map(r => r.name) || []
        })) || []
      });

    } catch (error) {
      console.error('处理出错:', error.message);
      this.sendJson(res, 500, {
        success: false,
        error: 'Internal Server Error',
        message: error.message,
        stack: process.env.NODE_ENV === 'development' ? error.stack : undefined
      });
    }
  }

  setCorsHeaders(res) {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Accept');
    res.setHeader('Access-Control-Max-Age', '86400');
  }

  sendJson(res, statusCode, data) {
    res.writeHead(statusCode, {
      'Content-Type': 'application/json; charset=utf-8',
      'X-Powered-By': 'm3u8-ad-skipper'
    });
    res.end(JSON.stringify(data, null, 2));
  }
}

if (require.main === module) {
  const port = process.env.PORT || 3000;
  const server = new M3U8AdSkipperServer({ port });
  server.start().catch(err => {
    console.error('启动失败:', err);
    process.exit(1);
  });
}

module.exports = M3U8AdSkipperServer;
