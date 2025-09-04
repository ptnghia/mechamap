## Translation Refactor Progress

Goal: Implement manifest-based, versioned, cache‑efficient multilingual architecture for server + JS client.

### Legend
- [x] Done  - [~] Partial  - [ ] Pending  - [!] Attention / risk

### Phase 1 (Foundations)
- [x] Config file `config/translation.php`
- [x] Version / manifest service (`TranslationVersionService`)
- [x] Model hooks bump version (save/delete)
- [x] Manifest endpoint `/api/translations/manifest`
- [x] Group endpoint `/api/translations/group`
- [x] Existing endpoints backward compatible (now include `version`)
- [x] Client: localStorage persistence + manifest consumption (initial skeleton)
- [x] Remove duplicate `isLoaded` + add flat index

### Phase 2 (Enhancements)
- [x] Conditional requests (hash compare skip) for group endpoint
- [x] Fallback locale basic chain (separate fallbackFlat indexing)
- [x] Pluralization (Intl.PluralRules simple variant mapping)
- [x] Metrics & instrumentation (basic counters + debug output)
- [x] Service Worker caching for group & manifest (stale-while-revalidate)
- [x] Precomputed immutable hashed group files (CDN ready)  (export-static command)
- [x] Multi-group delta endpoint (POST /api/translations/delta)

### Phase 3 (Quality & Tooling)
- [ ] Artisan commands for warming & manifest rebuild
- [x] Artisan commands added: translations:export-static, translations:warm
- [ ] Manifest explicit rebuild command (optional if bump logic insufficient)
- [ ] Admin UI controls (show version / force bump)
- [x] Key conflict detection (translations:detect-conflicts)
- [x] HTML escaping helper + raw variant (trans_e / trans_raw)
- [ ] E2E cache invalidation tests

### Current Risks / Notes
- Group hash derived from DB each cache cycle; may add event-driven precompute later.
- Client still fetches group even if hash equal (optimize in Phase 2).

### Next Immediate Actions
1. Admin UI: surface current locale version + force bump button (calls /api/translations/force-bump)
2. Add explicit manifest rebuild command (translations:rebuild-manifest) if needed
3. E2E tests for: delta unchanged path, fallback locale resolution, plural category selection
4. Optional: integrate static file serving heuristic (serve /translations/{locale}/{group}.{hash}.json if exists before DB fetch)

### Changelog
2025-09-04: Phase 1 implementation committed.

Maintainer: (update name)
