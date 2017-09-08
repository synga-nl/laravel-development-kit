<?php
namespace Synga\LaravelDevelopment\Database\Seeder;

trait ForwardHandleMethod
{
    public function handle()
    {
        $forwardMethod = 'run';

        if(property_exists($this, 'forwardMethod')){
            $forwardMethod = $this->forwardMethod;
        }

        call_user_func_array([$this, $forwardMethod], func_get_args());
    }
}