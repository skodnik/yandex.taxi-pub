<?php

namespace Unit;

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testIsJsonTrue()
    {
        $json_string = '{"id":"z87b387476d1402d857a4c6ed2827235","zone_name":"krasnogorsk","skip_estimated_waiting":true,"supports_forced_surge":true,"format_currency":true,"extended_description":true,"route":[[37.3970799752,55.8679988326],[37.435238,55.772152]],"requirements":{"nosmoking":true}}';

        $this->assertTrue(isJson($json_string));
    }

    public function testIsJsonFalse()
    {
        $json_string = '"id":"z87b387476d1402d857a4c6ed2827235","zone_name":"krasnogorsk","skip_estimated_waiting":true,"supports_forced_surge":true,"format_currency":true,"extended_description":true,"route":[[37.3970799752,55.8679988326],[37.435238,55.772152]],"requirements":{"nosmoking":true}}';

        $this->assertFalse(isJson($json_string));
    }

    public function testIdGenerator()
    {
        $this->assertEquals(32, strlen(makeId()));
    }
}