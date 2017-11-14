<?php


namespace carono\checksum;


use yii\helpers\ArrayHelper;

/**
 * Class Request
 *
 * @package carono\checksum
 */
class Request extends \yii\web\Request
{
    public $checksumParam = '_checksum';
    public $enableChecksumValidation = true;

    public function validateCsrfToken($clientSuppliedToken = null)
    {
        if ($this->getMethod() == 'POST' && \Yii::$app->request instanceof Request && \Yii::$app->request->enableChecksumValidation) {
            $post = \Yii::$app->request->post();
            $checksum = ArrayHelper::remove($post, \Yii::$app->request->checksumParam);
            ArrayHelper::remove($post, \Yii::$app->request->csrfParam);
            if ($checksum != Checksum::calculate($post)) {
                return false;
            }
        }
        return parent::validateCsrfToken($clientSuppliedToken);
    }
}