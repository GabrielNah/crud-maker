<?php

namespace Kaysr\CrudMaker;

use \Illuminate\Support\ServiceProvider;

class CrudMakerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
            $this->commands([
                CrudGenerator::class
            ]);
    }
}
