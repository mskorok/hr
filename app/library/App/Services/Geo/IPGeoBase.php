<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 03.05.19
 * Time: 18:26
 */

namespace App\Services\Geo;

/**
 * Class IPGeoBase
 * @package App\Services\Geo
 */
class IPGeoBase
{
    private $fHandleCIDR, $fHandleCities, $fSizeCIDR, $fSizeCities;

    /**
     * @brief
     *
     * @param string cidrFile  (cidr_optim.txt)
     * @param string citiesFile  (cities.txt)
     */
    public function __construct(string $cidrFile = null, string $citiesFile = null)
    {
        if (!$cidrFile) {
            $cidrFile = __DIR__ . '/cidr_optim.txt';
        }
        if (!$citiesFile) {
            $citiesFile = __DIR__ . '/cities.txt';
        }
        $this->fHandleCIDR = fopen($cidrFile, 'rb') or die("Cannot open $cidrFile");
        $this->fHandleCities = fopen($citiesFile, 'rb') or die("Cannot open $citiesFile");
        $this->fSizeCIDR = filesize($cidrFile);
        $this->fSizeCities = filesize($citiesFile);
    }

    /**
     * @brief
     * @param string idx
     * @return bool |array
     */
    private function getCityByIdx($idx)
    {
        rewind($this->fHandleCities);
        while (!feof($this->fHandleCities)) {
            $str = fgets($this->fHandleCities);
            $arRecord = explode("\t", trim($str));
            if ($arRecord[0] === $idx) {
                return ['city' => $arRecord[1],
                    'region' => $arRecord[2],
                    'district' => $arRecord[3],
                    'lat' => $arRecord[4],
                    'lng' => $arRecord[5]];
            }
        }
        return false;
    }

    /**
     * @brief
     * @param mixed $ip IPv4
     * @return bool | array
     */
    public function getRecord($ip)
    {
        $ip = sprintf('%u', ip2long($ip));

        rewind($this->fHandleCIDR);
        $rad = floor($this->fSizeCIDR / 2);
        $pos = $rad;
        while (fseek($this->fHandleCIDR, $pos, SEEK_SET) !== -1) {
            if ($rad) {
                $str = fgets($this->fHandleCIDR);
            } else {
                rewind($this->fHandleCIDR);
            }

            $str = fgets($this->fHandleCIDR);

            if (!$str) {
                return false;
            }

            $arRecord = explode("\t", trim($str));

            $rad = floor($rad / 2);
            if (!$rad && ($ip < $arRecord[0] || $ip > $arRecord[1])) {
                return false;
            }

            if ($ip < $arRecord[0]) {
                $pos -= $rad;
            } elseif ($ip > $arRecord[1]) {
                $pos += $rad;
            } else {
                $result = ['range' => $arRecord[2], 'cc' => $arRecord[3]];

                if ($arRecord[4] !== '-' && $cityResult = $this->getCityByIdx($arRecord[4])) {
                    $result += $cityResult;
                }

                return $result;
            }
        }
        return false;
    }
}