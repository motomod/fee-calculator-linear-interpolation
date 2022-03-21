<?php

declare(strict_types=1);

namespace App\Interpolation\Tests\Storage;

use App\Interpolation\Storage\Exception\UnableToLocateResourceException;
use App\Interpolation\Storage\JsonFile;
use PHPUnit\Framework\TestCase;

class JsonFileTest extends TestCase
{
    public function testResourceFound()
    {
        $jsonFile = $this->getMockBuilder(JsonFile::class)
            ->setConstructorArgs(['something'])
            ->onlyMethods(['fileGetContents'])
            ->getMock();

        $jsonFile->expects(self::once())->method('fileGetContents')->willReturn("{\"1000\": 145}");

        $this->assertEquals(["1000" => 145], $jsonFile->fetch('something'));
    }

    public function testResourceNotFound(): void
    {
        $jsonFile = $this->getMockBuilder(JsonFile::class)
            ->setConstructorArgs(['something'])
            ->onlyMethods(['fileGetContents'])
            ->getMock();

        $jsonFile->expects(self::once())->method('fileGetContents')->willReturn(false);

        $this->expectException(UnableToLocateResourceException::class);

        $jsonFile->fetch('something');
    }

    public function testResourceNotJson(): void
    {
        $jsonFile = $this->getMockBuilder(JsonFile::class)
            ->setConstructorArgs(['something'])
            ->onlyMethods(['fileGetContents'])
            ->getMock();

        $jsonFile->expects(self::once())->method('fileGetContents')->willReturn("notJson");

        $this->expectException(\JsonException::class);
        $this->expectExceptionMessage("Syntax error");

        $this->assertEquals(["1000" => 145], $jsonFile->fetch('something'));
    }
}
