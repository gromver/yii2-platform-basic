<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use gromver\platform\basic\assets\CkeditorHighlightAsset;
use gromver\platform\basic\modules\news\models\Post;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class PostView
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PostView extends Widget
{
    /**
     * Post model or PostId or PostId:PageAlias
     * @var Post|string
     * @type modal
     * @url /grom/news/backend/post/select
     * @translation gromver.platform
     */
    public $post;
    /**
     * @type list
     * @items layouts
     * @editable
     * @translation gromver.platform
     */
    public $layout = 'post/viewIssue';
    /**
     * @type yesno
     * @translation gromver.platform
     */
    public $showTranslations;
    /**
     * @type yesno
     * @translation gromver.platform
     */
    public $useHighlights = true;

    protected function launch()
    {
        if ($this->post && !$this->post instanceof Post) {
            $this->post = Post::findOne(intval($this->post));
        }

        if (empty($this->post)) {
            throw new InvalidConfigException(Yii::t('gromver.platform', 'Post not found.'));
        }

        if ($this->useHighlights) {
            CkeditorHighlightAsset::register($this->getView());
        }

        echo $this->render($this->layout, [
            'model' => $this->post
        ]);
    }

    public function customControls()
    {
        return [
            [
                'url' => ['/grom/news/backend/post/update', 'id' => $this->post->id, 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-pencil"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Update Post')]
            ],
        ];
    }

    public static function layouts()
    {
        return [
            'post/viewArticle' => Yii::t('gromver.platform', 'Article'),
            'post/viewIssue' => Yii::t('gromver.platform', 'Issue'),
        ];
    }
} 