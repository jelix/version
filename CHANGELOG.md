Changelog
=========

1.1.1
-----

Fix compatibility with PHP 8.0 and 8.1

1.1.0
-----

- Fix conformance issue with semver 2.0, on '-alpha.1' vs '-alpha.beta'
- Support of secondaries versions
- Improve the parser: wildcard can be kept in the Version object
- Fix comparison with versions having wildcards

1.0.1
-----

- Fix support of extended version 1.2-3.4
- New serialization function, `VersionComparator::serializeVersion2()`, to support more version syntax

1.0
---

Initial release
