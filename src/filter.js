class AdFilter {
  constructor(ruleEngine) {
    this.ruleEngine = ruleEngine;
  }

  filter(playlist) {
    if (playlist.isMaster) {
      return this.filterMasterPlaylist(playlist);
    }
    return this.filterMediaPlaylist(playlist);
  }

  filterMediaPlaylist(playlist) {
    const segments = playlist.segments || [];
    const results = this.ruleEngine.checkAllSegments(segments);
    
    const keptSegments = [];
    const removedSegments = [];
    
    results.forEach((result) => {
      if (result.isAd) {
        removedSegments.push({
          ...result.segment,
          adInfo: {
            matchedRules: result.matchedRules
          }
        });
      } else {
        keptSegments.push(result.segment);
      }
    });

    const adjustedSegments = this.adjustSequenceNumbers(keptSegments);

    return {
      ...playlist,
      segments: adjustedSegments,
      mediaSequence: 0,
      removedSegments,
      adDetected: removedSegments.length > 0
    };
  }

  filterMasterPlaylist(playlist) {
    return {
      ...playlist,
      variants: playlist.variants.map(v => ({
        ...v,
        uri: this.buildProxyUri(v.uri)
      }))
    };
  }

  adjustSequenceNumbers(segments) {
    return segments.map((segment, index) => ({
      ...segment,
      sequence: index
    }));
  }

  buildProxyUri(originalUri) {
    return originalUri;
  }

  smartFilter(playlist) {
    const segments = playlist.segments || [];
    if (segments.length === 0) return playlist;

    const results = this.ruleEngine.checkAllSegments(segments);
    
    const adIndices = results
      .map((r, i) => r.isAd ? i : -1)
      .filter(i => i >= 0);

    if (adIndices.length === 0) {
      return {
        ...playlist,
        removedSegments: [],
        adDetected: false
      };
    }

    const adClusters = this.findAdClusters(adIndices, segments.length);
    
    const validClusters = adClusters.filter(cluster => 
      this.isLikelyAdCluster(cluster, segments)
    );

    const validAdIndices = new Set();
    validClusters.forEach(cluster => {
      for (let i = cluster.start; i <= cluster.end; i++) {
        validAdIndices.add(i);
      }
    });

    const keptSegments = [];
    const removedSegments = [];

    segments.forEach((segment, index) => {
      if (validAdIndices.has(index)) {
        const result = results[index];
        removedSegments.push({
          ...segment,
          adInfo: {
            matchedRules: result.matchedRules,
            cluster: validClusters.findIndex(c => 
              index >= c.start && index <= c.end
            )
          }
        });
      } else {
        keptSegments.push(segment);
      }
    });

    return {
      ...playlist,
      segments: this.adjustSequenceNumbers(keptSegments),
      mediaSequence: 0,
      removedSegments,
      adDetected: removedSegments.length > 0,
      adClusters: validClusters
    };
  }

  findAdClusters(adIndices, totalSegments) {
    if (adIndices.length === 0) return [];

    const clusters = [];
    let currentCluster = {
      start: adIndices[0],
      end: adIndices[0],
      count: 1
    };

    for (let i = 1; i < adIndices.length; i++) {
      if (adIndices[i] - adIndices[i - 1] <= 2) {
        currentCluster.end = adIndices[i];
        currentCluster.count++;
      } else {
        clusters.push(currentCluster);
        currentCluster = {
          start: adIndices[i],
          end: adIndices[i],
          count: 1
        };
      }
    }
    clusters.push(currentCluster);

    return clusters;
  }

  isLikelyAdCluster(cluster, segments) {
    if (cluster.count < 2) return false;

    const clusterSegments = segments.slice(cluster.start, cluster.end + 1);
    const totalDuration = clusterSegments.reduce((sum, s) => sum + s.duration, 0);
    
    const durations = clusterSegments.map(s => s.duration);
    const avgDuration = totalDuration / clusterSegments.length;
    const variance = durations.reduce((sum, d) => sum + Math.pow(d - avgDuration, 2), 0) / durations.length;
    
    const isUniformDuration = Math.sqrt(variance) < avgDuration * 0.2;

    const isAtStart = cluster.start <= 2;
    const isAtEnd = cluster.end >= segments.length - 3;

    const hasDiscontinuity = clusterSegments.some(s => s.discontinuity);

    return (
      cluster.count >= 3 ||
      (isAtStart && totalDuration >= 5) ||
      (isAtEnd && totalDuration >= 5) ||
      (hasDiscontinuity && cluster.count >= 2) ||
      (isUniformDuration && cluster.count >= 4)
    );
  }
}

module.exports = AdFilter;
