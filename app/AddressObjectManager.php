<?php

namespace App;

use Exception;
use Illuminate\Support\Arr;

/**
 * Class AddressObjectManager
 *
 * @package App
 */
class AddressObjectManager
{
    /**
     * @var string
     */
    public $fullAddressString;

    /**
     * @var array
     */
    public $addressInfo;

    /**
     * @var array
     */
    private $_address;

    /**
     * @var array
     */
    private $_houses;

    private $_additional = [];

    private $_addressObjectLevel = [
        'region'        => 1,
        'area'          => 3,
        'city'          => 4,
        'city_district' => 5,
        'settlement'    => 6,
        'street'        => 7,
        'house'         => 8,
    ];

    /**
     * AddressObjectManager constructor.
     *
     * @param array $address
     *
     * @throws Exception
     */
    public function __construct(array $address)
    {
        if (empty($address)) {
            throw new Exception('Empty address array');
        }

        if (!empty($address['houses'])) {
            $this->_houses = $address['houses'];
            unset($address['houses']);
        }
        else {
            $this->_houses = [];
        }

        $this->_address = $address;
        $this->_getFullAddressString();
        $this->_getAddressInfo();
        $this->_getHouseInfo();
        $this->_setNullTypes();
    }

    /**
     * @param $addressObject
     *
     * @return mixed
     */
    private function _getAddressObjectType($addressObject)
    {
        return FiasObjectType::where('level', $addressObject['aolevel'])
            ->where('scname', $addressObject['shortname'])
            ->first()
            ->toArray();
    }

    private function _getFullAddressString()
    {
        $address = [];
        $index = 0;

        foreach ($this->_address as $id => $addressObject) {
            $offName = $addressObject['offname'];
            $offNameEnding = mb_substr($offName, -2, 2);

            if ($offNameEnding === 'ая' && $index === 0) {
                $address[] = "{$addressObject['offname']} {$addressObject['shortname']}";
            }
            else {
                $address[] = "{$addressObject['shortname']} {$addressObject['offname']}";
            }

            $index++;
        }

        $this->fullAddressString = implode(', ', $address);
    }

    private function _getAddressInfo()
    {
        $addressObjectLevelFlipped = array_flip($this->_addressObjectLevel);

        foreach ($this->_address as $index => $addressObject) {
            $addressObject['type'] = $this->_getAddressObjectType($addressObject);
            $level = (int)$addressObject['aolevel'];

            if (isset($addressObjectLevelFlipped[$level])) {
                $paramPrefix = $addressObjectLevelFlipped[$level];
                $this->_setTypeInfo($paramPrefix, $addressObject);
            }
        }
    }

    /**
     * @param $house
     */
    private function _setHouseInfo($house)
    {
        $estateStatusQuery = FiasEstateStatus::where('eststatid', $house['eststatus'])->first()->toArray();
        $estateStatus = null;
        $estateStatusShort = null;
        $blockType = null;
        $blockTypeFull = null;
        $block = null;

        if (!empty($estateStatusQuery)) {
            $estateStatus = mb_strtolower($estateStatusQuery['name']);
            $estateStatusShort = mb_strtolower(mb_substr($estateStatus, 0, 1));
        }

        if (!empty($house['buildnum'])) {
            $blockType = 'к';
            $blockTypeFull = 'корпус';
            $block = $house['buildnum'];

            if (!empty($house['strucnum'])) {
                $block .= " стр {$house['strucnum']}";
            }
        }
        elseif (!empty($house['strucnum'])) {
            $blockType = 'с';
            $blockTypeFull = 'строение';
            $block = $house['strucnum'];
        }

        $this->addressInfo['houses'][] = [
            'postal_code'     => $house['postalcode'],
            'house_fias_id'   => $house['houseguid'],
            'house_type'      => $estateStatusShort,
            'house_type_full' => $estateStatus,
            'house'           => $house['housenum'],
            'block'           => $block,
            'block_type'      => $blockType,
            'block_type_full' => $blockTypeFull,
        ];
    }

    private function _getHouseInfo()
    {
        $aoguIds = Arr::pluck($this->_address, 'aoguid');

        foreach ($this->_houses as $index => $house) {
            if (in_array($house['aoguid'], $aoguIds)) {
                $this->_setHouseInfo($house);
            }
        }
    }

    /**
     * @param $paramPrefix
     * @param $addressObject
     */
    private function _setTypeInfo($paramPrefix, $addressObject)
    {
        $this->addressInfo['postal_code'] = $addressObject['postalcode'];
        $this->addressInfo["{$paramPrefix}_fias_id"] = $addressObject['aoguid'];
        $this->addressInfo["{$paramPrefix}_type"] = $addressObject['shortname'];
        $this->addressInfo["{$paramPrefix}_type_full"] = $addressObject['type']['socrname'];
        $this->addressInfo["{$paramPrefix}"] = $addressObject['offname'];
    }

    private function _setNullTypes()
    {
        foreach ($this->_addressObjectLevel as $levelName => $level) {
            if (!isset($this->addressInfo[$levelName])) {
                $this->addressInfo["{$levelName}_fias_id"] = null;
                $this->addressInfo["{$levelName}_type"] = null;
                $this->addressInfo["{$levelName}_type_full"] = null;
                $this->addressInfo["{$levelName}"] = null;
            }
        }

        if (!isset($this->addressInfo['block'])) {
            $this->addressInfo["block"] = null;
            $this->addressInfo["block_type"] = null;
            $this->addressInfo["block_type_full"] = null;
        }
    }
}
