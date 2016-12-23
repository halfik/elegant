<?php

namespace Netinteractive\Elegant\Search;


interface TranslatorInterface
{
    /**
     * @param string $field
     * @param string $type
     * @return mixed
     */
    public function translate($field, $type);
} 