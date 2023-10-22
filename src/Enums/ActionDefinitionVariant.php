<?php
namespace Apie\HtmlBuilders\Enums;

use Stringable;

enum ActionDefinitionVariant: string
{
    case DEFAULT = 'default';
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case DANGER = 'danger';
    case PLAIN = 'plain';
}
