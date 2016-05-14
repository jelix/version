<?php
/**
* @author      Laurent Jouanneau
* @copyright   2016 Laurent Jouanneau
*
* @link        http://www.jelix.org
* @licence     MIT
*/

namespace Jelix\Version;

/**
 *
 */
class Version
{
    private $version = array();

    private $stabilityVersion = array();

    private $buildMetadata = '';

    public function __construct(array $version, array $stabilityVersion = array(), $buildMetadata = '')
    {
        if (count($version) < 3) {
            $version = array_pad($version, 3, '0');
        }
        $this->version = $version;
        $this->stabilityVersion = $stabilityVersion;
        $this->buildMetadata = $buildMetadata;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        $vers = implode('.', $this->version);
        if ($this->stabilityVersion) {
            $vers .= '-'.implode('.', $this->stabilityVersion);
        }
        if ($this->buildMetadata) {
            $vers .= '+'.$this->buildMetadata;
        }

        return $vers;
    }

    public function getMajor()
    {
        return $this->version[0];
    }

    public function getMinor()
    {
        return $this->version[1];
    }

    public function getPatch()
    {
        return $this->version[2];
    }

    public function getTailNumbers()
    {
        if (count($this->version) > 3) {
            return array_slice($this->version, 3);
        }

        return array();
    }

    public function getBranchVersion()
    {
        return $this->version[0].'.'.$this->version[1];
    }

    public function getStabilityVersion()
    {
        return $this->stabilityVersion;
    }

    public function getBuildMetadata()
    {
        return $this->buildMetadata;
    }

    /**
     * Returns the next major version
     * 2.1.3 -> 3.0.0
     * 2.1b1.4 -> 3.0.0.
     */
    public function getNextMajorVersion()
    {
        return ($this->version[0] + 1).'.0.0';
    }

    /**
     * Returns the next minor version
     * 2.1.3 -> 2.2
     * 2.1 -> 2.2
     * 2.1b1.4 -> 2.2.
     */
    public function getNextMinorVersion()
    {
        return $this->version[0].'.'.($this->version[1] + 1).'.0';
    }

    /**
     * Returns the next patch version
     * 2.1.3 -> 2.1.4
     * 2.1b1.4 -> 2.2.
     */
    public function getNextPatchVersion()
    {
        return $this->version[0].'.'.$this->version[1].'.'.($this->version[2] + 1);
    }
}
