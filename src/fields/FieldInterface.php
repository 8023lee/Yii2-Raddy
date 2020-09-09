<?php

namespace benbanfa\raddy\fields;

use benbanfa\raddy\filters\FilterInterface;
use benbanfa\raddy\inputs\InputTypeInterface;
use Closure;
use yii\base\Model;
use yii\db\ColumnSchema;

interface FieldInterface
{
    /**
     * 通过数据库信息做一些初始化工作，比如 MoneyField 可通过判断字段是浮点型还是整型来设置单位是元还是分
     */
    public function init(ColumnSchema $columnSchema): void;

    /**
     * 设置数据显示生成器
     *
     * @param Closure|null $generator 内容生成器
     */
    public function setContentGenerator(?Closure $generator): self;

    /**
     * 获取数据显示生成器
     */
    public function getContentGenerator(): ?Closure;

    /**
     * 获取 Yii2 DataColumn format 选项的值
     * 因为 format 也会改变内容的显示方式，所以设置了 format 后 setContentGenerator 不起作用
     *
     * @return string|array|Closure|null
     */
    public function getFormat();

    /**
     * 通过 $value 换化成格式化后要显示的字符串，此方法应当在处理求和的值和处理一般单元格数据的时候复用
     *
     * @param mixed $value
     *
     * @return string
     */
    public function format($value);

    /**
     * 设置过滤器
     *
     * @param FilterInterface|null $filter
     *
     * @return FieldInterface
     */
    public function setFilter(?FilterInterface $filter): self;

    /**
     * 获取过滤器
     */
    public function getFilter(): ?FilterInterface;

    /**
     * 设置输入表单类型
     *
     * @param InputTypeInterface|null $inputType
     *
     * @return FieldInterface
     */
    public function setInputType(?InputTypeInterface $inputType): self;

    /**
     * 获取输入表单类型
     *
     * @return InputTypeInterface|null
     */
    public function getInputType(): ?InputTypeInterface;

    /**
     * 设置是否只读
     *
     * @param bool $isReadOnly
     *
     * @return FieldInterface
     */
    public function setReadOnly(bool $isReadOnly): self;

    /**
     * 是否只读
     *
     * @return bool
     */
    public function isReadOnly(): bool;

    /**
     * 数据校验之前的处理
     *
     * @param string $name  字段名
     * @param Model  $model 数据模型
     */
    public function preDataValidation(string $name, Model $model): void;

    /**
     * 处理表单提交的数据
     *
     * @param string $name  字段名
     * @param Model  $model 数据模型
     */
    public function handlePostData(string $name, Model $model): void;

    /**
     * 修饰表单输入的 HTML 代码
     *
     * @param string $html  已生成的 html 代码
     * @param string $name  字段名
     * @param Model  $model 数据模型
     */
    public function wrapInputHtml(string $html, string $name, Model $model): string;
}
