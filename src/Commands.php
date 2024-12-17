<?php

namespace ComposerPatchClone;

use Composer\Script\Event;
use ComposerPatchClone\Exceptions\PatchException;

class Commands
{
    /**
     * Handles the patch-get command
     *
     * @param Event $event
     * @return void
     * @throws PatchException
     */
    public static function clonePatch(Event $event)
    {
        // Retrieve command arguments
        $args = $event->getArguments();

        if (count($args) < 3) {
            throw new PatchException("Usage: composer patch-get <package> <title> <uri> [--output-name=<name>]");
        }

        list($package, $title, $uri) = $args;

        // Extract optional --output-name argument
        $outputName = self::extractOutputName($args);

        // Validate URI
        if (!filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new PatchException("The provided URI is not valid: $uri");
        }

        // Determine output directory
        $config = self::getComposerConfig();
        $outputDir = $config['extra']['composer-patch-clone']['output-directory'] ?? 'resources/patch';
        $savePath = self::buildSavePath($outputDir, $package, $uri, $outputName);

        // Download and save the patch
        echo "Downloading patch...\n";
        self::downloadPatch($uri, $savePath);

        // Update composer.json with the new patch entry
        self::updateComposerJson($package, $title, $savePath);
        echo "Patch added successfully to composer.json.\n";
    }

    private static function extractOutputName($args)
    {
        foreach ($args as $arg) {
            if (strpos($arg, '--output-name=') === 0) {
                return substr($arg, strlen('--output-name='));
            }
        }
        return null;
    }

    private static function getComposerConfig()
    {
        $composerJson = file_get_contents('composer.json');
        return json_decode($composerJson, true);
    }

    private static function buildSavePath($outputDir, $package, $uri, $outputName)
    {
        $packageDir = $outputDir . '/' . str_replace('/', DIRECTORY_SEPARATOR, $package);
        if (!is_dir($packageDir)) {
            mkdir($packageDir, 0777, true);
        }

        $fileName = $outputName ?: basename(parse_url($uri, PHP_URL_PATH));
        return $packageDir . '/' . $fileName;
    }

    private static function downloadPatch($uri, $savePath)
    {
        $content = @file_get_contents($uri);
        if ($content === false) {
            throw new PatchException("Failed to download the patch from: $uri");
        }

        file_put_contents($savePath, $content);
    }

    private static function updateComposerJson($package, $title, $savePath)
    {
        $composerJson = self::getComposerConfig();

        if (!isset($composerJson['extra']['patches'])) {
            $composerJson['extra']['patches'] = [];
        }

        if (!isset($composerJson['extra']['patches'][$package])) {
            $composerJson['extra']['patches'][$package] = [];
        }

        // Add or override the patch entry
        $composerJson['extra']['patches'][$package][$title] = $savePath;

        // Save back to composer.json
        file_put_contents('composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
