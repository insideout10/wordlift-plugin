<?php

/*
 * This file is part of the "composer-exclude-files" plugin.
 *
 * © Chauncey McAskill <chauncey@mcaskill.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Wordlift\Modules\Common\McAskill\Composer;

use Wordlift\Modules\Common\Composer\Composer;
use Wordlift\Modules\Common\Composer\EventDispatcher\EventSubscriberInterface;
use Wordlift\Modules\Common\Composer\IO\IOInterface;
use Wordlift\Modules\Common\Composer\Package\PackageInterface;
use Wordlift\Modules\Common\Composer\Plugin\PluginInterface;
use Wordlift\Modules\Common\Composer\Script\ScriptEvents;
use Wordlift\Modules\Common\Composer\Util\Filesystem;
class ExcludeFilePlugin implements PluginInterface, EventSubscriberInterface
{
    const INCLUDE_FILES_PROPERTY = 'files';
    const EXCLUDE_FILES_PROPERTY = 'exclude-from-files';
    /**
     * @var Composer
     */
    private $composer;
    /**
     * Apply plugin modifications to Composer.
     *
     * @param  Composer    $composer The Composer instance.
     * @param  IOInterface $io       The Input/Output instance.
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
    }
    /**
     * Remove any hooks from Composer.
     *
     * @codeCoverageIgnore
     *
     * @param  Composer    $composer The Composer instance.
     * @param  IOInterface $io       The Input/Output instance.
     * @return void
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
        // no need to deactivate anything
    }
    /**
     * Prepare the plugin to be uninstalled.
     *
     * @codeCoverageIgnore
     *
     * @param  Composer    $composer The Composer instance.
     * @param  IOInterface $io       The Input/Output instance.
     * @return void
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
        // no need to uninstall anything
    }
    /**
     * Gets a list of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(ScriptEvents::PRE_AUTOLOAD_DUMP => 'parseAutoloads');
    }
    /**
     * Parse the vendor 'files' to be included before the autoloader is dumped.
     *
     * Note: The double realpath() calls fixes failing Windows realpath() implementation.
     * See https://bugs.php.net/bug.php?id=72738
     * See \Composer\Autoload\AutoloadGenerator::dump()
     *
     * @return void
     */
    public function parseAutoloads()
    {
        $composer = $this->composer;
        $package = $composer->getPackage();
        if (!$package) {
            return;
        }
        $excludedFiles = $this->parseExcludedFiles($this->getExcludedFiles($package));
        if (!$excludedFiles) {
            return;
        }
        $generator = $composer->getAutoloadGenerator();
        $packages = $composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();
        $packageMap = $generator->buildPackageMap($composer->getInstallationManager(), $package, $packages);
        $this->filterAutoloads($packageMap, $package, $excludedFiles);
    }
    /**
     * Alters packages to exclude files required in "autoload.files" by "extra.exclude-from-files".
     *
     * @param  array            $packageMap    Array of `[ package, installDir-relative-to-composer.json) ]`.
     * @param  PackageInterface $mainPackage   Root package instance.
     * @param  string[]         $excludedFiles The files to exclude from the "files" autoload mechanism.
     * @return void
     */
    private function filterAutoloads(array $packageMap, PackageInterface $mainPackage, array $excludedFiles)
    {
        $excludedFiles = array_flip($excludedFiles);
        $type = self::INCLUDE_FILES_PROPERTY;
        foreach ($packageMap as $item) {
            list($package, $installPath) = $item;
            // Skip root package
            if ($package === $mainPackage) {
                continue;
            }
            $autoload = $package->getAutoload();
            // Skip misconfigured packages
            if (!isset($autoload[$type]) || !is_array($autoload[$type])) {
                continue;
            }
            if (null !== $package->getTargetDir()) {
                $installPath = substr($installPath, 0, -strlen('/' . $package->getTargetDir()));
            }
            foreach ($autoload[$type] as $key => $path) {
                if ($package->getTargetDir() && !is_readable($installPath . '/' . $path)) {
                    // add target-dir from file paths that don't have it
                    $path = $package->getTargetDir() . '/' . $path;
                }
                $resolvedPath = $installPath . '/' . $path;
                $resolvedPath = strtr($resolvedPath, '\\', '/');
                if (isset($excludedFiles[$resolvedPath])) {
                    unset($autoload[$type][$key]);
                }
            }
            $package->setAutoload($autoload);
        }
    }
    /**
     * Gets a list files the root package wants to exclude.
     *
     * @param  PackageInterface $package Root package instance.
     * @return string[] Retuns the list of excluded files otherwise NULL if misconfigured or undefined.
     */
    private function getExcludedFiles(PackageInterface $package)
    {
        $type = self::EXCLUDE_FILES_PROPERTY;
        $extra = $package->getExtra();
        if (isset($extra[$type]) && is_array($extra[$type])) {
            return $extra[$type];
        }
        return array();
    }
    /**
     * Prepends the vendor directory to each path in "extra.exclude-from-files".
     *
     * @param  string[] $paths Array of paths relative to the composer manifest.
     * @return string[] Retuns the array of paths, prepended with the vendor directory.
     */
    private function parseExcludedFiles(array $paths)
    {
        if (empty($paths)) {
            return $paths;
        }
        $filesystem = new Filesystem();
        $config = $this->composer->getConfig();
        $vendorPath = $filesystem->normalizePath(realpath(realpath($config->get('vendor-dir'))));
        foreach ($paths as &$path) {
            $path = preg_replace('{/+}', '/', trim(strtr($path, '\\', '/'), '/'));
            $path = $vendorPath . '/' . $path;
        }
        return $paths;
    }
}
