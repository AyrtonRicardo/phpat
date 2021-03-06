<?php

declare(strict_types=1);

namespace PhpAT\Selector;

use PhpAT\File\FileFinder;
use PhpAT\Parser\Ast\ReferenceMap;
use PhpAT\Parser\Ast\ClassLike;
use PhpAT\Parser\Ast\FullClassName;
use PhpParser\Parser;

/**
 * Class PathSelector
 *
 * @package PhpAT\Selector
 */
class PathSelector implements SelectorInterface
{
    private const DEPENDENCIES = [
        FileFinder::class,
        Parser::class
    ];

    /**
     * @var string
     */
    private $path;
    /**
     * @var FileFinder
     */
    private $fileFinder;
    /**
     * @var ReferenceMap
     */
    private $map;
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getDependencies(): array
    {
        return self::DEPENDENCIES;
    }

    public function injectDependencies(array $dependencies): void
    {
        $this->fileFinder = $dependencies[FileFinder::class];
        $this->parser = $dependencies[Parser::class];
    }

    /**
     * @param ReferenceMap $map
     */
    public function setReferenceMap(ReferenceMap $map): void
    {
        $this->map = $map;
    }

    /**
     * @return ClassLike[]
     */
    public function select(): array
    {
        foreach ($this->fileFinder->findSrcFiles($this->path) as $file) {
            $filePathname = str_replace('\\', '/', $file->getPathname());

            foreach ($this->map->getSrcNodes() as $srcNode) {
                if ($srcNode->getFilePathname() === $filePathname) {
                    $result[] = FullClassName::createFromFQCN($srcNode->getClassName());
                }
            }
        }

        return $result ?? [];
    }

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->path;
    }
}
