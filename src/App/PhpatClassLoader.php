<?php

declare(strict_types=1);

namespace PhpAT\App;

use Composer\Autoload\ClassLoader;

class PhpatClassLoader
{
    /** @var string */
    private $vendorPath;
    /** @var ClassLoader */
    private $classLoader;

    public function __construct(ClassLoader $loader, string $autoloadFile)
    {
        $this->vendorPath = $this->extractVendorPath($autoloadFile);
        $this->classLoader = $loader;
    }

    /**
     * @return string
     */
    public function getVendorPath(): string
    {
        return $this->vendorPath;
    }

    public function getClassMap(): array
    {
        return array_map(
            function (string $value) {
                return realpath($value);
            },
            $this->classLoader->getClassMap()
        );
    }

    private function extractVendorPath(string $autoloadFile)
    {
        $path = substr($autoloadFile, 0, strrpos($autoloadFile,'/autoload.php'));

        return realpath($path);
    }
}
