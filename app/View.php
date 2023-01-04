<?php

declare(strict_types=1);

namespace App;

class View
{
    public function __construct(protected string $view)
    {
    }

    public static function make(string $view): static
    {
        return new static($view);
    }

    public static function createViewPath(string $viewName): string
    {
        return __DIR__ . '/../views' . '/' . $viewName . '.php';
    }

    public function render(): string
    {
        $viewpath = self::createViewPath($this->view);

        try {
            if (! file_exists($viewpath)) {
                throw new \Exception('View not found');
            }

            ob_start();

            include $viewpath;

            return (string) ob_get_clean();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function __toString()
    {
        return $this->render();
    }
}
