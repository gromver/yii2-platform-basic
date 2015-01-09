<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;

use Yii;
use yii\web\Cookie;

/**
 * Class Request
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Request extends \yii\web\Request
{
    const LANGUAGE_KEY = '__language';

    /**
     * @var int language cookie lifetime in seconds. Default is 1 year. Set to false to disable cookie.
     */
    public $languageCookieLifetime = 31536000;

    /**
     * @var bool wether to store language selection in session and (optionally) in cookie
     */
    public $persistLanguage = true;

    /**
     * @var bool wether to automatically detect the preferred language from the browser settings
     */
    public $detectLanguage = true;

    /**
     * @var bool wether to redirect to the default language URL if no language specified
     */
    public $redirectDefault = true;

    /**
     * @var array list of available language codes. More specific patterns first, e.g. 'en_us', 'en'.
     * This can also contain key/value items of the form "<url_name>"=>"<language", e.g. 'english'=>'en'
     */
    private $_pathInfo;
    private $_defaultLanguage;

    public function getPathInfo()
    {
        if ($this->_pathInfo === null) {
            $this->_pathInfo = $this->resolvePathInfo();

            $language = null;
            $baseUrl = $this->getBaseUrl();

            if ($this->_pathInfo) {
                if (($pos = strpos($this->_pathInfo, '/')) !== false) {
                    $language = substr($this->_pathInfo, 0, $pos);
                } else {
                    $language = $this->_pathInfo;
                }

                if(in_array($language, Yii::$app->acceptedLanguages)) {
                    //язык обнаружен в начале пути - ru/site/index
                    Yii::trace("Detected language '{$language}'.");
                    //очищаем путь от языка
                    $this->_pathInfo = trim(substr($this->_pathInfo, strlen($language)), '/');

                    //сохраняем выбранный язык в куках
                    if ($this->persistLanguage) {
                        Yii::$app->session[self::LANGUAGE_KEY] = $language;
                        if ($this->languageCookieLifetime) {
                            Yii::$app->response->cookies->add(new Cookie([
                                'name' => self::LANGUAGE_KEY,
                                'value' => $language,
                                'expire' => time() + $this->languageCookieLifetime
                            ]));
                        }
                    }

                    //если язык совпадает с тем что используется по умолчанию то редиректим на туже страницу но вырезаем язык из пути
                    if ($language == $this->getDefaultLanguage() && $this->redirectDefault && $this->getIsGet()) {
                        Yii::$app->response->redirect(preg_replace("#^{$baseUrl}/{$language}#", $baseUrl, $this->getUrl()));
                        Yii::$app->end();
                    }
                } else {
                    $language = null;
                }
            }

            if ($language === null) {
                //язык не обнаружен в начале пути
                //если язык сохранен в сесии то тянем его от туда
                if ($this->persistLanguage) {
                    $language = Yii::$app->session[self::LANGUAGE_KEY];

                    if ($language === null) {
                        $language = $this->getCookies()->getValue(self::LANGUAGE_KEY);
                    }
                }

                //если в сессии не обнаружен, пытаемся определить подходящий исходя из списка языков поддерживаемых браузером
                if ($language === null && $this->detectLanguage) {
                    $language = $this->getPreferredLanguage(Yii::$app->acceptedLanguages);
                }

                //если определенный язык не совпадает с языком по умолчанию - то редиректим на тотже адрес с указанием определенного языка
                if ($language != $this->getDefaultLanguage() && $this->getIsGet()){
                    Yii::$app->response->redirect(preg_replace('#^' . $this->getBaseUrl() . '#', $this->getBaseUrl() . '/' . $language, $this->getUrl()));
                    Yii::$app->end();
                }
            }

            //назначаем язык нашему приложению
            Yii::$app->language = $language;
        }

        return $this->_pathInfo;
    }

    public function getDefaultLanguage()
    {
        if (!isset($this->_defaultLanguage)) {
            $this->_defaultLanguage = Yii::$app->language;
        }

        return $this->_defaultLanguage;
    }
}