<?php

namespace Vangarde\Katana;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Compilers\Concerns\CompilesLayouts;

/**
 * @see BladeCompiler
 */
class KatanaCompiler extends BladeCompiler
{
    use CompilesLayouts;

    /**
     * @var string
     */
    protected $model;

    public function __construct(Filesystem $files, $cachePath)
    {
        parent::__construct($files, $cachePath);
        $this->model = config('katana.model.class');
    }

    /**
     * Compile the view at the given path.
     *
     * @param string|null $path
     * @return void
     * @throws ModelNotFoundException
     */
    public function compile($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        if (!is_null($this->cachePath)) {
            $fileContent = '';
            try {
                $fileContent = $this->files->get($this->getPath());
            } catch (FileNotFoundException $exception) {
                $fileContent = $this->model::where(
                    config('katana.model.templateNameColumn'), '=', $this->getTemplateName($this->path)
                )->firstOrFail()->{config('katana.model.contentField')};
            }
            $contents = $this->compileString($fileContent);

            if (!empty($this->getPath())) {
                $contents = $this->appendFilePath($contents);
            }

            $this->files->put(
                $this->getCompiledPath($this->getPath()), $contents
            );
        }
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param string $path
     * @return bool
     */
    public function isExpired($path): bool
    {
        $compiled = $this->getCompiledPath($path);
        if (!$this->files->exists($compiled)) {
            return true;
        }

        if ($this->files->exists($path)) {
            return $this->files->lastModified($path) >=
                $this->files->lastModified($compiled);
        } else {
            return $this->modelViewLastModified($this->getTemplateName($path)) >=
                $this->files->lastModified($compiled);
        }
    }

    /**
     * Checks last modified date of template in Database
     *
     * @param string $path
     * @return int
     */
    private function modelViewLastModified(string $path): int
    {
        $lastModified = $this->model::where(config('katana.model.templateNameColumn'), '=', $path)
            ->firstOrFail()
            ->updated_at;
        if (is_null($lastModified))
            return Carbon::now()->timestamp;
        return $lastModified->timestamp;
    }

    /**
     * Returns the name of a blade template
     *
     * @param string $path
     * @return string
     */
    private function getTemplateName(string $path): string
    {
        $d = [];
        $names = explode('.', $path);
        for ($i = 0; $i < count($names) - 2; $i++) {
            array_push($d, $names[$i]);
        }
        return implode('.', $d);
    }
}