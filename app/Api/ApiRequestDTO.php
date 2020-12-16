<?php

namespace App\Api;

use Exception;
use Illuminate\Support\Str;

/**
 * Class ApiRequestDTO
 *
 * @package App
 */
class ApiRequestDTO
{
    /**
     * Поисковая строка
     *
     * @var string
     */
    public $query = '';

    /**
     * Количество возвращаемых записей
     *
     * @var int
     */
    public $count = 10;

    /**
     * Часть адреса, с которой начинать поиск
     *
     * @var array
     */
    public $fromBound = [];

    /**
     * Часть адреса, до которой искать
     *
     * @var array
     *
     */
    public $toBound = [];

    private $availableBounds = [
        'country',
        'region',
        'area',
        'city',
        'settlement',
        'street',
        'house',
    ];

    /**
     * @var array
     */
    public $nums;

    /**
     * @throws Exception
     */
    private function _checkTypes()
    {
        if (empty($this->query)) {
            throw new Exception('query: Обязательный параметр');
        }

        if (!is_string($this->query)) {
            throw new Exception('query: Некорректный тип параметра');
        }

        if (!empty($this->fromBound)) {
            if (!is_array($this->fromBound)) {
                throw new Exception('from_bound: Некорректный тип параметра');
            }

            if (empty($this->fromBound['value'])) {
                throw new Exception('from_bound: Пропущено обязательное поле value');
            }

            if (!$this->isAvailableBound($this->fromBound['value'])) {
                throw new Exception(
                    'from_bound: Некорректное значение value. Разрешенные значения: ' . $this->availableBoundsStr()
                );
            }
        }

        if (!empty($this->toBound)) {
            if (!is_array($this->toBound)) {
                throw new Exception('to_bound: Некорректный тип параметра');
            }

            if (empty($this->toBound['value'])) {
                throw new Exception('to_bound: Пропущено обязательное поле value');
            }

            if (!$this->isAvailableBound($this->toBound['value'])) {
                throw new Exception(
                    'to_bound: Некорректное значение value. Разрешенные значения: ' . $this->availableBoundsStr()
                );
            }
        }

        if (!empty($this->count)) {
            if (!is_int($this->count)) {
                throw new Exception('count: Некорректный тип параметра');
            }
        }
    }

    /**
     * @return string
     */
    private function availableBoundsStr()
    {
        return implode(', ', $this->availableBounds);
    }

    /**
     * @param $boundValue
     *
     * @return bool
     */
    private function isAvailableBound($boundValue)
    {
        return in_array($boundValue, $this->availableBounds);
    }

    /**
     *
     */
    private function _prepareQuery()
    {
        $parsedData = ApiDataQueryParser::execute($this->query);
        $this->query = preg_replace("|[\s\t]+|iu", '|', $parsedData['query']);
        $this->nums = $parsedData['nums'];
    }

    /**
     * @param array $attrs
     *
     * @return ApiRequestDTO
     * @throws Exception
     */
    public static function create(array $attrs = [])
    {
        $self = new self();

        if (empty($attrs)) {
            throw new Exception('Не передано ни одного аргумента');
        }

        foreach ($attrs as $name => $value) {
            $name = Str::camel($name);

            if (isset($self->$name)) {
                $self->$name = $value;
            }
        }

        $self->_checkTypes();
        $self->_prepareQuery();

        return $self;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'query'     => $this->query,
            'fromBound' => $this->fromBound,
            'toBound'   => $this->toBound,
        ];
    }
}
