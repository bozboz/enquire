<?php

namespace Bozboz\Enquire\Forms\Fields;

class FieldMapper
{
    protected $mapping;

    public function register($aliasOrArray, $class = null)
    {
        if (is_array($aliasOrArray)) {
            foreach ($aliasOrArray as $alias => $class) {
                $class->input_type = $alias;
                $this->register($alias, $class);
            }
        } else {
            $class->input_type = $aliasOrArray;
            $this->mapping[$aliasOrArray] = $class;
        }
    }

    public function has($alias)
    {
        return array_key_exists($alias, $this->mapping);
    }

    public function get($type_alias)
    {
        $mapping = $this->mapping[$type_alias];

        $model = $mapping->replicate();
        $model->input_type = $type_alias;
        $model->setView($mapping->getView());

        return $model;
    }

    public function getAll($filterClass = null)
    {
        return collect($this->mapping)->each(function($map, $alias) {
            if (!is_string($map)) {
                $map->input_type = $alias;
            }
            return $map;
        })->filter(function($item) use ($filterClass) {
            if ( ! $filterClass) {
                return true;
            }

            if (is_object($item)) {
                $class = get_class($item);
            } else {
                $class = $item;
            }
            return $class === $filterClass;
        })->sort();
    }
}
