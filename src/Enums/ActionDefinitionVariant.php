<?php
namespace Apie\HtmlBuilders\Enums;

enum ActionDefinitionVariant: string
{
    case DEFAULT = 'default';
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case DANGER = 'danger';
    case PLAIN = 'plain';
}
