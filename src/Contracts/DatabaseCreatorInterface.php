<?php

namespace Protosofia\Ben10ant\Contracts;

interface DatabaseCreatorInterface
{
    public function createDatabase(array $params);
    public function getParameters();
}
