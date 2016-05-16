<?php
/**
* @author      Laurent Jouanneau
* @contributor
* @copyright   2009-2016 Laurent Jouanneau
* @link        http://jelix.org
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
* @since 1.2
*/

use Jelix\Version\VersionComparator;
use Jelix\Version\Parser;
use Jelix\Version\Version;

class versionComparatorTest extends PHPUnit_Framework_TestCase {

    public function getCompareVersion() {
        return array(
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

    protected function _compare($v1, $v2) {
        $v1 = VersionComparator::serializeVersion($v1);
        $v2 = VersionComparator::serializeVersion($v2);
        if ($v1 == $v2)
            return 0;
        if ($v1 < $v2)
            return -1;
        return 1;
    }
    protected function _comparer($v1, $v2) {
        $v1 = VersionComparator::serializeVersion($v1);
        $v2 = VersionComparator::serializeVersion($v2,1);
        if ($v1 == $v2)
            return 0;
        if ($v1 < $v2)
            return -1;
        return 1;
    }
    protected function _comparel($v1, $v2) {
        $v1 = VersionComparator::serializeVersion($v1,-1);
        $v2 = VersionComparator::serializeVersion($v2);
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
    }

    public function getCompareVersionRange() {
        return array(
            array(true,     '1.1',  '=1.1'),
            array(true,     '1.1',  '>1.0'),
            array(true,     '1.1',  '<1.2'),
            array(true,     '1.1',  '<=1.1'),
            array(true,     '1.1',  '>=1.1'),
            array(false,    '1.0',  '>=1.1'),
            array(false,    '1.0',  '<=0.9'),
            array(false,    '1.0',  '=0.9'),
            array(true,     '1.1',  '=1.1.*'),
            array(true,     '1.5',  '!=1.3'),
            array(true,     '1.2',  '!=1.3'),
            array(true,     '1.3.2','!=1.3'),
            array(false,    '1.3',  '!=1.3'),
            array(false,    '1.3.0','!=1.3'),
            array(true,     '1.5',  '~1.3'),
            array(true,     '1.9',  '~1.3'),
            array(true,     '1.3',  '~1.3'),
            array(false,    '1.2',  '~1.3'),
            array(false,    '2.0',  '~1.3'),
            array(false,    '2.5',  '~1.3'),
            array(false,    '2.0b', '~1.3'),
            array(false,     '1.5',  '^1.3'),
            array(false,     '1.9',  '^1.3'),
            array(true,     '1.3',  '^1.3'),
            array(true,     '1.3.2',  '^1.3'),
            array(false,     '1.4.0',  '^1.3'),
            array(false,    '1.2',  '^1.3'),
            array(false,    '2.0',  '^1.3'),
            array(false,    '2.5',  '^1.3'),
            array(false,    '2.0b', '^1.3'),
            array(true,     '1.3.2',  '^1.3.2'),
            array(false,     '1.3.1',  '^1.3.2'),
            array(true,     '1.3.3',  '^1.3.2'),
            array(false,     '1.4.0',  '^1.3.2'),
            array(true,     '1.1',  '>1.0,<2.0'),
            array(true,     '2.3',  '<1.0||>2.0'),
            array(false,    '1.5',  '<1.0||>2.0'),
            array(true,     '1.0',  '1.0 - 2.0'),
            array(true,     '1.1',  '1.0 - 2.0'),
            array(true,     '2.0',  '1.0 - 2.0'),
            array(true,     '2.0-beta',  '1.0 - 2.0'),
            array(false,     '0.9',  '1.0 - 2.0'),
            array(true,     '2.0.1',  '1.0 - 2.0'),
            array(false,     '2.0.1',  '1.0 - 2.0.0'),
            array(true,     '0.9',  '<1.0 , >0.8||>2.0'),
            array(true,     '0.9.9','<1.0 >0.8 || >2.0'),
            array(true,     '2.1',  '<1.0 >0.8 |>2.0'),
            array(false,    '1.0',  '<1.0 >0.8||>2.0'),
            array(false,    '0.8',  '<1.0 >0.8||>2.0'),
            array(false,    '2.0',  '<1.0 >0.8||>2.0'),
            array(false,    '0.5',  '<1.0,>0.8||>2.0'),
            array(false,    '1.5',  '<1.0,>0.8||>2.0'),
            array(true,     '0.9',  '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(true,     '2.0.1','<1.0,>0.8||>=2.0.*,<2.5.4||>3.0'),
            array(true,     '2.3',  '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(true,     '3.1',  '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(false,    '0.7',  '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(false,    '1.3',  '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(false,    '2.5.4','<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),
            array(false,    '2.9',  '<1.0,>0.8||>2.0.*,<2.5.4||>3.0'),

            array(true, '1','1.*'),
            array(true,'1.1.1','1.1.*'),
            array(true,'1.1.2','1.1.*'),
            array(true,'1.1','1.1.*'),
            array(false,'1.2','1.1.*'),
            array(false,'1.1','1.2.*'),
            array(true,'1.1','*'),
        );
    }
    /**
     * @dataProvider getCompareVersionRange
     */
    public function testCompareVersionRange($result, $version, $constraints) {
        if ($result) {
            $this->assertTrue(VersionComparator::compareVersionRange($version, $constraints));
        }
        else {
            $this->assertFalse(VersionComparator::compareVersionRange($version, $constraints));
        }
        
    }
}

