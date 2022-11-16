<?php

namespace App;

use App\Controllers\FileController;

class App
{
    public function __construct(protected Router $router, protected array $request)
    {
    }

    public function run()
    {
        try {
            echo $this->router->resolve($this->request['uri'], strtolower($this->request['method']));
        } catch (\Exception) {
            http_response_code(404);

            echo \App\View::make('error/404');
        }
    }
}
