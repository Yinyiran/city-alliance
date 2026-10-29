import { describe, it, expect, beforeEach } from 'vitest';
import { StoreService } from './store.service';

describe('StoreService - Spatial GIS & WeCom Logic', () => {
  let service: StoreService;

  beforeEach(() => {
    // 实例化，DB 依赖在纯算法测距测试中暂不需要传入
    service = new StoreService({} as any);
  });

  describe('Haversine Distance Calculation', () => {
    it('should calculate distance between two known coordinates accurately', () => {
      // 杭州西湖 (30.2458, 120.1438) 到 武林广场 (30.2741, 120.1619) 约 3.5 公里
      const dist = service.calculateDistanceMeters(
        30.2458,
        120.1438,
        30.2741,
        120.1619,
      );
      expect(dist).toBeGreaterThan(3200);
      expect(dist).toBeLessThan(3800);
    });

    it('should return 0 meters for identical coordinates', () => {
      const dist = service.calculateDistanceMeters(
        31.2304,
        121.4737,
        31.2304,
        121.4737,
      );
      expect(dist).toBe(0);
    });
  });
});
