<?php

namespace AZ\Helpers;

use Exception;


/**
 * 
 */
class Arrays
{


    /**
     * 
     *
     * @var array
     */
    private array $data;


    /**
     *
     * @param array $data
     */
    public function __construct(array $data)
    {

        $this->data = $data;
    }


    /**
     *
     * @return array
     */
    public function getData(): array
    {

        return $this->data;
    }

    /**
     * Convert stdClass to array
     *
     * @return array
     */
    public function asArray(): array
    {

        return \json_decode(\json_encode($this->data), true);
    }


    /**
     * Map array
     *
     * @param string $field
     * @param string|null $key
     * @param boolean $unique
     * @return array
     */
    public function map(string $field, ?string $key = null, bool $unique = false): array
    {

        $result = [];

        foreach ($this->data as $row) {

            if (!\is_array($row)) {

                $row = (array)$row;
            }

            if (!isset($row[$field])) {

                throw new Exception("Field not found: '{$field}'", 1);
            }

            if ($key) {

                if (!isset($row[$field])) {

                    throw new Exception("Field not found: '{$key}'", 1);
                }

                $result[$key] = $row[$field];
            } else {

                $result[] = $row[$field];
            }
        }

        if ($unique || $key === null) {

            $result = \array_unique($result);

        }

        return $result;
    }
}
