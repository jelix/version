Class to compare version

# installation

You can install it from Composer. In your project:

```
composer require "jelix/inifile"
```

# Usage

## Simple comparison


```php
\Jelix\VersionUtils\VersionComparator::compareVersion($v1, $v2);
```

`compareVersion()` returns:

- `-1` if $v1 < $v2
- `0` if $v1 == $v2
- `1` if $v1 > $v2


Example of supported version syntaxes:

- 1.0, 1.2.3
- 1.2a, 1.2alpha, 1.2alpha.3, 1.2a1.3
- 1.2b, 1.2beta, 1.2beta.3, 1.2b3.4
- 1.2RC
- 1.2-dev, 1.2rc, 1.2b1-dev, 1.2b1-dev.9, 1.2RC-dev, 1.2RC2-dev.1700


It supports also version wilcards: 1.*, 1.2.*

Example to compare two versions:

```php
\Jelix\VersionUtils\VersionComparator::compareVersion('1.2pre','1.2RC');
```

To compare two versions with a wildcard:

```php
\Jelix\VersionUtils\VersionComparator::compareVersion('1.1.*','1.1'); // returns 0 
\Jelix\VersionUtils\VersionComparator::compareVersion('1.1.*','1.1.1'); // returns 0
\Jelix\VersionUtils\VersionComparator::compareVersion('1.1.*','1.1.2'); // returns 0
\Jelix\VersionUtils\VersionComparator::compareVersion('1.2.*','1.1.2'); // returns 1
```


## Comparison with range

`compareVersionRange()` allows you to use operator to compare version. 

- `=`, `>`, `<`, `>=`, `<=`, `=`, `!=`
- `~` : specify a range between the version and the next major version

You can combine several constraints with `,` (AND) and '|' (OR).


```php
// check if 0.5 is between 0.8 and 1.0 or if it is higher than 2.0
\Jelix\VersionUtils\VersionComparator::compareVersionRange('0.5','<1.0,>0.8|>2.0');
```


# history

This class has been included for years into the [Jelix Framework](http://jelix.org)
until Jelix 1.6, and has been released in 2016 in a separate repository.

