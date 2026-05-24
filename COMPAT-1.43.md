# ArtRobNugenComPermalinks — MediaWiki 1.43 compat note

Written 2026-05-24 as part of Phase 2.6 prep for the 1.39 → 1.43 jump on
wiki.robnugen.com. Playwright already confirms the extension renders correctly
under 1.43 on the rehearsal install.

## Verdict

**No code changes required for 1.43.** The extension is already on the modern
hook surface and does not touch any MediaWiki API that shifted between 1.39 and
1.43.

## What was checked

### Hook surface — modern

- `extension.json` uses `manifest_version: 2`, `AutoloadNamespaces`, and a
  `HookHandlers` block.
- The class implements the typed `\MediaWiki\Hook\ParserFirstCallInitHook`
  interface (introduced 1.35, still the recommended form in 1.43).
- Sole runtime hook: `<permalink>` tag registered via `$parser->setHook()` —
  stable Parser API, unchanged through 1.43.

### Art-DB connection — does NOT use MediaWiki's DB layer

`includes/ArtRobNugenComPermalinks.php` `require_once`s two files from
`/home/robuwikipix/art.robnugen.com/includes/` (`mysql.php`, `lilurl.php`) and
calls `$lilurl->get_id()`. All SQL happens inside that external lilurl code.

**Implication:** the LoadBalancer / `IDatabase` API reshuffle in 1.40+ is
irrelevant here — MediaWiki's DB stack is never in the call chain. The art-DB
link survives the upgrade because it never went through MediaWiki to begin with.

Sole external dependency: PHP `short_open_tag = On`, so `mysql.php`'s `<?`
opener parses. Confirmed on for DH php-8.2 and php-8.3 (see
`project_upgrade.md` notes on the Phase 2.5 rehearsal).

### Deprecated globals — none in scope

- `$wgRequest` was already replaced with `RequestContext::getMain()->getRequest()`
  in commit `567d814` (during the PHP 8.1 work).
- No other `$wg*` globals are read or written by the extension.
- No `User::`/`$wgUser` use, no direct `OutputPage` use, no `LinkRenderer`
  dependencies — nothing on the 1.40–1.43 deprecation lists touches this code.

## Cross-cutting tech debt (NOT a 1.43 blocker)

The hardcoded `require_once '/home/robuwikipix/art.robnugen.com/includes/...'`
reaches across codebases and carries plaintext DB creds. That's pre-existing
debt and is tracked separately as mg #216 (decouple ArtRobNugenCom from
`~/art.robnugen.com/includes/`), planned for its own merge bubble after 1.43
ships. The 1.43 upgrade does not need to touch it.

## `requires` field

`extension.json` declares `MediaWiki >= 1.35.0`. Accurate and conservative —
leave as-is. No reason to bump just because 1.43 is tested.
