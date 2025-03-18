<?php
namespace Apie\HtmlBuilders\Exceptions;

use Apie\Core\Exceptions\ApieException;

class EmptyFormNameException extends ApieException
{
    public function __construct()
    {
        parent::__construct('Form Name is empty!');
    }
}
