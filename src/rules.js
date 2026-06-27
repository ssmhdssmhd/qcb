class AdRuleEngine {
  constructor(options = {}) {
    this.options = {
      minSegmentDuration: options.minSegmentDuration || 2,
      maxSegmentDuration: options.maxSegmentDuration || 30,
      adKeywords: options.adKeywords || [],
      adFilenamePatterns: options.adFilenamePatterns || [],
      durationTolerance: options.durationTolerance || 0.5,
      checkShortSegments: options.checkShortSegments !== false,
      checkLongSegments: options.checkLongSegments === true,
      checkKeywords: options.checkKeywords !== false,
      checkFilenamePatterns: options.checkFilenamePatterns !== false,
      checkDiscontinuity: options.checkDiscontinuity === true,
      checkRepetitiveDuration: options.checkRepetitiveDuration === true,
      ...options
    };

    this.rules = [];
    this.initRules();
  }

  initRules() {
    if (this.options.checkShortSegments) {
      this.rules.push({
        name: 'short-duration',
        description: '片段时长过短，可能是广告',
        check: (segment, index, segments) => {
          return segment.duration < this.options.minSegmentDuration;
        }
      });
    }

    if (this.options.checkLongSegments) {
      this.rules.push({
        name: 'long-duration',
        description: '片段时长过长，可能是广告',
        check: (segment, index, segments) => {
          return segment.duration > this.options.maxSegmentDuration;
        }
      });
    }

    if (this.options.checkKeywords && this.options.adKeywords.length > 0) {
      this.rules.push({
        name: 'keyword-match',
        description: '标题或文件名包含广告关键词',
        check: (segment) => {
          const text = `${segment.title || ''} ${segment.uri || ''}`.toLowerCase();
          return this.options.adKeywords.some(kw => 
            text.includes(kw.toLowerCase())
          );
        }
      });
    }

    if (this.options.checkFilenamePatterns && this.options.adFilenamePatterns.length > 0) {
      this.rules.push({
        name: 'filename-pattern',
        description: '文件名匹配广告命名模式',
        check: (segment) => {
          const uri = segment.uri || '';
          const filename = uri.split('/').pop() || uri;
          return this.options.adFilenamePatterns.some(pattern => 
            pattern.test(filename) || pattern.test(uri)
          );
        }
      });
    }

    if (this.options.checkDiscontinuity) {
      this.rules.push({
        name: 'discontinuity',
        description: '存在不连续标记，可能是插播广告',
        check: (segment, index, segments) => {
          return segment.discontinuity === true;
        }
      });
    }

    if (this.options.checkRepetitiveDuration) {
      this.rules.push({
        name: 'repetitive-duration',
        description: '重复出现完全相同时长的片段，可能是广告',
        check: (segment, index, segments) => {
          if (segments.length < 10) return false;
          
          const duration = segment.duration;
          let exactCount = 0;
          for (const s of segments) {
            if (Math.abs(s.duration - duration) < 0.001) {
              exactCount++;
            }
          }
          
          const isShortAd = duration >= 2 && duration <= 6;
          
          return exactCount >= 4 && exactCount > segments.length * 0.5 && isShortAd;
        }
      });
    }
  }

  checkSegment(segment, index, segments) {
    const matchedRules = [];
    
    for (const rule of this.rules) {
      try {
        if (rule.check(segment, index, segments)) {
          matchedRules.push({
            name: rule.name,
            description: rule.description
          });
        }
      } catch (e) {
        // 忽略规则检查错误
      }
    }

    return {
      isAd: matchedRules.length > 0,
      matchedRules
    };
  }

  checkAllSegments(segments) {
    return segments.map((segment, index) => ({
      segment,
      index,
      ...this.checkSegment(segment, index, segments)
    }));
  }

  addRule(rule) {
    if (rule.name && rule.check && typeof rule.check === 'function') {
      this.rules.push(rule);
    }
  }

  removeRule(ruleName) {
    this.rules = this.rules.filter(r => r.name !== ruleName);
  }

  getRules() {
    return this.rules.map(r => ({
      name: r.name,
      description: r.description
    }));
  }
}

module.exports = AdRuleEngine;
