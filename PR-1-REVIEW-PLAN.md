# Plán implementácie – PR #1 (code review)

PR: [#1 Add Symfony 8 support and rename to pixelfederation/event-store-symfony-bundle](https://github.com/pixelfederation/event-store-symfony-bundle/pull/1)
Reviewer: `majoskorec` — 9 komentárov, stav `CHANGES_REQUESTED`.

---

## 1. CI matica – doplniť PHP 8.5

**Komentáre:** `.github/workflows/tests.yml:16`, `.github/workflows/static-analyse.yml:13` → „aj 8.5"

- [tests.yml:15-16](.github/workflows/tests.yml#L15-L16) a [static-analyse.yml:12-13](.github/workflows/static-analyse.yml#L12-L13): do `php-version` matice pridať `"8.5"`.
- `composer.json` má `"php": "^8.4"`, čo 8.5 pokrýva — netreba meniť.
- V matici testov tak vzniknú 4 kombinácie (7.4/8.1 × 8.4/8.5). Ak by Symfony 7.4 nešlo na PHP 8.5, riešiť `exclude`, nie downgrade.
- README „Requirements" nechať ako „PHP 8.4 or higher".

## 2. Vrátiť bump copyright rokov 2024 → 2026

**Komentár:** `src/Command/AbstractProjectionCommand.php:5` → „tieto veci neviem či by som menil, AI ti to vyslovene odporučila?"

Reviewer má pravdu — bump rokov v hlavičkách je šum, ktorý nafukuje diff (67 riadkov v 60+ súboroch) a navyše pripisuje pôvodným autorom roky, kedy už na projekte nerobili.

- Revert `2014-2026`/`2015-2026` späť na `2014-2024`/`2015-2024` naprieč `src/` aj `test/` (`sed` cez zoznam z `git diff --stat`).
- Overiť, že `php-cs-fixer` header rule si to nezmení naspäť — ak `prooph/php-cs-fixer-config` rok generuje, treba fixnúť konfiguráciu, nie súbory.

## 3. Hlavička v novom `event_store.php`

**Komentár:** `src/Resources/config/event_store.php:5` → „toto sem nedával... nie je to pravda"

Nový súbor nesie hlavičku tvrdiacu, že ho v 2014 napísal Alexander Miertsch. To nesedí.

- V [src/Resources/config/event_store.php](src/Resources/config/event_store.php) nahradiť hlavičku licenčným blokom bez pripísania autorstva pôvodným autorom (ponechať BSD-3-Clause odkaz na `LICENSE`), prípadne uviesť PixelFederation.
- Pozor: ak `php-cs-fixer` header_comment vynucuje presný tvar, treba súbor buď zosúladiť s konfiguráciou, alebo konfiguráciu upraviť — inak spadne `composer cs`.

## 4. `composer.json` – autorstvo forku

**Komentár:** `composer.json:12` → „zisti u AI ako sa zvykne zapisovať toto keď si to forkneš a začínaš to udržiavať ty"

Bežná konvencia pre udržiavaný fork: pôvodných autorov ponechať s `"role": "Original author"`, seba/organizáciu pridať ako `"role": "Maintainer"`.

- Pridať do `authors` položku PixelFederation (`name`, `email`/`homepage`, `role: Maintainer`), ostatným doplniť `role: Original author`.
- `homepage`: `http://getprooph.org/` → `https://github.com/pixelfederation/event-store-symfony-bundle`.
- `replace: {"prooph/event-store-symfony-bundle": "*"}` ponechať — to je správny fork idiom; zvážiť len upresnenie na `"^0.5"` namiesto `"*"`, aby fork nezablokoval prípadný budúci upstream release.
- `support.issues`/`source` už na fork ukazujú — OK.

## 5. `composer.json` – verzie závislostí

**Komentáre:** `composer.json:73` → „1.11 nemusíš podporovať"; `composer.json:68` → „toto kľudne updatni na najnovšiu verziu"

- `phpstan/phpstan`: `"^1.11 || ^2.0"` → `"^2.0"` (aktuálne 2.2.x). Zjednoduší to aj bod 6.
- `phpunit/phpunit`: `"^11.5"` → najnovšie (13.3.x). **Toto je väčší kus práce než jednoriadková zmena:** PHPUnit 12 a 13 priniesli breaking changes; testy už používajú atribúty (`#[Test]`), čo pomáha, ale treba prejsť `phpunit.xml.dist` schému a deprecated API (`createMock` na finals, data providery musia byť statické, atď.). Plán: bumpnúť, spustiť `composer test`, opraviť čo padne, migrovať konfig cez `phpunit --migrate-configuration`.
- Pri príležitosti: `prooph/pdo-event-store` `^1.12` (najnovšie 1.16.5 — constraint už pokrýva), `matthiasnoback/symfony-dependency-injection-test` `^6.3` je najnovšia, `friendsofphp/php-cs-fixer` `^3.5` → `^3.95` pre istotu.

## 6. Pročistiť `phpstan.neon` ignore

**Komentár:** `phpstan.neon:22` → „pokús sa prečistiť tie ignore nad tým... väčšina sa bude môcť odmazať"

Overené experimentálne (PHPStan level 6 spustený len s `missingType.*` ignormi): **všetkých 8 regexových ignorov na [phpstan.neon:4-20](phpstan.neon#L4-L20) je mŕtvych a dá sa zmazať.** Zostane 7 reálnych chýb, ktoré sa dajú opraviť v kóde namiesto ignorovania:

| Chyba | Miesto | Oprava |
|---|---|---|
| 4× `ternary.alwaysFalse` | [AbstractProjectionCommand.php:87,92,99,113](src/Command/AbstractProjectionCommand.php#L87) | `$projectionName` je typované `string`, takže `\is_array($this->projectionName) ? ... : [...]` je mŕtvy kód. Nahradiť `vsprintf(..., [$this->projectionName])` alebo rovno `sprintf()`; pri `$input->getArgument()` doplniť `(string)` cast. |
| `varTag.nativeType` | [ProjectionStateCommand.php:34](src/Command/ProjectionStateCommand.php#L34) | `json_encode()` vracia `string\|false`. Použiť `\JSON_THROW_ON_ERROR` a `/** @var string */` zmazať. |
| `varTag.nativeType` | [ProjectionOptionsPass.php:67](src/DependencyInjection/Compiler/ProjectionOptionsPass.php#L67) | `Definition::getClass(): ?string`, nie `object`. Null-check + `/** @var class-string */`. |
| `varTag.nativeType` | [RegisterProjectionsPass.php:44](src/DependencyInjection/Compiler/RegisterProjectionsPass.php#L44) | to isté |

Výsledný `phpstan.neon` má ostať len s `missingType.iterableValue` / `missingType.generics` a `reportUnmatchedIgnoredErrors: true` (nie `false`), aby mŕtve ignory už nezhnili.

## 7. PHP ekvivalent zmazaných XML fixtúr

**Komentár:** `test/DependencyInjection/Fixture/config/xml/event_store_multiple.xml:8` → „tieto xml by nemali mať php ekvivalent?"

PR zmazal 10 XML fixtúr + `XmlEventStoreExtensionTest` (−227 riadkov) bez náhrady, takže ostal pokrytý len YAML loader. Konfigurácia bundlu v XML na Symfony 8 skutočne nie je možná (zmizol `getNamespace()`), ale PHP formát áno — a ten pokrytý nie je.

- Vytvoriť `test/DependencyInjection/Fixture/config/php/` s PHP ekvivalentmi všetkých 10 fixtúr (`event_store.php`, `event_store_multiple.php`, `event_store_with_@.php`, `metadata_enricher.php`, `metadata_enricher_global.php`, `missing_projection_key.php`, `plugins.php`, `plugins_global.php`, `projections.php`, `unconfigured.php`) vo forme `return static function (ContainerConfigurator $c) { $c->extension('prooph_event_store', [...]); }`.
- Pridať `PhpEventStoreExtensionTest extends AbstractEventStoreExtensionTestCase` s `PhpFileLoader` — analogicky k [YamlEventStoreExtensionTest.php](test/DependencyInjection/YamlEventStoreExtensionTest.php). Celá testovacia logika sa zdedí, takže ide o ~20 riadkov testu + fixtúry.
- Do CHANGELOG-u pod „Removed" doplniť, že konfigurácia bundlu v XML už nie je podporovaná a používatelia majú prejsť na YAML/PHP.

---

## Poradie prác

1. Body 1–4 (triviálne, nezávislé) → jeden commit „review fixes: CI matrix, headers, authorship".
2. Bod 6 (PHPStan cleanup + 7 opráv v kóde) → samostatný commit, ľahko reviewovateľný.
3. Bod 5 (bump PHPUnit) → samostatný commit, lebo môže vyvolať reťazec opráv v testoch.
4. Bod 7 (PHP fixtúry + test) → samostatný commit, ideálne až po bode 5, aby sa nový test nepísal dvakrát.

Na záver `composer check` + `composer phpstan` lokálne a odpovedať na jednotlivé review vlákna.
