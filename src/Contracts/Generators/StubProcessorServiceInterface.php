<?php

namespace Kuato\Contracts\Generators;

interface StubProcessorServiceInterface
{
    public function make($stub, $moduleName, $subModuleName=null);
    public function setStub($stub);
    public function getStub();
}