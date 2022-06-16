<?php

namespace Tests\Tools;

use Datalog\Tools\ArrayFlattener;
use PHPUnit\Framework\TestCase;

class ArrayFlattenerTest extends TestCase
{
    /** @test */
    public function is_flattens(): void
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

    /** @test */
    public function can_return_flattened_key_value_string(): void
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
