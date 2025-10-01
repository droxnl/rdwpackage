<?php

namespace DroxNL\Rdw;

use Illuminate\Contracts\Foundation\Application;

class Rdw
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param $license
     * @param array $types
     * @return $this
     */
    public function find($license, array $types = ['info'])
    {
        return (new RdwApi)->find($license, $types);
    }
}
