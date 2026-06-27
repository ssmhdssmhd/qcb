const M3U8Parser = require('./parser');
const AdRuleEngine = require('./rules');
const AdFilter = require('./filter');
const OutputGenerator = require('./output');

class M3U8AdSkipper {
  constructor(options = {}) {
    this.options = {
      minSegmentDuration: options.minSegmentDuration || 2,
      maxSegmentDuration: options.maxSegmentDuration || 30,
      adKeywords: options.adKeywords || [
        'ad', 'ads', 'advert', 'advertisement',
        'pre-roll', 'mid-roll', 'post-roll',
        'preroll', 'midroll', 'postroll',
        'commercial', 'promo', 'sponsor',
        '广告', '插播', '贴片', '片头', '片尾'
      ],
      adFilenamePatterns: options.adFilenamePatterns || [
        /ad[s]?[-_]?\d+/i,
        /advert/i,
        /commercial/i,
        /pre[-_]?roll/i,
        /mid[-_]?roll/i,
        /post[-_]?roll/i,
        /sponsor/i,
        /^ad\//i
      ],
      durationTolerance: options.durationTolerance || 0.5,
      ...options
    };

    this.parser = new M3U8Parser();
    this.ruleEngine = new AdRuleEngine(this.options);
    this.filter = new AdFilter(this.ruleEngine);
    this.outputGenerator = new OutputGenerator();
  }

  async process(input, options = {}) {
    const playlist = await this.parser.parse(input);
    const filteredPlaylist = this.filter.filter(playlist);
    const output = this.outputGenerator.generate(filteredPlaylist, options);
    return {
      original: playlist,
      filtered: filteredPlaylist,
      output,
      stats: this.getStats(playlist, filteredPlaylist)
    };
  }

  getStats(original, filtered) {
    const originalSegments = original.segments || [];
    const filteredSegments = filtered.segments || [];
    const removedCount = originalSegments.length - filteredSegments.length;
    
    const originalDuration = originalSegments.reduce((sum, s) => sum + (s.duration || 0), 0);
    const filteredDuration = filteredSegments.reduce((sum, s) => sum + (s.duration || 0), 0);
    
    return {
      totalSegments: originalSegments.length,
      keptSegments: filteredSegments.length,
      removedSegments: removedCount,
      originalDuration: Math.round(originalDuration * 100) / 100,
      filteredDuration: Math.round(filteredDuration * 100) / 100,
      savedDuration: Math.round((originalDuration - filteredDuration) * 100) / 100,
      adPercentage: originalDuration > 0 
        ? Math.round(((originalDuration - filteredDuration) / originalDuration) * 10000) / 100
        : 0
    };
  }
}

module.exports = M3U8AdSkipper;
