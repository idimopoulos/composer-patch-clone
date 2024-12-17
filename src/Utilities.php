<?php

namespace ComposerPatchClone;

use ComposerPatchClone\Exceptions\PatchException;

class Utilities
{
    /**
     * Validate if the provided URL is well-formed and reachable.
     *
     * @param string $uri
     * @return void
     * @throws PatchException
     */
    public static function validateUri(string $uri): void
    {
        if (!filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new PatchException("Invalid URL format: $uri");
        }

        $headers = @get_headers($uri);
        if (!$headers || strpos($headers[0], '200') === false) {
            throw new PatchException("Unable to access the URL: $uri");
        }
    }

    /**
     * Ensure the output directory exists, and create it if not.
     *
     * @param string $directory
     * @return void
     */
    public static function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    /**
     * Download the patch file from the provided URI.
     *
     * @param string $uri
     * @param string $savePath
     * @return void
     * @throws PatchException
     */
    public static function downloadFile(string $uri, string $savePath): void
    {
        $content = @file_get_contents($uri);
        if ($content === false) {
            throw new PatchException("Failed to download the patch from: $uri");
        }

        if (file_put_contents($savePath, $content) === false) {
            throw new PatchException("Failed to save the patch to: $savePath");
        }
    }

    /**
     * Parse command-line arguments to extract key-value options (e.g., --output-name=value).
     *
     * @param array $args
     * @param string $option
     * @return string|null
     */
    public static function extractOption(array $args, string $option): ?string
    {
        foreach ($args as $arg) {
            if (strpos($arg, "--{$option}=") === 0) {
                return substr($arg, strlen("--{$option}="));
            }
        }
        return null;
    }

    /**
     * Update composer.json with a new patch entry.
     *
     * @param string $package
     * @param string $title
     * @param string $filePath
     * @return void
     * @throws PatchException
     */
    public static function updateComposerJson(string $package, string $title, string $filePath): void
    {
        $composerJsonPath = 'composer.json';

        if (!file_exists($composerJsonPath)) {
            throw new PatchException("composer.json file not found.");
        }

        $composerData = json_decode(file_get_contents($composerJsonPath), true);
        if ($composerData === null) {
            throw new PatchException("Failed to parse composer.json.");
        }

        // Ensure 'extra > patches' structure exists
        $composerData['extra']['patches'] = $composerData['extra']['patches'] ?? [];
        $composerData['extra']['patches'][$package] = $composerData['extra']['patches'][$package] ?? [];

        // Add or update the patch entry
        $composerData['extra']['patches'][$package][$title] = $filePath;

        // Write back to composer.json
        if (file_put_contents($composerJsonPath, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
            throw new PatchException("Failed to update composer.json.");
        }
    }
}
