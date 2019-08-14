<?php


namespace ZhangDi\SdkKernel\Support;


use ArrayIterator;
use Countable;
use IteratorAggregate;

class Collection implements Countable, IteratorAggregate
{
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function count()
    {
        return count($this->items);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     */
    public function get($key, $defaultValue = null)
    {
        if (!$this->has($key)) {
            return $defaultValue;
        }
        return $this->items[$key];
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->items[$key]);
    }

}