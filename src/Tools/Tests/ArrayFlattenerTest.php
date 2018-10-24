<?php

namespace Tools\Tests;

use Datalog\Tools\ArrayFlattener;

class ArrayFlattenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function is_flattens()
    {
        $input = [
            'foo' => 'bar',
            'nested1' => [
                'foo' => 'bar',
                'nested2' => [
                    'foo' => 'bar',
                    'baz' => 'bazz',
                ],
            ],
        ];

        $expected = ['foo' => 'bar', 'nested1.foo' => 'bar', 'nested1.nested2.foo' => 'bar', 'nested1.nested2.baz' => 'bazz'];

        $this->assertEquals($expected, ArrayFlattener::getFlat($input));
    }

    /**
     * @test
     */
    public function can_return_flattened_key_value_string()
    {
        $input = [
            'foo' => 'bar',
            'nested1' => [
                'foo' => 'bar',
                'nested2' => [
                    'foo' => 'bar',
                    'baz' => 'bazz',
                ],
            ],
        ];

        $expected = 'foo="bar" nested1.foo="bar" nested1.nested2.foo="bar" nested1.nested2.baz="bazz"';

        $this->assertEquals($expected, ArrayFlattener::getFlatKeyValueString($input));
    }
}
