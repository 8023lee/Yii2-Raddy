<?php

namespace benbanfa\raddy\fields;

use benbanfa\raddy\inputs\InputTypeInterface;
use benbanfa\raddy\inputs\SelectType;

class BooleanField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'boolean';
    }

    public function getInputType(): ?InputTypeInterface
    {
        return new SelectType(['否', '是']);
    }
}
