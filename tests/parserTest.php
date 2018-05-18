<?php
/**
* @author      Laurent Jouanneau
* @copyright   2016 Laurent Jouanneau
* @link        http://jelix.org
* @licence     MIT
*/

use Jelix\Version\Version as Version;
use Jelix\Version\Parser as Parser;

class parserTest extends PHPUnit_Framework_TestCase {

    public function getVersions() {
        return array(
            array('1.2',                    1, 2, 0, array(),   array(),                        '', '1.2.0',        '1.3',      ''),
            array('1.2.3',                  1, 2, 3, array(),   array(),                        '', '1.2.3',        '1.2.4',    ''),
            array('1',                      1, 0, 0, array(),   array(),                        '', '1.0.0',        '2',        ''),
            array('1.2.3.4.5',              1, 2, 3, array(4,5),array(),                        '', '1.2.3.4.5',    '1.2.3.4.6', ''),
            array('1.2b',                   1, 2, 0, array(),   array('beta'),                  '', '1.2.0-beta',   '1.2', ''),
            array('1.2a',                   1, 2, 0, array(),   array('alpha'),                 '', '1.2.0-alpha',  '1.2', ''),
            array('1.2RC',                  1, 2, 0, array(),   array('rc'),                    '', '1.2.0-rc',     '1.2', ''),
            array('1.2bETA',                1, 2, 0, array(),   array('beta'),                  '', '1.2.0-beta',   '1.2', ''),
            array('1.2alpha',               1, 2, 0, array(),   array('alpha'),                 '', '1.2.0-alpha',  '1.2', ''),
            array('1.2pre',                 1, 2, 0, array(),   array('pre'),                   '', '1.2.0-pre',    '1.2', ''),
            array('1.2B1',                  1, 2, 0, array(),   array('beta','1'),              '', '1.2.0-beta.1', '1.2', ''),
            array('1.2a2',                  1, 2, 0, array(),   array('alpha', '2'),            '', '1.2.0-alpha.2', '1.2', ''),
            array('1.2-a2',                 1, 2, 0, array(),   array('alpha', '2'),            '', '1.2.0-alpha.2', '1.2', ''),
            array('1.2alpha2',              1, 2, 0, array(),   array('alpha', '2'),            '', '1.2.0-alpha.2', '1.2', ''),
            array('1.2-alpha.2',            1, 2, 0, array(),   array('alpha', '2'),            '', '1.2.0-alpha.2', '1.2', ''),
            array('1.2-alpha.2.3.4',        1, 2, 0, array(),   array('alpha', '2', '3', '4'),  '', '1.2.0-alpha.2.3.4',    '1.2', ''),
            array('1.2-alpha2',             1, 2, 0, array(),   array('alpha', '2'),            '', '1.2.0-alpha.2',        '1.2', ''),
            array('1.2b2pre',               1, 2, 0, array(),   array('beta','2','pre'),        '', '1.2.0-beta.2.pre',     '1.2', ''),
            array('1.2b2pre.4',             1, 2, 0, array(),   array('beta','2','pre', '4'),   '', '1.2.0-beta.2.pre.4',   '1.2', ''),
            array('1.2b2-dev',              1, 2, 0, array(),   array('beta','2','dev'),        '', '1.2.0-beta.2.dev',     '1.2', ''),
            array('1.2.3a1pre',             1, 2, 3, array(),   array('alpha','1','pre'),       '', '1.2.3-alpha.1.pre',    '1.2.3', ''),
            array('1.2RC-dev',              1, 2, 0, array(),   array('rc','dev'),              '', '1.2.0-rc.dev',         '1.2', ''),
            array('1.2-3.5.2',              1, 2, 0, array(),   array(),                        '', '1.2.0-3.5.2',          '1.3', '3.5.2'),
            array('1.2b2pre.4+2.3.4foo',    1, 2, 0, array(),   array('beta','2','pre', '4'),   '2.3.4foo', '1.2.0-beta.2.pre.4+2.3.4foo', '1.2', '' ),
            array('1.2b2pre.4-1.2.5+2.3.4foo',    1, 2, 0, array(),   array('beta','2','pre', '4'),   '2.3.4foo', '1.2.0-beta.2.pre.4-1.2.5+2.3.4foo', '1.2', '1.2.5' ),
            array('3.2beta2-1.4.0.20180516172314', 3, 2, 0, array(), array('beta', '2'),        '', '3.2.0-beta.2-1.4.0.20180516172314', '3.2' , '1.4.0.20180516172314'),
            array('3.2-beta.2-1.4.0.20180516172314', 3, 2, 0, array(), array('beta', '2'),      '', '3.2.0-beta.2-1.4.0.20180516172314', '3.2' , '1.4.0.20180516172314'),
        );
    }

    /**
     * @dataProvider getVersions
     */
    public function testVersions($version, $major, $minor, $patch, $tail, $stab, $md, $str, $nextVersion, $secondaryVersion) {
        $version = Parser::parse($version);
        $this->assertEquals($major, $version->getMajor());
        $this->assertEquals($minor, $version->getMinor());
        $this->assertEquals($patch, $version->getPatch());
        $this->assertEquals($tail, $version->getTailNumbers());
        $this->assertEquals($stab, $version->getStabilityVersion());
        $this->assertEquals($md, $version->getBuildMetadata());
        $this->assertEquals($str, $version->toString());
        $this->assertEquals($nextVersion, $version->getNextTailVersion());
        if ($secondaryVersion) {
            if ($version->getSecondaryVersion()) {
                $this->assertEquals($secondaryVersion, $version->getSecondaryVersion()->toString());
            }
            else {
                // always fails obviously
                $this->assertEquals($secondaryVersion, '');
            }
        }
        else {
            $this->assertNull($version->getSecondaryVersion());
        }
    }


    public function getNextVersions() {
        return array(
            array('1',          '2.0.0', '1.1.0', '1.0.1', '1.0' ),
            array('1.2',        '2.0.0', '1.3.0', '1.2.1', '1.2' ),
            array('1.2.3.4',    '2.0.0', '1.3.0', '1.2.4', '1.2' ),
            array('1.2-3.5.2',  '2.0.0', '1.3.0', '1.2.1', '1.2' ),
        );
    }

    /**
     * @dataProvider getNextVersions
     */
    public function testNextVersions($version, $nextMajor, $nextMinor, $nextPatch, $branch) {
        $version = Parser::parse($version);
        $this->assertEquals($nextMajor, $version->getNextMajorVersion());
        $this->assertEquals($nextMinor, $version->getNextMinorVersion());
        $this->assertEquals($nextPatch, $version->getNextPatchVersion());
        $this->assertEquals($branch, $version->getBranchVersion());
    }
}