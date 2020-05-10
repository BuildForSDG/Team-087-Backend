<?php

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    /**
     * @test
     * @testdox SampleTestCase
     */
    public function sampleTestCase()
    {
        $this->assertIsBool(true);
    }
}
