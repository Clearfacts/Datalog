<?php

namespace Tools\Tests;

use Datalog\Tools\ArrayFlattener;

class ArrayFlattenerTest extends \PHPUnit\Framework\TestCase
{
    public function testFlattens()
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

    public function testReturnsFlattenedKeyKalueString()
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
