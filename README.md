This PHP library parse any version syntax, including semantic version. It allows
also to compare versions, using Composer version constraints syntax.

# installation

You can install it from Composer. In your project:

```
composer require "jelix/version"
```

# Usage

## Parsing

Use the `Jelix\Version\Parser` class to retrieve a `Jelix\Version\Version` object
containing all versions informations.

```php
$version = Jelix\Version\Parser::parse('1.2.3b2');

$version->toString(); // '1.2.3-beta.2'
$version->getMajor(); // 1
$version->getMinor(); // 2
$version->getPatch(); // 3
$version->getStabilityVersion(); // array('beta', '2')

$version->getNextMajorVersion(); // '2.0.0'
$version->getNextMinorVersion(); // '1.3.0'
$version->getNextPatchVersion(); // '1.2.4'
$version->getBranchVersion(); // '1.2'
```

Example of supported version syntaxes:

- 1.0, 1.2.3
- 1.2-alpha, 1.2a, 1.2alpha, 1.2alpha.3, 1.2a1.3
- 1.2.3-beta, 1.2b, 1.2beta, 1.2beta.3, 1.2b3.4
- 1.2RC, 1.2-rc.4.5
- 1.2-dev, 1.2b1-dev, 1.2b1-dev.9, 1.2RC-dev, 1.2RC2-dev.1700


## Simple comparison


```php
\Jelix\Version\VersionComparator::compareVersion($v1, $v2);
```

`compareVersion()` returns:

- `-1` if $v1 < $v2
- `0` if $v1 == $v2
- `1` if $v1 > $v2


Example to compare two versions:

```php
\Jelix\Version\VersionComparator::compareVersion('1.2pre','1.2RC');
```

## Comparison with range

`compareVersionRange()` allows you to use operator to compare version. It
is compatible with version constraints supported by Composer.

- `>`, `<`, `>=`, `<=`, `=`, `!=`
- no operator means `=`
- `~` : specify a range between the version and the next major version
   (excluding the next major version itself and its unstable variant)
   - `~1.2` is equivalent to `>=1.2 <2.0.0-dev`
   - `~1.2.3` is equivalent to `>=1.2.3 <1.3.0-dev`
- '^' : specify a range between the version and the next minor version
   - `^1.2.3` is equivalent to `>=1.2.3 <1.3.0`
   - `^0.3` is equivalent to `>=0.3.0 <0.4.0`
- hyphen separator to specify a range : '1.2 - 1.5'
   - `1.0 - 2.0` is equivalent to `>=1.0.0 <2.1` (as 2.0 is interpreted as 2.0.*)
   - `1.0.0 - 2.1.0` is equivalent to `>=1.0.0 <=2.1.0`
- It supports also version wilcards with or without operators: `1.*`, `1.2.*` 


You can combine several constraints with boolean operators :

- AND operator: `,` or ` `
- OR operator: `||` or `|`.


```php
// check if 0.5 is between 0.8 and 1.0 or if it is higher than 2.0
\Jelix\Version\VersionComparator::compareVersionRange('0.5','<1.0,>0.8|>2.0');

// check if 0.5 is between 0.8 and 1.0
\Jelix\Version\VersionComparator::compareVersionRange('0.5','0.8 - 1.0');

// with a wildcard
\Jelix\Version\VersionComparator::compareVersionRange('1.1', '1.1.*'); // returns true
\Jelix\Version\VersionComparator::compareVersionRange('1.1.2', '1.2.*'); // returns false
\Jelix\Version\VersionComparator::compareVersionRange('1.3.0', '>=1.2.*'); // returns true
```


# History

Ancester of these classes has been included for years into the [Jelix Framework](http://jelix.org)
until Jelix 1.6, and has been released in 2016 into a separate repository.

