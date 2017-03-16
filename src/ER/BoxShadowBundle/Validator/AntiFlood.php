<?php

namespace ER\BoxShadowBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class AntiFlood
 * @package ER\BoxShadowBundle\Validator
 * @Annotation
 */
class AntiFlood extends Constraint
{
    public $message = "Votre message %string% est considéré comme flood.";

    public function validatedBy()
    {
        return 'er_boxshadow_antiflood';
    }
}