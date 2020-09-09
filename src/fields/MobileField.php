<?php

namespace benbanfa\raddy\fields;

use benbanfa\raddy\inputs\InputTypeInterface;
use benbanfa\raddy\inputs\MobileType;
use benbanfa\security\captcha\Captcha;
use yii\base\Model;
use yii\helpers\html;

/**
 * Class MobileField
 *
 * 手机号 类型字段
 *
 * 文本框 + 按钮（按钮单击事件，发送认证码，客户端 Ajax 请求）
 */
class MobileField extends AbstractField
{
    // 是否需要认证码
    private $requiresAuthCode;
    // 认证码输入框 name
    private $authCodeInputName;
    // 认证码Id隐藏域 name
    private $authCodeHiddenName;
    // 发送认证码按钮文本
    private $authCodeButtonText;

    public function __construct($requiresAuthCode = false, $authCodeInputName = 'authCode', $authCodeHiddenName = 'authCodeId', $authCodeButtonText = '获取认证码')
    {
        $this->requiresAuthCode = $requiresAuthCode;
        $this->authCodeInputName = $authCodeInputName;
        $this->authCodeHiddenName = $authCodeHiddenName;
        $this->authCodeButtonText = $authCodeButtonText;

        // 不支持搜索
        $this->setFilter(null);
    }

    /**
     * 是否需要认证码
     */
    public function requiresAuthCode()
    {
        return $this->requiresAuthCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputType(): ?InputTypeInterface
    {
        return new MobileType();
    }

    /**
     * {@inheritdoc}
     */
    public function wrapInputHtml(string $html, string $name, Model $model): string
    {
        if ($this->requiresAuthCode) {
            // 图形验证码
            $captchaInputHtml = Captcha::widget(['name' => $model->formName().'[captcha]', 'template' => '{input}{image}',
                'options' => ['id' => $this->authCodeInputName.'-captcha', 'class' => 'form-control', 'style' => 'display:inline; width:100px;'], ]);

            // 认证码输入框
            $inputHtml = html::textInput($model->formName().'['.$this->authCodeInputName.']', '', ['class' => 'form-control', 'style' => 'display:inline; width:100px;']);

            // 发送认证码 Button
            $btnHtml = html::button($this->authCodeButtonText, ['id' => 'btn-'.$this->authCodeInputName, 'class' => 'btn ']);

            // 认证码ID 隐藏表单
            $hidHtml = html::hiddenInput($model->formName().'['.$this->authCodeHiddenName.']', '', ['id' => 'hid-'.$this->authCodeHiddenName]);

            $html .= ($captchaInputHtml.'</br></br>'.$inputHtml.$btnHtml.$hidHtml);
        }

        return $html;
    }
}
