# Changelog

## [2.0.0](https://github.com/audunru/config-secrets/compare/v1.0.0...v2.0.0) (2025-11-08)


### ⚠ BREAKING CHANGES

* laravel 12 support ([#51](https://github.com/audunru/config-secrets/issues/51))
* do not update configuration when package is booted
* new config format ([#9](https://github.com/audunru/config-secrets/issues/9))

### Features

* environment overrides ([9a5a437](https://github.com/audunru/config-secrets/commit/9a5a437a61345b6c91cbe338375e7dd8b0c26e42))
* laravel 11 support ([#12](https://github.com/audunru/config-secrets/issues/12)) ([895f434](https://github.com/audunru/config-secrets/commit/895f434c219eb67f449bfb99b80b29023fc8d02c))
* laravel 12 support ([#51](https://github.com/audunru/config-secrets/issues/51)) ([200a38f](https://github.com/audunru/config-secrets/commit/200a38fba4b949dbb48661412574248abbbc52d2))
* update configuration only when running certain console commands ([#17](https://github.com/audunru/config-secrets/issues/17)) ([83f058d](https://github.com/audunru/config-secrets/commit/83f058d92e3a6b03a9ef21edd38b315e3256bfd9))


### Bug Fixes

* do not update configuration when package is booted ([d73ed2e](https://github.com/audunru/config-secrets/commit/d73ed2e8d8813456b4a8e045ef4528718a36c87d))


### Performance Improvements

* service provider is deferrable ([ce5bd4e](https://github.com/audunru/config-secrets/commit/ce5bd4e8ca145bba6f55e2eb9f062970e205b3d2))


### Reverts

* service provider is deferrable ([c486360](https://github.com/audunru/config-secrets/commit/c486360ad63f1ed2b416a239c7a2fca6da91dd4f))


### Miscellaneous Chores

* dependabot config ([de3c4c9](https://github.com/audunru/config-secrets/commit/de3c4c9b6beee5c0884ffdaad4e7af2a6bcafa4f))
* **main:** release 5.0.0 ([#50](https://github.com/audunru/config-secrets/issues/50)) ([95cac9c](https://github.com/audunru/config-secrets/commit/95cac9c27a751d063a2b5c08b98d93bd3215de94))
* **master:** release 1.1.0 ([#8](https://github.com/audunru/config-secrets/issues/8)) ([fc773f2](https://github.com/audunru/config-secrets/commit/fc773f27d9ac06a2a1c41b0864207d7fa55c2f87))
* **master:** release 1.1.1 ([#11](https://github.com/audunru/config-secrets/issues/11)) ([8d8e87a](https://github.com/audunru/config-secrets/commit/8d8e87a1e899e664b6c0963d4d2f904e8d0e6fca))
* **master:** release 2.0.0 ([#13](https://github.com/audunru/config-secrets/issues/13)) ([f18890d](https://github.com/audunru/config-secrets/commit/f18890d1ec80f0d273d40260fe7dfff6cb315ea1))
* **master:** release 3.0.0 ([#14](https://github.com/audunru/config-secrets/issues/14)) ([158568c](https://github.com/audunru/config-secrets/commit/158568cc067d7209a416dd7800ed4fc5d9737269))
* **master:** release 3.0.1 ([#15](https://github.com/audunru/config-secrets/issues/15)) ([9a9b954](https://github.com/audunru/config-secrets/commit/9a9b95491536a77c732ce36756759a1d173be99a))
* **master:** release 4.0.0 ([#16](https://github.com/audunru/config-secrets/issues/16)) ([107da93](https://github.com/audunru/config-secrets/commit/107da938feb28f22ce9b4abcb188de6cfb979bc7))
* **master:** release 4.1.0 ([#18](https://github.com/audunru/config-secrets/issues/18)) ([0580f37](https://github.com/audunru/config-secrets/commit/0580f375cbe2173bb3a080217c2f2e7e628912f7))
* **master:** release 4.1.1 ([#20](https://github.com/audunru/config-secrets/issues/20)) ([dc3702a](https://github.com/audunru/config-secrets/commit/dc3702af776d5219a32364160ceafed933cb1af7))
* **master:** release 4.1.2 ([#22](https://github.com/audunru/config-secrets/issues/22)) ([a0bdac2](https://github.com/audunru/config-secrets/commit/a0bdac2ff8f0a68bbf8e75d5ac49a6e21e6fcc6f))
* **master:** release 4.1.3 ([#27](https://github.com/audunru/config-secrets/issues/27)) ([e22dabb](https://github.com/audunru/config-secrets/commit/e22dabbf18945e93e813e18f104649f130d332fd))
* parallel php-cs-fixer ([f9497f0](https://github.com/audunru/config-secrets/commit/f9497f0c3557bd65ab3ffd7e958eb9d3ac07236c))
* release 2.0.0 ([0b852b9](https://github.com/audunru/config-secrets/commit/0b852b9b6e4e58f65472aa3ef4341e77d4440ffa))
* rename master branch to main ([db71da1](https://github.com/audunru/config-secrets/commit/db71da199bafa52391e8809a35790544e85ecb4a))
* update dependencies ([b7fdb2f](https://github.com/audunru/config-secrets/commit/b7fdb2f758701fce89e7e765ce59f855cfd5b481))
* update dependencies ([ec42f7b](https://github.com/audunru/config-secrets/commit/ec42f7ba0556558dbd98ea53049fc7a890ebaa69))
* update dependencies ([3fe0162](https://github.com/audunru/config-secrets/commit/3fe0162c48a4a84b4453407d07f2dfabe7500084))


### Code Refactoring

* new config format ([#9](https://github.com/audunru/config-secrets/issues/9)) ([2665926](https://github.com/audunru/config-secrets/commit/26659261bffbfe9cccdeff2527c6206fb5c447e2))

## [5.0.0](https://github.com/audunru/config-secrets/compare/v4.1.3...v5.0.0) (2025-03-16)


### ⚠ BREAKING CHANGES

* laravel 12 support ([#51](https://github.com/audunru/config-secrets/issues/51))

### Features

* laravel 12 support ([#51](https://github.com/audunru/config-secrets/issues/51)) ([9e41488](https://github.com/audunru/config-secrets/commit/9e41488bab09765fcf08970cab5f8d24469a6d6b))


### Miscellaneous Chores

* rename master branch to main ([ed6b53c](https://github.com/audunru/config-secrets/commit/ed6b53cc5f5cab0a2d04f28412229b43b17cde15))

## [4.1.3](https://github.com/audunru/config-secrets/compare/v4.1.2...v4.1.3) (2025-02-01)


### Miscellaneous Chores

* dependabot config ([d003005](https://github.com/audunru/config-secrets/commit/d0030051682c85342cf9996a088916db2bebcbe0))

## [4.1.2](https://github.com/audunru/config-secrets/compare/v4.1.1...v4.1.2) (2024-08-25)


### Miscellaneous Chores

* update dependencies ([0811881](https://github.com/audunru/config-secrets/commit/081188187171ad0802a45428f320195672e8b125))

## [4.1.1](https://github.com/audunru/config-secrets/compare/v4.1.0...v4.1.1) (2024-05-20)


### Miscellaneous Chores

* parallel php-cs-fixer ([9ac0757](https://github.com/audunru/config-secrets/commit/9ac075704eb2efa02cf8a6bf734f1e11a143c123))
* update dependencies ([1848ec4](https://github.com/audunru/config-secrets/commit/1848ec4606c628a5128179f7e5f3bd72f1ad32ea))

## [4.1.0](https://github.com/audunru/config-secrets/compare/v4.0.0...v4.1.0) (2024-05-10)


### Features

* update configuration only when running certain console commands ([#17](https://github.com/audunru/config-secrets/issues/17)) ([450280b](https://github.com/audunru/config-secrets/commit/450280b5e43dded971cc5de64a1cbc0dcc170ad3))

## [4.0.0](https://github.com/audunru/config-secrets/compare/v3.0.1...v4.0.0) (2024-05-08)


### ⚠ BREAKING CHANGES

* do not update configuration when package is booted

### Bug Fixes

* do not update configuration when package is booted ([a04f6a4](https://github.com/audunru/config-secrets/commit/a04f6a4de16e1ace3fa4226e82b4f7372199c184))


### Reverts

* service provider is deferrable ([c2cf2fc](https://github.com/audunru/config-secrets/commit/c2cf2fce2e436ea28c08ec53ffd1c8f4d1031374))

## [3.0.1](https://github.com/audunru/config-secrets/compare/v3.0.0...v3.0.1) (2024-05-05)


### Performance Improvements

* service provider is deferrable ([5c3f297](https://github.com/audunru/config-secrets/commit/5c3f297c81bf25ccec22ae0447c969984487b5ae))

## [3.0.0](https://github.com/audunru/config-secrets/compare/v2.0.0...v3.0.0) (2024-04-07)


### ⚠ BREAKING CHANGES

* new config format ([#9](https://github.com/audunru/config-secrets/issues/9))

### Code Refactoring

* new config format ([#9](https://github.com/audunru/config-secrets/issues/9)) ([cad4ce2](https://github.com/audunru/config-secrets/commit/cad4ce2a2048f651ad5a4280697a22ec889d8fd4))

## [2.0.0](https://github.com/audunru/config-secrets/compare/v1.1.1...v2.0.0) (2024-04-07)


### Features

* laravel 11 support ([#12](https://github.com/audunru/config-secrets/issues/12)) ([218fa33](https://github.com/audunru/config-secrets/commit/218fa3344f920587fc09d166001c819d720fe5e4))


### Miscellaneous Chores

* release 2.0.0 ([f119f11](https://github.com/audunru/config-secrets/commit/f119f110b582170a3f5c22560f91e7b314509647))

## [1.1.1](https://github.com/audunru/config-secrets/compare/v1.1.0...v1.1.1) (2024-04-07)


### Miscellaneous Chores

* update dependencies ([685a3d3](https://github.com/audunru/config-secrets/commit/685a3d35fbf9024479ee31e39bcd0f2e661c31dd))

## [1.1.0](https://github.com/audunru/config-secrets/compare/v1.0.0...v1.1.0) (2023-08-20)


### Features

* environment overrides ([c1d69cb](https://github.com/audunru/config-secrets/commit/c1d69cb76d1071c0d5e11911fd6f131d0ee14722))
