<?php
/**
* @author      Laurent Jouanneau
* @copyright   2016-2018 Laurent Jouanneau
*
* @link        http://www.jelix.org
* @licence     MIT
*/

namespace Jelix\Version;

class Parser
{
    private function __construct()
    {
    }

    /**
     * Is able to parse semantic version syntax or any other version syntax.
     *
     * @param string $version
     * @param array $options list of options for the parser.
     *          'removeWildcard' => true
     * 
     * @return Version
     */
    public static function parse($version, $options = array())
    {
        $options = array_merge(array(
            'removeWildcard' => true
        ), $options);

        // extract meta data
        $vers = explode('+', $version, 2);
        $metadata = '';
        if (count($vers) > 1) {
            $metadata = $vers[1];
        }
        $version = $vers[0];

        // extract secondary version
        $allVersions = preg_split('/(-|:)([0-9]+|\*)($|\.|-)/', $version, 2, PREG_SPLIT_DELIM_CAPTURE);
        $version = $allVersions[0];
        if (count($allVersions) > 1 && $allVersions[1] != '') {
            if ($allVersions[2] == '*' && $options['removeWildcard']) {
                $secondaryVersion = null;
                $secondaryVersionSeparator = '-';
            }
            else {
                $secondaryVersion = self::parse($allVersions[2].$allVersions[3].$allVersions[4], $options);
                $secondaryVersionSeparator = $allVersions[1];
            }
        }
        else {
            $secondaryVersion = null;
            $secondaryVersionSeparator = '-';
        }

        // extract stability part
        $vers = explode('-', $version, 2);
        $stabilityVersion = array();
        if (count($vers) > 1) {
            $stabilityVersion = explode('.', $vers[1]);
        }

        // extract version parts
        $vers = explode('.', $vers[0]);
        foreach ($vers as $k => $number) {
            if (!is_numeric($number)) {
                if (preg_match('/^([0-9]+)(.*)$/', $number, $m)) {
                    $vers[$k] = $m[1];
                    $stabilityVersion = array_merge(
                                            array($m[2]),
                                            array_slice($vers, $k + 1),
                                            $stabilityVersion
                                        );
                    $vers = array_slice($vers, 0, $k + 1);
                    break;
                } elseif ($number == '*') {
                    $vers = array_slice($vers, 0, $k);
                    if (!$options['removeWildcard']) {
                        $vers[$k] = '*';
                    }
                    break;
                } else {
                    throw new \Exception('Bad version syntax');
                }
            } else {
                $vers[$k] = intval($number);
            }
        }

        $stab = array();
        foreach ($stabilityVersion as $k => $part) {
            if (preg_match('/^[a-z]+$/', $part)) {
                $stab[] = self::normalizeStability($part);
            } elseif (preg_match('/^[0-9]+$/', $part)) {
                $stab[] = $part;
            } else {
                $m = preg_split('/([0-9]+)/', $part, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                foreach ($m as $p) {
                    $stab[] = self::normalizeStability($p);
                }
            }
        }

        if (count($stab) == 0 && $secondaryVersion && !$options['removeWildcard']) {
            if ($secondaryVersion->getMajor() == '*' && $secondaryVersionSeparator == '-') {
                $secondaryVersion = null;
                $stab = array('*');
            }
        }

        return new Version($vers, $stab, $metadata, $secondaryVersion, $secondaryVersionSeparator);
    }

    protected static function normalizeStability($stab)
    {
        $stab = strtolower($stab);
        if ($stab == 'a') {
            $stab = 'alpha';
        }
        if ($stab == 'b') {
            $stab = 'beta';
        }

        return $stab;
    }
}
