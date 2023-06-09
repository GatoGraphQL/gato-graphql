<?php

declare(strict_types=1);

namespace GatoGraphQL\GatoGraphQL;

use GatoGraphQL\ExternalDependencyWrappers\Composer\Semver\SemverWrapper;

use function get_file_data;

class PluginStaticHelpers
{
    /**
     * @var string[]|null
     */
    private static ?array $activeWordPressPluginFiles = null;
    /**
     * @var string[]|null
     */
    private static ?array $activeWordPressPluginSlugs = null;

    public static function getGitHubRepoDocsRootPathURL(): string
    {
        $mainPluginVersion = PluginApp::getMainPlugin()->getPluginVersion();
        $tag = str_ends_with($mainPluginVersion, '-dev')
            ? 'master'
            : $mainPluginVersion;
        return 'https://raw.githubusercontent.com/leoloso/PoP/' . $tag . '/layers/GatoGraphQLForWP/plugins/gato-graphql/';
    }

    /**
     * If param $pluginFileOrSlug ends with ".php", then it's the
     * plugin file, otherwise it's the plugin slug.
     */
    public static function isWordPressPluginActive(string $pluginFileOrSlug): bool
    {
        if (str_ends_with($pluginFileOrSlug, '.php')) {
            $pluginFile = $pluginFileOrSlug;
            return in_array($pluginFile, static::getActiveWordPressPluginFiles());
        }
        $pluginSlug = $pluginFileOrSlug;
        return in_array($pluginSlug, static::getActiveWordPressPluginSlugs());
    }

    /**
     * @return string[]
     */
    protected static function getActiveWordPressPluginFiles(): array
    {
        if (self::$activeWordPressPluginFiles === null) {
            self::$activeWordPressPluginFiles = get_option('active_plugins', []);
        }
        return self::$activeWordPressPluginFiles;
    }

    /**
     * @return string[]
     */
    protected static function getActiveWordPressPluginSlugs(): array
    {
        if (self::$activeWordPressPluginSlugs === null) {
            self::$activeWordPressPluginSlugs = array_map(
                function (string $pluginFile): string {
                    $pos = strpos($pluginFile, '/');
                    if ($pos === false) {
                        return $pluginFile;
                    }
                    return substr($pluginFile, 0, $pos);
                },
                static::getActiveWordPressPluginFiles()
            );
        }
        return self::$activeWordPressPluginSlugs;
    }

    public static function doesActivePluginSatisfyVersionConstraint(
        string $pluginFile,
        string $versionConstraint
    ): bool {
        $pluginDir = dirname(PluginApp::getMainPlugin()->getPluginDir());
        $pluginAbsolutePathFile = $pluginDir . '/' . $pluginFile;
        $pluginData = get_file_data($pluginAbsolutePathFile, array('Version'), 'plugin');
        $pluginVersion = $pluginData[0];
        if ($pluginVersion === '') {
            return false;
        }
        return SemverWrapper::satisfies($pluginVersion, $versionConstraint);
    }
}
