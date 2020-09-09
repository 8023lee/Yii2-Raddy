<?php
/**
 * Created by PhpStorm.
 * User: lijingang
 * Date: 2020/4/9
 * Time: 下午5:29
 */

namespace benbanfa\raddy\services;

class DetailColumnsOptionMaker
{
    /**
     * 通过 ['field' => FieldInterface] 形式的配置，得到
     * Yii2 GridView 能识别的 option
     *
     * @param array $$fields
     */
    public function make(array $fields): array
    {
        $result = [];

        foreach ($fields as $name => $field) {
            $result[$name] = [
                'attribute' => $name,
            ];

            $result[$name]['format'] = 'html'; // 默认
            $format = $field->getFormat();
            if (null !== $format) {
                $result[$name]['format'] = $format;

                continue; // 因 format 和 content 都是控制内容的，所以设置了 format 就不需要再管 content
            }

            $result[$name]['value'] = function ($model) use ($name, $field) {
                if (null === $model->$name) {
                    return '(未设置)';
                }

                $generator = $field->getContentGenerator();
                if (null !== $generator) {
                    return $generator($name, $model);
                }

                $content = $model->$name;

                return $content;
            };
        }

        return $result;
    }
}
