<?php

namespace repository;

abstract class AbstractRepository
{
    /**
     * @var array
     */
    protected $itemsList = [];

    /**
     * После удаления элементов не обновляются индексы списка, делаем это вручную
     * @return void
     */
    public function updateListKeys()
    {
        $temp = [];
        foreach ($this->itemsList as $item) {
            $temp[] = $item;
        }

        $this->itemsList = $temp;
    }

    /**
     * @param $data
     * @return array|null
     */
    public static function toArray($data): ?array
    {
        if (is_object($data) && method_exists($data, 'toArray')) {
            return $data->toArray();
        } else if (is_array($data)) {
            foreach ($data as $key => $item) {
                $data[$key] = self::toArray($item);
            }
        }

        return $data;
    }
}