const https = require('https');
const http = require('http');
const url = require('url');

class M3U8Parser {
  constructor() {
    this.baseUrl = '';
  }

  async parse(input) {
    let content;
    
    if (this.isUrl(input)) {
      content = await this.fetchUrl(input);
      this.baseUrl = this.getBaseUrl(input);
    } else if (input.startsWith('#') || input.includes('#EXTM3U')) {
      content = input;
      this.baseUrl = '';
    } else {
      const fs = require('fs');
      content = fs.readFileSync(input, 'utf8');
      this.baseUrl = '';
    }

    return this.parseContent(content);
  }

  isUrl(str) {
    return /^https?:\/\//i.test(str);
  }

  getBaseUrl(u) {
    const parsed = url.parse(u);
    const pathParts = parsed.pathname.split('/');
    pathParts.pop();
    return `${parsed.protocol}//${parsed.host}${pathParts.join('/')}/`;
  }

  fetchUrl(u) {
    return new Promise((resolve, reject) => {
      const client = u.startsWith('https') ? https : http;
      client.get(u, (res) => {
        if (res.statusCode === 301 || res.statusCode === 302) {
          return this.fetchUrl(res.headers.location).then(resolve).catch(reject);
        }
        if (res.statusCode !== 200) {
          return reject(new Error(`HTTP ${res.statusCode}: ${res.statusMessage}`));
        }
        let data = '';
        res.on('data', (chunk) => data += chunk);
        res.on('end', () => resolve(data));
      }).on('error', reject);
    });
  }

  parseContent(content) {
    const lines = content.split('\n').map(l => l.trim()).filter(l => l.length > 0);
    const playlist = {
      version: 3,
      targetDuration: 0,
      mediaSequence: 0,
      segments: [],
      isMaster: false,
      variants: [],
      raw: content
    };

    let currentSegment = null;
    let i = 0;

    while (i < lines.length) {
      const line = lines[i];

      if (line === '#EXTM3U') {
        i++;
        continue;
      }

      if (line.startsWith('#EXT-X-VERSION:')) {
        playlist.version = parseInt(line.split(':')[1], 10);
        i++;
        continue;
      }

      if (line.startsWith('#EXT-X-TARGETDURATION:')) {
        playlist.targetDuration = parseFloat(line.split(':')[1]);
        i++;
        continue;
      }

      if (line.startsWith('#EXT-X-MEDIA-SEQUENCE:')) {
        playlist.mediaSequence = parseInt(line.split(':')[1], 10);
        i++;
        continue;
      }

      if (line.startsWith('#EXT-X-STREAM-INF:')) {
        playlist.isMaster = true;
        const attrs = this.parseAttributes(line.split(':')[1]);
        i++;
        if (i < lines.length && !lines[i].startsWith('#')) {
          playlist.variants.push({
            uri: lines[i],
            bandwidth: parseInt(attrs.BANDWIDTH, 10) || 0,
            resolution: attrs.RESOLUTION || '',
            codecs: attrs.CODECS || '',
            name: attrs.NAME || ''
          });
        }
        i++;
        continue;
      }

      if (line.startsWith('#EXTINF:')) {
        const parts = line.substring(8).split(',');
        const duration = parseFloat(parts[0]);
        const title = parts.slice(1).join(',').trim();
        currentSegment = {
          duration,
          title,
          uri: '',
          byteRange: null,
          discontinuity: false,
          tags: []
        };
        i++;
        continue;
      }

      if (line.startsWith('#EXT-X-BYTERANGE:')) {
        if (currentSegment) {
          const range = line.substring(17);
          const parts = range.split('@');
          currentSegment.byteRange = {
            length: parseInt(parts[0], 10),
            offset: parts[1] ? parseInt(parts[1], 10) : 0
          };
        }
        i++;
        continue;
      }

      if (line.startsWith('#EXT-X-DISCONTINUITY')) {
        if (currentSegment) {
          currentSegment.discontinuity = true;
        }
        i++;
        continue;
      }

      if (line.startsWith('#EXT-X-ENDLIST')) {
        playlist.endlist = true;
        i++;
        continue;
      }

      if (line.startsWith('#')) {
        if (currentSegment) {
          currentSegment.tags.push(line);
        }
        i++;
        continue;
      }

      if (currentSegment && !line.startsWith('#')) {
        currentSegment.uri = line;
        currentSegment.absoluteUri = this.resolveUri(line);
        playlist.segments.push(currentSegment);
        currentSegment = null;
        i++;
        continue;
      }

      i++;
    }

    return playlist;
  }

  parseAttributes(attrString) {
    const attrs = {};
    const regex = /([A-Z0-9-]+)=("[^"]*"|[^,]+)/g;
    let match;
    while ((match = regex.exec(attrString)) !== null) {
      let value = match[2];
      if (value.startsWith('"') && value.endsWith('"')) {
        value = value.slice(1, -1);
      }
      attrs[match[1]] = value;
    }
    return attrs;
  }

  resolveUri(uri) {
    if (!this.baseUrl || /^https?:\/\//i.test(uri)) {
      return uri;
    }
    if (uri.startsWith('/')) {
      const parsed = url.parse(this.baseUrl);
      return `${parsed.protocol}//${parsed.host}${uri}`;
    }
    return this.baseUrl + uri;
  }
}

module.exports = M3U8Parser;
