<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 ************************************************************************
 */
namespace Fennec\Modules\Blog\Controller;

use \Fennec\Controller\Base;
use \Fennec\Modules\Blog\Model\Blog as BlogModel;
use \Fennec\Services\Settings;

/**
 * Blog module
 *
 * @author David Lima
 * @version 1.0
 */
class Index extends Base
{
    use \Fennec\Library\Urls;

    /**
     * Default number of posts shown on listing
     *
     * @var integer
     */
    const DEFAULT_POSTS_PER_PAGE = 10;

    /**
     * Blog Model
     *
     * @var \Fennec\Modules\Blog\Model\Blog
     */
    private $model;
    
    /**
     * Settings object
     * @var Settings $settings
     */
    public $settings;

    /**
     * Defines $this->model
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->settings = new Settings('Blog');
        
        $this->model = new BlogModel();
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $title = "Blog module";
        if ($this->getParam('page')) {
            $page = intval($this->getParam('page'));
            $title .= " :: Page $page";
        } else {
            $page = 1;
        }

        $this->setTitle($title);
        
        $postsPerPage = $this->settings->getSetting('postsPerPage');
        if (! $postsPerPage) {
            $postsPerPage = self::DEFAULT_POSTS_PER_PAGE;
        }

        $this->posts = $this->model->getActivePosts($postsPerPage, $page);
        $this->totalPosts = $this->model->countArticles();
        $this->totalPages = ceil($this->totalPosts / $postsPerPage);
    }

    /**
     * If $_GET['slug'] is a valid post, sets it to $this->post
     */
    public function readAction()
    {
        if ($this->getParam('slug')) {
            $slug = $this->toSlug($this->getParam('slug'));
            $post = $this->model->getByColumn('url', $slug);

            if (count($post)) {
                $this->post = $post[0];
                $this->setTitle($this->post->getTitle());
            } else {
                $this->throwHttpError(404);
            }
        }
    }
    
    /**
     * Return valid XML containing RSS feeds for blog entries
     */
    public function rssAction()
    {
        header("Content-Type: text/xml");

        $posts = $this->model->getActivePosts((int) $this->settings->rssTotalPosts);
        $items = "";
        
        foreach ($posts as $item) {
            $body = strip_tags($item->body);
            $link = "http://" . $_SERVER['HTTP_HOST'] . $this->linkToRoute('blog-read', array($item->url));
            
            $items .= <<<XML
    <item>
        <title><![CDATA[{$item->title}]]></title>
        <link>{$link}</link>
        <guid>{$link}</guid>
        <description><![CDATA[{$body}]]></description>
    </item>

XML;
        }
        
        $xml = <<<XML
<?xml version="1.0" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <atom:link href="http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" rel="self" type="application/rss+xml" />
    <title><![CDATA[{$this->settings->rssChannelTitle}]]></title>
    <description><![CDATA[{$this->settings->rssChannelDescription}]]></description>
    <link>http://{$_SERVER['HTTP_HOST']}</link>
    <image>
        <url>http://{$_SERVER['HTTP_HOST']}/assets/img/rss.png</url>
        <link>http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}</link>
        <title><![CDATA[{$this->settings->rssChannelTitle}]]></title>
    </image>
$items
</channel>
</rss>
XML;
        echo $xml;
    
    }
}
