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
            array('1.2',        1, 2, 0, array(), array(), '', '1.2.0'),
            array('1.2.3',      1, 2, 3, array(), array(), '', '1.2.3'),
            array('1',          1, 0, 0, array(), array(), '', '1.0.0'),
            array('1.2.3.4.5',  1, 2, 3, array(4,5), array(), '', '1.2.3.4.5'),

            array('1.2b',       1, 2, 0, array(), array('beta'), '', '1.2.0-beta'),
            array('1.2a',       1, 2, 0, array(), array('alpha'), '', '1.2.0-alpha'),
            array('1.2RC',      1, 2, 0, array(), array('rc'), '', '1.2.0-rc'),
            array('1.2bETA',    1, 2, 0, array(), array('beta'), '', '1.2.0-beta'),
            array('1.2alpha',   1, 2, 0, array(), array('alpha'), '', '1.2.0-alpha'),
            array('1.2pre',     1, 2, 0, array(), array('pre'), '', '1.2.0-pre'),
            array('1.2B1',      1, 2, 0, array(), array('beta','1'), '', '1.2.0-beta.1'),
            array('1.2a2',      1, 2, 0, array(), array('alpha', '2'), '', '1.2.0-alpha.2'),
            array('1.2-a2',     1, 2, 0, array(), array('alpha', '2'), '', '1.2.0-alpha.2'),
            array('1.2alpha2',  1, 2, 0, array(), array('alpha', '2'), '', '1.2.0-alpha.2'),
            array('1.2-alpha.2', 1, 2, 0, array(), array('alpha', '2'), '', '1.2.0-alpha.2'),
            array('1.2-alpha.2.3.4', 1, 2, 0, array(), array('alpha', '2', '3', '4'), '', '1.2.0-alpha.2.3.4'),
            array('1.2-alpha2', 1, 2, 0, array(), array('alpha', '2'), '', '1.2.0-alpha.2'),
            array('1.2b2pre',   1, 2, 0, array(), array('beta','2','pre'), '', '1.2.0-beta.2.pre'),
            array('1.2b2pre.4', 1, 2, 0, array(), array('beta','2','pre', '4'), '', '1.2.0-beta.2.pre.4'),
            array('1.2b2-dev',  1, 2, 0, array(), array('beta','2','dev'), '', '1.2.0-beta.2.dev'),
            array('1.2.3a1pre', 1, 2, 3, array(), array('alpha','1','pre'), '', '1.2.3-alpha.1.pre'),
            array('1.2RC-dev',  1, 2, 0, array(), array('rc','dev'), '', '1.2.0-rc.dev'),
            array('1.2b2pre.4+2.3.4foo', 1, 2, 0, array(), array('beta','2','pre', '4'), '2.3.4foo', '1.2.0-beta.2.pre.4+2.3.4foo'),
        );
    }

    /**
     * @dataProvider getVersions
     */
    public function testVersions($version, $major, $minor, $patch, $tail, $stab, $md, $str) {
        $version = Parser::parse($version);
        $this->assertEquals($major, $version->getMajor());
        $this->assertEquals($minor, $version->getMinor());
        $this->assertEquals($patch, $version->getPatch());
        $this->assertEquals($tail, $version->getTailNumbers());
        $this->assertEquals($stab, $version->getStabilityVersion());
        $this->assertEquals($md, $version->getBuildMetadata());
        $this->assertEquals($str, $version->toString());
    }


    public function getNextVersions() {
        return array(
            array('1', '2.0.0', '1.1.0', '1.0.1', '1.0' ),
            array('1.2', '2.0.0', '1.3.0', '1.2.1', '1.2' ),
            array('1.2.3.4', '2.0.0', '1.3.0', '1.2.4', '1.2' ),
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