<?php

namespace ComposerPatchClone\Tests;

use ComposerPatchClone\Exceptions\PatchException;
use ComposerPatchClone\Utilities;
use PHPUnit\Framework\TestCase;

class UtilitiesTest extends TestCase
{
    public function testValidateUriValid()
    {
        $this->expectNotToPerformAssertions();
        Utilities::validateUri("https://example.com");
    }

    public function testValidateUriInvalid()
    {
        $this->expectException(PatchException::class);
        Utilities::validateUri("invalid-url");
    }

    public function testEnsureDirectoryExists()
    {
        $testDir = __DIR__ . '/tmp/test-dir';
        Utilities::ensureDirectoryExists($testDir);
        $this->assertTrue(is_dir($testDir));
        rmdir($testDir);
    }

    public function testDownloadFile()
    {
        $testFile = __DIR__ . '/tmp/test-file.txt';
        $url = "https://raw.githubusercontent.com/github/gitignore/main/README.md"; // A valid small file
        Utilities::ensureDirectoryExists(__DIR__ . '/tmp');

        Utilities::downloadFile($url, $testFile);
        $this->assertFileExists($testFile);
        unlink($testFile);
    }

    public function testDownloadFileInvalidUrl()
    {
        $this->expectException(PatchException::class);
        Utilities::downloadFile("https://invalid-url.test/file.txt", __DIR__ . "/tmp/test.txt");
    }
}
