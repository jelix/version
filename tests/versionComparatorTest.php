<?php
/**
* @author      Laurent Jouanneau
* @contributor
* @copyright   2009-2022 Laurent Jouanneau
* @link        http://jelix.org
* @licence     MIT
* @since 1.2
*/

use Jelix\Version\VersionComparator;
use Jelix\Version\Parser;
use Jelix\Version\Version;

class VersionComparatorForTests extends VersionComparator
{
    public static function compileRangeForTest($range)
    {
        return self::compileRange($range);
    }

    public static function getBoundsFromWildcardVersionForTest(Version $version)
    {
        return self::getBoundsFromWildcardVersion($version);
    }
}


class versionComparatorTest extends \PHPUnit\Framework\TestCase {

    public function getWildcardRangeList()
    {
        return array(
                // wildcard version, range result
            array('1.*',        '(>=1.0.0-dev) AND (<2.0.0-dev)'),
            array('1.4.*',        '(>=1.4.0-dev) AND (<1.5.0-dev)'),
            array('1.4.*-stable', '(>=1.4.0) AND (<1.5.0)'),
            array('1.2.3.*', '(>=1.2.3.0-dev) AND (<1.2.4-dev)'),
            array('1.7.0-pre.*', '(>=1.7.0-pre.0) AND (<1.7.0-alpha)'),
            array('1.2.0-*', '(>=1.2.0-dev) AND (<1.2.1-dev)'),
        );
    }

    /**
     * @dataProvider getWildcardRangeList
     */
    public function testWildcardRange($wildcardVersion, $rangeResult)
    {
        $v1 = Parser::parse($wildcardVersion);
        $this->assertEquals($rangeResult, (string) VersionComparator::getRangeFromWildcardVersion($v1));
    }


    public function getCompareVersion() {
        return array(
            // 0 = equals
            // -1 : v1 < v2
            // 1 : v1 > v2

            // rules to check precedence as defined by semver 2.0.0
            array(-1, '1.0.0-alpha', '1.0.0-alpha.1'),
            array(-1, '1.0.0-alpha.1', '1.0.0-alpha.beta'),
            array(-1, '1.0.0-alpha.beta', '1.0.0-beta'),
            array(-1, '1.0.0-beta', '1.0.0-beta.2'),
            array(-1, '1.0.0-beta.2', '1.0.0-beta.11'),
            array(-1, '1.0.0-beta.11', '1.0.0-rc.1'),
            array(-1, '1.0.0-rc.1 ', '1.0.0'),

            // other rules
            array(0,'1.0','1.0'),
            array(1, '1.1','1.0'),
            array(-1, '1.0','1.1'),
            array(-1, '1.1','1.1.1'),
            array(1, '1.1.2','1.1'),
            array(1, '1.2','1.2b'),
            array(1, '1.2','1.2a'),
            array(1, '1.2','1.2RC'),
            array(1, '1.2','1.2bETA'),
            array(1, '1.2','1.2alpha'),
            array(1, '1.2','1.2pre'),
            array(0, '1.2-dev','1.2dev'),
            array(0, '1.2-dev','1.2pre'),
            array(0, '1.2-dev','1.2-pre'),
            array(0, '1.2dev','1.2pre'),
            array(0, '1.2dev','1.2-pre'),
            array(-1, '1.2-dev','1.2-alpha'),
            array(-1, '1.2-dev','1.2-beta'),
            array(-1, '1.2-dev','1.2-stable'),
            array(-1, '1.2.0-dev','1.2.0-stable'),
            array(-1, '1.2.0-dev','1.2.0'),
            array(-1, '1.2b','1.2'),
            array(-1, '1.2a','1.2'),
            array(-1, '1.2RC','1.2'),
            array(-1, '1.2bEta','1.2'),
            array(-1, '1.2alpha','1.2'),
            array(-1, '1.2b1','1.2b2'),
            array(-1, '1.2B1','1.2b2'),
            array(1, '1.2b2','1.2b1'),
            array(1, '1.2b2','1.2b2-dev'),
            array(-1, '1.2b2-dev','1.2b2'),
            array(-1, '1.2b2-dev.2324','1.2b2'),
            array(0, '1.2b2pre','1.2b2-dev'),
            array(1, '1.2b2pre.4','1.2b2-dev'),
            array(-1, '1.2b2pre.4','1.2b2-dev.9'),
            array(-1, '1.2b2pre','1.2b2-dev.9'),
            array(-1, '1.2RC1','1.2RC2'),
            array(0, '1.2.3a1pre','1.2.3a1-dev'),

            array(-1,'1.2RC-dev','1.2RC'),
            array(1,'1.2RC','1.2RC-dev'),

            array(-1,'1.2pre','1.2a'),
            array(-1,'1.2pre.0','1.2a'),
            array(-1,'1.2pre','1.2b'),
            array(-1,'1.2pre','1.2RC'),
            array(-1,'1.2PRE','1.2RC'),
            array(-1,'1.2a','1.2b'),
            array(-1,'1.2b','1.2rc'),
            array(-1,'1.2rc','1.2'),
            array(-1,'1.2-3.0','1.2-3.1'),
            array(1,'1.2-3.0','1.2'),
            array(-1,'1.2-3.0','1.3'),
            array(0,'1.2-3.0','1.2-3'),
            array(0,'1.2-3','1.2-3.0.0'),
            array(1,'1.2-3.0','1.2-2.9'),
            array(-1,'1.2-3.0','1.2.1-3.0'),
            array(-1,'1.2-3.0','1.2.1-1'),
            array(1,'1.2-3.0','1.2-alpha.2'),
            array(1,'1.2-3.0','1.2-beta.2'),

            array(1,'1.2-rc.2','1.2-beta.2'),
            array(1,'1.2-beta.2','1.2-alpha.2'),
            array(1,'1.2-alpha.2','1.2-dev'),
            array(1,'1.2-alpha.2','1.2-dev.3'),

            array(1, '3.0.6-1.2.0.20161107145649', '3.0.5-1.2.0.20161107145649'),
            array(1, '3.0.6-1.2.0.20161107145649', '3.0.6-1.2.0.20161107145648'),
            array(1, '3.2.0-beta.1.1.3.11.20180413165539', '3.2.0-beta.1.1.3.11.20180413165538'),
            array(-1, '3.2.0-beta.1.1.3.11.20180413165538', '3.2.0-beta.1.1.3.11.20180413165539'),
            array(1, '3.2.0-beta.1-1.3.11.20180413165539', '3.2.0-beta.1-1.3.11.20180413165538'),
            array(-1, '3.2.0-beta.1-1.3.11.20180413165538', '3.2.0-beta.1-1.3.11.20180413165539'),
            array(-1, '3.1.11-1.3.11.20180323171329', '3.2.0-pre.1.3.11.20180409175020'),
            array(0, '3.2beta2-1.4.0.20180516172314', '3.2-beta.2-1.4.0.20180516172314'),
            array(0, '3.3rc1-1.4.4.20190425152736', '3.3-rc.1-1.4.4.20190425152736'),

            array(1,'1.2-alpha.2','1.2-alpha.2-dev.3'),
        );
    }

    /**
     * @dataProvider getCompareVersion
     */
    public function testCompareVersion($result, $v1, $v2) {
        // 0 = equals
        // -1 : v1 < v2
        // 1 : v1 > v2

        //$this->assertEquals($result, VersionComparator::compareVersion($v1,$v2));
        $v1 = Parser::parse($v1);
        $v2 = Parser::parse($v2);
        $this->assertEquals($result, VersionComparator::compare($v1,$v2));
    }

    public function getCompareVersionWildcard() {
        $list = $this->getCompareVersion();
        return array_merge($list, array(
            array(0, '1.2.0', '1.2.*'),
            array(0, '1.2.0', '1.*'),
            array(0, '1.2.0-rc', '1.2.*'),
            array(0, '1.2.3-rc', '1.2.*'),  // >=1.2.0 <1.3.0
            array(0, '1.2.0-rc', '1.2.0-*'),
            array(0, '1.2.0-beta', '1.2.0-*'),
            array(0, '1.2.0-alpha', '1.2.0-*'),
            array(0, '1.2.0-6.6', '1.2.*'),
            array(0, '1.2.0-6.6', '1.2.0-*'),
            array(0, '6.6', '*'),
            array(0, '1.2.0-6.6', '1.2.0:*'),
        ));
    }
    /**
     * @dataProvider getCompareVersionWildcard
     */
    public function testCompareWildcardVersion($result, $v1, $v2) {
        // 0 = equals
        // -1 : v1 < v2
        // 1 : v1 > v2

        $v1 = Parser::parse($v1);
        $v2 = Parser::parse($v2);
        $this->assertEquals($result, VersionComparator::compare($v1, $v2));
    }

    protected function _compare($v1, $v2) {
        $v1 = VersionComparator::serializeVersion2($v1);
        $v2 = VersionComparator::serializeVersion2($v2);
        if ($v1 == $v2)
            return 0;
        if ($v1 < $v2)
            return -1;
        return 1;
    }
    protected function _comparer($v1, $v2) {
        $v1 = VersionComparator::serializeVersion2($v1);
        $v2 = VersionComparator::serializeVersion2($v2,1);
        if ($v1 == $v2)
            return 0;
        if ($v1 < $v2)
            return -1;
        return 1;
    }
    protected function _comparel($v1, $v2) {
        $v1 = VersionComparator::serializeVersion2($v1,-1);
        $v2 = VersionComparator::serializeVersion2($v2);
        if ($v1 == $v2)
            return 0;
        if ($v1 < $v2)
            return -1;
        return 1;
    }

    public function testSerialization() {
        $this->assertEquals('001z99z.002a99z.0000000000a00a.0000000000a00a', VersionComparator::serializeVersion('1.2alpha'));
        $this->assertEquals('001z99z.001z99z.0000000000a00a.0000000000a00a', VersionComparator::serializeVersion('1.1'));
        $this->assertEquals('001z99z.001z99z.0000000001z99z.0000000000a00a', VersionComparator::serializeVersion('1.1.1'));
        $this->assertEquals('001z99z.001z99z.0000000002z99z.0000000000a00a', VersionComparator::serializeVersion('1.1.2'));
        $this->assertEquals('001z99z.000a00a.0000000000a00a.0000000000a00a', VersionComparator::serializeVersion('1.*'));
    }

    public function getSerialResult()
    {
        return array(
            array('1.2alpha',        'z001z002z0000000000z0000000000z0000000000-a000a000a0000000000a0000000000a0000000000-z000z000z0000000000z0000000000z0000000000'),
            array('1.1',             'z001z001z0000000000z0000000000z0000000000-z000z000z0000000000z0000000000z0000000000-z000z000z0000000000z0000000000z0000000000'),
            array('1.1.1.4.5',       'z001z001z0000000001z0000000004z0000000005-z000z000z0000000000z0000000000z0000000000-z000z000z0000000000z0000000000z0000000000'),
            array('1.1.2-dev.3-pre', 'z001z001z0000000002z0000000000z0000000000-_000_003_0000000000_0000000000_0000000000-_000_000_0000000000_0000000000_0000000000'),
            array('1.2.*',           'z001z002z0000000000z0000000000z0000000000-z000z000z0000000000z0000000000z0000000000-z000z000z0000000000z0000000000z0000000000'),
            array('1.2RC2',          'z001z002z0000000000z0000000000z0000000000-r000r002r0000000000r0000000000r0000000000-z000z000z0000000000z0000000000z0000000000'),
            array('3.2.0-beta.1.1.3.11.20180413165538', 'z003z002z0000000000z0000000000z0000000000-b000b001b0000000001b0000000003b0000000011b20180413165538-z000z000z0000000000z0000000000z0000000000'),
            array('3.2.0-beta.1.1.3.11.20180413165539', 'z003z002z0000000000z0000000000z0000000000-b000b001b0000000001b0000000003b0000000011b20180413165539-z000z000z0000000000z0000000000z0000000000'),
            array('3.2.0-beta.1-1.3.11.20180413165538', 'z003z002z0000000000z0000000000z0000000000-b000b001b0000000000b0000000000b0000000000-z000z000z0000000000z0000000000z0000000000'),
            array('1.0.0-alpha.1',    'z001z000z0000000000z0000000000z0000000000-a000a001a0000000000a0000000000a0000000000-z000z000z0000000000z0000000000z0000000000'),
            array('1.0.0-alpha.beta', 'z001z000z0000000000z0000000000z0000000000-a000b000b0000000000b0000000000b0000000000-z000z000z0000000000z0000000000z0000000000'),
        );
    }

    /**
     * @dataProvider getSerialResult
     */
    public function testSerialization2($version, $serial) {
        $this->assertEquals($serial, VersionComparator::serializeVersion2($version));
    }

    public function testCompareSerializedVersion() {

        // 0 = equals
        // -1 : v1 < v2
        // 1 : v1 > v2
        $this->assertEquals(0, $this->_compare('1.0','1.0'));
        $this->assertEquals(1, $this->_compare('1.1','1.0'));
        $this->assertEquals(-1, $this->_compare('1.0','1.1'));
        $this->assertEquals(-1, $this->_compare('1.1','1.1.1'));
        $this->assertEquals(1, $this->_compare('1.1.2','1.1'));
        $this->assertEquals(1, $this->_compare('1.2','1.2b'));
        $this->assertEquals(1, $this->_compare('1.2','1.2a'));
        $this->assertEquals(1, $this->_compare('1.2','1.2RC'));
        $this->assertEquals(1, $this->_compare('1.2','1.2bETA'));
        $this->assertEquals(1, $this->_compare('1.2','1.2alpha'));

        $this->assertEquals(-1, $this->_compare('1.2b','1.2'));
        $this->assertEquals(-1, $this->_compare('1.2a','1.2'));
        $this->assertEquals(-1, $this->_compare('1.2RC','1.2'));
        $this->assertEquals(-1, $this->_compare('1.2bEta','1.2'));
        $this->assertEquals(-1, $this->_compare('1.2alpha','1.2'));

        $this->assertEquals(-1, $this->_compare('1.0.0-alpha', '1.0.0-alpha.1'));
        $this->assertEquals(-1, $this->_compare('1.0.0-alpha.1', '1.0.0-alpha.beta'));
        $this->assertEquals(-1, $this->_compare('1.0.0-alpha.beta', '1.0.0-beta'));
        $this->assertEquals(-1, $this->_compare('1.0.0-beta', '1.0.0-beta.2'));
        $this->assertEquals(-1, $this->_compare('1.0.0-beta.2', '1.0.0-beta.11'));
        $this->assertEquals(-1, $this->_compare('1.0.0-beta.11', '1.0.0-rc.1'));
        $this->assertEquals(-1, $this->_compare('1.0.0-rc.1 ', '1.0.0'));

        $this->assertEquals(-1, $this->_compare('1.2b1','1.2b2'));
        $this->assertEquals(-1, $this->_compare('1.2B1','1.2b2'));
        $this->assertEquals(1, $this->_compare('1.2b2','1.2b1'));
        $this->assertEquals(1, $this->_compare('1.2b2','1.2b2-dev'));
        $this->assertEquals(-1, $this->_compare('1.2b2-dev','1.2b2'));
        $this->assertEquals(-1, $this->_compare('1.2b2-dev.2324','1.2b2'));
        $this->assertEquals(0, $this->_compare('1.2b2pre','1.2b2-dev'));
        $this->assertEquals(1, $this->_compare('1.2b2pre.4','1.2b2-dev'));
        $this->assertEquals(-1, $this->_compare('1.2b2pre.4','1.2b2-dev.9'));
        $this->assertEquals(-1, $this->_compare('1.2b2pre','1.2b2-dev.9'));
        $this->assertEquals(-1, $this->_compare('1.2RC1','1.2RC2'));
        $this->assertEquals(0, $this->_compare('1.2.3a1pre','1.2.3a1-dev'));

        $this->assertEquals(-1, $this->_compare('3.2pre171102','3.2pre180212'));

        $this->assertEquals(-1, $this->_compare('1.2RC-dev','1.2RC'));
        $this->assertEquals(1, $this->_compare('1.2RC','1.2RC-dev'));

        $this->assertEquals(-1, $this->_compare('1.2RC2-dev.1699','1.2RC2-dev.1700'));

        $this->assertEquals(0, $this->_compare('1.*','1'));
        $this->assertEquals(-1, $this->_comparel('1.1.*','1.1.1'));
        $this->assertEquals(-1, $this->_comparer('1.1.2','1.1.*'));
        $this->assertEquals(0, $this->_comparel('1.1.*','1.1'));
        $this->assertEquals(-1, $this->_comparer('1.1','1.1.*'));
        $this->assertEquals(-1, $this->_compare('1.1.*','1.2'));
        $this->assertEquals(-1, $this->_compare('1.1','1.2.*'));

        $this->assertEquals(-1, $this->_comparer('1.1','*'));
        $this->assertEquals(-1, $this->_comparel('*','1.1'));

        $this->assertEquals(-1, $this->_compare('1.2pre','1.2a'));
        $this->assertEquals(-1, $this->_compare('1.2pre','1.2b'));
        $this->assertEquals(-1, $this->_compare('1.2pre','1.2RC'));
        $this->assertEquals(-1, $this->_compare('1.2PRE','1.2RC'));
        $this->assertEquals(-1, $this->_compare('1.2a','1.2b'));
        $this->assertEquals(-1, $this->_compare('1.2b','1.2rc'));
        $this->assertEquals(-1, $this->_compare('1.2rc','1.2'));


        $this->assertEquals(-1, $this->_compare('3.2.0-beta.1.1.3.11.20180413165538','3.2.0-beta.1.1.3.11.20180413165539'));
        $this->assertEquals(0, $this->_compare('3.2.0-beta.1-1.3.11.20180413165538','3.2.0-beta.1-1.3.11.20180413165539'));
        $this->assertEquals(1, $this->_compare('3.2.0-beta.1.1.3.11.20180413165538','3.2.0-beta.1.1.3.11.20180413165537'));
        $this->assertEquals(0, $this->_compare('3.2.0-beta.1-1.3.11.20180413165538','3.2.0-beta.1-1.3.11.20180413165537'));
    }


    public function getBoundsFromWildcardVersion()
    {
        return array(
            array('1.2.*',   '1.2.0-dev', '1.3.0-dev'),
            array('3.2*',    '3.2.0-dev', '3.3.0-dev'),
            array('1.2.0-*', '1.2.0-dev', '1.2.1-dev'),
            array('3.2-*',   '3.2.0-dev', '3.2.1-dev'),
            array('3.2.4-*', '3.2.4-dev', '3.2.5-dev'),
        );
    }

    /**
     * @dataProvider getBoundsFromWildcardVersion
     */
    public function testBoundsFromWildcardVersion($version, $boundLeft, $boundRight)
    {
        $v = Parser::parse($version);
        list($b1, $b2) = VersionComparatorForTests::getBoundsFromWildcardVersionForTest($v);
        $this->assertEquals($boundLeft, (string)$b1);
        $this->assertEquals($boundRight, (string)$b2);
    }

    public function getCompileRange()
    {
        return array(
            array('1.2.*', '(>=1.2.0-dev) AND (<1.3.0-dev)'),
            array('3.2*', '=3.2.*'),
            array('1.2.0-*', '=1.2.0-*'),
            array('3.2-*', '=3.2.0-*'),
            array('3.2.4-*', '=3.2.4-*'),
        );
    }

    /**
     * @dataProvider getCompileRange
     */
    public function testCompileRange($range, $serializedResult)
    {
        $result = VersionComparatorForTests::compileRangeForTest($range);
        $this->assertEquals($serializedResult, (string)$result);
    }

    public function getCompareVersionRange() {
        return array(
            array(true,     '1.1',      '=1.1'),
            array(true,     '1.1',      '>1.0'),
            array(true,     '1.1',      '<1.2'),
            array(true,     '1.1',      '<=1.1'),
            array(true,     '1.1',      '>=1.1'),
            array(false,    '1.0',      '>=1.1'),
            array(false,    '1.0',      '<=0.9'),
            array(false,    '1.0',      '=0.9'),
            array(true,     '1.1',      '=1.1.*'),
            array(true,     '1.5',      '!=1.3'),
            array(true,     '1.2',      '!=1.3'),
            array(true,     '1.3.2',    '!=1.3'),
            array(false,    '1.3',      '!=1.3'),
            array(false,    '1.3.0',    '!=1.3'),
            array(true,     '1.5',      '~1.3'),
            array(true,     '1.9',      '~1.3'),
            array(true,     '1.3',      '~1.3'),
            array(false,    '1.2',      '~1.3'),
            array(false,    '2.0',      '~1.3'),
            array(false,    '2.5',      '~1.3'),
            array(false,    '2.0b',     '~1.3'),
            array(true,     '1.5',      '^1.3'),
            array(true,     '1.9',      '^1.3'),
            array(true,     '1.3',      '^1.3'),
            array(true,     '1.3.2',    '^1.3'),
            array(true,     '1.4.0',    '^1.3'),
            array(true,     '1.99.99',    '^1.3'),
            array(false,    '1.2',      '^1.3'),
            array(false,    '2.0',      '^1.3'),
            array(false,    '2.5',      '^1.3'),
            array(false,    '2.0b',     '^1.3'),
            array(true,     '1.3.2',    '^1.3.2'),
            array(false,    '1.3.1',    '^1.3.2'),
            array(true,     '1.3.3',    '^1.3.2'),
            array(true,     '1.4.0',    '^1.3.2'),
            array(true,     '1.1',      '>1.0,<2.0'),
            array(true,     '2.3',      '<1.0||>2.0'),
            array(false,    '1.5',      '<1.0||>2.0'),
            array(true,     '1.0',      '1.0 - 2.0'),
            array(true,     '1.1',      '1.0 - 2.0'),
            array(true,     '2.0',      '1.0 - 2.0'),
            array(true,     '2.0-beta', '1.0 - 2.0'),
            array(false,    '0.9',      '1.0 - 2.0'),
            array(true,     '2.0.1',    '1.0 - 2.0'),
            array(false,    '2.0.1',    '1.0 - 2.0.0'),
            array(false,    '2.9.0',    '<2.0.0-dev'),
            array(false,    '2.9.0',    '>2.10.0-dev'),
            array(true,     '2.9.0',    '>2.9.0-dev'),
            array(true,     '0.9',      '<1.0 , >0.8||>2.0'),
            array(true,     '0.9.9',    '<1.0 >0.8 || >2.0'),
            array(true,     '2.1',      '<1.0 >0.8 |>2.0'),
            array(false,    '1.0',      '<1.0 >0.8||>2.0'),
            array(false,    '0.8',      '<1.0 >0.8||>2.0'),
            array(false,    '2.0',      '<1.0 >0.8||>2.0'),
            array(false,    '0.5',      '<1.0,>0.8||>2.0'),
            array(false,    '1.5',      '<1.0,>0.8||>2.0'),
            array(true,     '0.9',      '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(true,     '2.0.1',    '<1.0,>0.8||>=2.0.*,<2.5.4||>3.0'),
            array(true,     '2.3',      '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(true,     '3.1',      '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(false,    '0.7',      '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(false,    '1.3',      '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(false,    '2.5.4',    '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(false,    '2.9',      '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(true,     '1.7.0pre.0',   '1.4pre - 1.7.0pre.*'),
            array(true,     '1.7.0pre.0',   '>=1.4pre <=1.7.0pre.*'),
            array(true,     '1',        '1.*'),
            array(true,     '1.1.1',    '1.1.*'),
            array(true,     '1.1.2',    '1.1.*'),
            array(true,     '1.1',      '1.1.*'),
            array(false,    '1.2',      '1.1.*'),
            array(false,    '1.1',      '1.2.*'),
            array(true,     '1.1',      '*'),
            array(true,     '0.3.2',    '0.3.*'),
            array(true,     '3.0.5-1.2.0.20161107145649', '>=3.0.5-1.2.0.20161107145649'),
            array(false,    '3.0.5-1.2.0.20161107145649', '>=3.0.5-1.2.0.20161107145650'),
            array(true,     '3.0.5-1.2.0.20161107145649', '>=3.0.5-1.2.0.20161107145648'),
            array(true,     '3.0.5-1.2.0.20161107145649', '>=3.0.4-1.2.0.20161107145648'),
            array(false,    '3.0.5-1.2.0.20161107145649', '>=3.0.6-1.2.0.20161107145648'),
            array(false,     '3.2beta2-1.4.0.20180516172314', '3.2.0-pre.*'),
            array(true,     '3.2beta2-1.4.0.20180516172314', '3.2*'),
            array(true,     '3.2beta2-1.4.0.20180516172314', '3.2-*'),
            array(true,     '3.2.0',        '3.2-*'),
            array(true,     '3.2.0-rc',     '3.2-*'),
            array(true,     '3.2.0-beta',   '3.2-*'),
            array(true,     '3.2.0-alpha',  '3.2-*'),
            array(false,    '3.2.1',        '3.2-*'),
            array(true,     '3.2beta2-1.4.0.20180516172314', '3.2.*'),
            array(false,    '3.2beta2-1.4.0.20180516172314', '3.2.*-stable'),
            array(false,    '3.2.4',        '3.2-*'),
            array(true,     '3.2.4-dev',    '3.2.4-*'),
            array(true,     '3.2.4-dev',    '3.2.*-*'),
            //array(false,    '3.2.4-dev',    '3.2.*-rc'),
            array(true,     '3.2.4-1.2',    '3.2.4-1.*'),
            array(false,    '3.22-1.4.0.20180516172314', '3.2*'),
            array(true,     '3.2-1.4.0.20180516172314', '3.2*'),
            array(true,     '3.22-1.4.0.20180516172314', '3.22.*'),
            array(true,     '3.3rc1-1.4.4.20190425152736', '3.3rc*'),
            array(true,     '3.3rc1-1.4.4.20190425152736', '3.3.*||3.3rc*'),
            array(true,     '1.5.2',        '>=1.3.6'),
            array(true,     '1.5.2',        '<=1.5.*'),
            array(true,     '1.5.2',        '>=1.5.*'),
            array(true,     '1.5.2',        '<=1.6.*'),
            array(true,     '1.5.2',        '>=1.3.6,<=1.5.*'),
            array(true,     '3.3.7-pre.202005285413', '3.3.*||3.3-pre.*'),
            array(true,     '3.3.6-pre-1.4.7.20200421101118', '>=3.3.6-pre'),
        );
    }
    /**
     * @dataProvider getCompareVersionRange
     */
    public function testCompareRangeVersion($result, $version, $constraints) {
        if ($result) {
            $this->assertTrue(VersionComparator::compareVersionRange($version, $constraints));
        }
        else {
            $this->assertFalse(VersionComparator::compareVersionRange($version, $constraints));
        }

    }
}

