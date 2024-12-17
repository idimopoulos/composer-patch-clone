<?php

namespace ComposerPatchClone\Tests;

use Composer\Script\Event;
use ComposerPatchClone\Commands;
use ComposerPatchClone\Exceptions\PatchException;
use PHPUnit\Framework\TestCase;

class CommandsTest extends TestCase
{

    private $composerJsonContents;

    private $composerMock;

    protected function setUp(): void
    {
        $this->composerMock = $this->createMock(Event::class);
        $this->composerMock
            ->method('getArguments')
            ->willReturn([
                'test/package',
                'Add custom feature',
                'https://raw.githubusercontent.com/github/gitignore/main/README.md',
                '--output-name=test-patch.diff'
            ]);
    }

    public function testclonePatchSuccess()
    {
        $this->expectOutputRegex('/Patch added successfully/');
        $this->setupComposerJsonForTest();

        Commands::clonePatch($this->composerMock);

        $patchPath = __DIR__ . '/tmp/patch/test/package/test-patch.diff';
        $this->assertFileExists($patchPath);

        unlink($patchPath);
        rmdir(dirname($patchPath));
    }

    public function testclonePatchInvalidUri()
    {
        $this->expectException(PatchException::class);

        // Mocking the Event object with specific arguments
        $eventMock = $this->createMock(Event::class);
        $eventMock->method('getArguments')
            ->willReturn([
                'test/package',
                'Invalid URL Test',
                'invalid-url' // Passing an invalid URL for this test
            ]);

        // Call the clonePatch method and expect it to throw an exception
        Commands::clonePatch($eventMock);
    }

    protected function setupComposerJsonForTest()
    {
        $composerJson = file_get_contents('composer.json');
        $this->composerJsonContents = $composerJson;

        $composerJson = json_decode($composerJson, true);
        $composerJson['extra']['composer-patch-clone']['output-directory'] = 'tests/tmp/patch';

        file_put_contents('composer.json', json_encode($composerJson));
    }

    /**
     * Restore the original composer.json contents after each test.
     */
    protected function tearDown(): void
    {
        file_put_contents('composer.json', $this->composerJsonContents);
    }
}
