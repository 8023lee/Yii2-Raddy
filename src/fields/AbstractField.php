<?php

namespace benbanfa\raddy\fields;

use benbanfa\raddy\filters\FilterInterface;
use benbanfa\raddy\filters\StringFilter;
use benbanfa\raddy\inputs\InputTypeInterface;
use benbanfa\raddy\inputs\TextType;
use Closure;
use yii\base\Model;
use yii\db\ColumnSchema;

abstract class AbstractField implements FieldInterface
{
    // 默认 只读
    private $isReadOnly = true;

    private $filter;

    private $inputType;

    private $contentGenerator;

    public function __construct()
    {
        $this->filter = new StringFilter(); // 默认字符搜索
    }

    /**
     * {@inheritdoc}
     */
    public function init(ColumnSchema $columnSchema): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setContentGenerator(?Closure $generator): FieldInterface
    {
        $this->contentGenerator = $generator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentGenerator(): ?Closure
    {
        return $this->contentGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function format($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter(?FilterInterface $filter): FieldInterface
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter(): ?FilterInterface
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function setInputType(?InputTypeInterface $inputType): FieldInterface
    {
        $this->inputType = $inputType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputType(): ?InputTypeInterface
    {
        return $this->inputType ?: new TextType();
    }

    /**
     * {@inheritdoc}
     */
    public function setReadOnly(bool $isReadOnly): FieldInterface
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function preDataValidation(string $name, Model $model): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handlePostData(string $name, Model $model): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function wrapInputHtml(string $html, string $name, Model $model): string
    {
        return $html;
    }
}
