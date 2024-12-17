<?php

namespace ComposerPatchClone\Tests;

use ComposerPatchClone\Exceptions\PatchException;
use PHPUnit\Framework\TestCase;

class PatchExceptionTest extends TestCase
{
    public function testPatchExceptionMessage()
    {
        $exception = new PatchException("Test exception message");
        $this->assertSame("Test exception message", $exception->getMessage());
    }

    public function testPatchExceptionToString()
    {
        $exception = new PatchException("Test exception", 123);
        $this->assertStringContainsString('[123]: Test exception', (string)$exception);
    }
}
