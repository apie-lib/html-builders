<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;

class Csrf extends BaseComponent
{
    public function __construct(string $token)
    {
        parent::__construct(
            [
                'token' => $token,
            ]
        );
    }
}
