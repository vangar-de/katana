<?php

namespace \Vangarde\Katana;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;

class ModelViewFinder extends FileViewFinder
{
    /**
     * @var string
     */
    protected $model;

    public function __construct(Filesystem $files, array $paths, array $extensions = null)
    {
        parent::__construct($files, $paths, $extensions);
        $this->model = config('katana.model.class');
    }

    /**
     * @param string $name
     * @param array $paths
     * @throws ModelNotFoundException
     * @return string
     */
    protected function findInPaths($name, $paths)
    {
        try {
            return parent::findInPaths($name, $paths);
        } catch (\InvalidArgumentException $e) {
            return $this->model::where(config('katana.model.templateNameColumn'), '=', $name)
                    ->firstOrFail()
                    ->{config('katana.model.templateNameColumn')} . '.blade.php';
        }
    }
}
