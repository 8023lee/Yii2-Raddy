<?php

namespace benbanfa\raddy\fields;

use benbanfa\raddy\filters\DateTimeFilter;

class DateTimeField extends AbstractField
{
    private $format;

    public function __construct(string $format = 'short')
    {
        $this->format = $format;
        $this->setFilter(new DateTimeFilter());
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return ['datetime', $this->format];
    }
}
