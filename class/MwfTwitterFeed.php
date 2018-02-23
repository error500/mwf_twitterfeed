<?php
use Abraham\TwitterOAuth\TwitterOAuth;

class MwfTwitterFeed {

    protected $twitterName;
    protected $twitterAccount;
    protected $cacheLife;
    protected $feedSize;
    protected $consumerKey;
    protected $consumerSecret;
    protected $accessToken;
    protected $accessTokenSecret;
    protected $cache;
    protected $tweets;

public function __construct()
{
    $this->twitterName = getenv('TWITTER_NAME');
    $this->twitterAccount = getenv('TWITTER_ACCOUNT');
    $this->cacheLife = getenv('CACHE_LIFE');
    $this->feedSize = getenv('FEED_SIZE');
    $this->consumerKey = getenv('CONSUMER_KEY');
    $this->consumerSecret = getenv('CONSUMER_SECRET');
    $this->accessToken = getenv('ACCESS_TOKEN');
    $this->accessTokenSecret = getenv('ACCESS_TOKEN_SECRET');

    $this->cache = dirname(__FILE__) .'/../cache/tweets.tmp';

    add_shortcode('mwf_twitter', array($this, 'writeShortcode'));

}

    function checkHttps() {

        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {

            return true;
        }
        return false;
    }

    function readTweets() {
        if (!file_exists($this->cache) || (time() - filemtime($this->cache) > $this->cacheLife)) {

            $connection = new TwitterOAuth($this->consumerKey, $this->consumerSecret, $this->accessToken, $this->accessTokenSecret);

            $tweets = $connection->get('statuses/user_timeline', array('count' => $this->feedSize));
            file_put_contents($this->cache, serialize($tweets));

        } else {

            $tweets = unserialize(file_get_contents($this->cache));

        }
        return $tweets;
    }

	public function writeShortcode () {

		wp_enqueue_style(
			'mwf_twitter-css',
			plugins_url('/../css/twitter.css',__FILE__ )
		);

        $this->tweets = $this->readTweets();


		ob_start();		
		?>
			<div id="tweets">
				<a class="follow-button right" title="Suivre <?php echo $this->twitterName; ?> sur Twitter"  role="button" href="https://twitter.com/<?php echo $this->twitterAccount;?>">
					<i class="ic-button-bird"></i>Suivre
				</a>
				<h2>Tweets</h2>

				<ol>
				<?php foreach ($this->tweets as $tweet){
					?>
					<li class="tweet">
						<div class="header">

							<?php 
							if(isset($tweet->retweeted_status)) {
							?>
								<div class="h-card p-author" data-scribe="component:author">
									<a href="<?php echo $tweet->retweeted_status->user->url?>">
									<strong class="fullname"><?php echo $tweet->retweeted_status->user->name?></strong>
									<span class="p-nickname">@<?php echo $tweet->retweeted_status->user->screen_name?></span>
									<img class="avatar" src="<?php
                                    echo $this->checkHttps() ? $tweet->retweeted_status->user->profile_image_url_https : $tweet->retweeted_status->user->profile_image_url;
                                    ?>" />
									</a>
								</div>
								<p  class="e-entry-title" ><?php echo preg_replace('/((www|http:\/\/|https:\/\/)[^ ]+)/', '<a href="\1">\1</a>', $tweet->retweeted_status->text); ?></p>
								<div class="retweet-credit" class="right"><i class="ic-rt"></i>Retweet&eacute; par <?php echo $this->twitterName; ?></div>
							<?php 
							} else {
							?>
								<div class="h-card p-author" data-scribe="component:author">
									<a href="<?php echo $tweet->user->url?>">
										<strong class="fullname"><?php echo $tweet->user->name?></strong>
										<span class="p-nickname">@<?php echo $tweet->user->screen_name?></span>
										<img class="avatar" src="<?php
                                        echo $this->checkHttps() ? $tweet->user->profile_image_url : $tweet->user->profile_image_url?>" />
									</a>
								</div>
								<p class="e-entry-title" ><?php echo preg_replace('/((www|http:\/\/|https:\/\/)[^ ]+)/', '<a href="\1">\1</a>', $tweet->text); ?></p>
							<?php 
							} ?>

							<?php if (isset($tweet->entities->media)) {
								foreach ($tweet->entities->media as $media) {
									if ( $media->type == "photo" ) { ?>
										<img src="<?php echo  $this->checkHttps() ? $media->media_url_https : $media->media_url ?>" />
									<?php }
								}
							}?>
						</div>
					</li>
				<?php } //endforeach ?>
				</ol>

			</div>
		<?php
		$render = ob_get_contents();
		ob_end_clean();
			
		return $render;

	}	
}