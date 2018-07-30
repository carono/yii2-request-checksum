<?php


namespace carono\checksum;


use yii\helpers\Html;

class ActiveForm extends \yii\widgets\ActiveForm
{
    public function run()
    {
        $content = ob_get_clean();
        $html = Html::beginForm($this->action, $this->method, $this->options);
        $html .= $content;

        if ($this->enableClientScript) {
            $this->registerClientScript();
        }

        $html .= Html::endForm();
        return $html;
    }
}