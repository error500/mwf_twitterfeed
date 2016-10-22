<?php
use Abraham\TwitterOAuth\TwitterOAuth;

class MwfTwitterFeed {
	public static  function mwf_shortcode_twitter ($atts,$content) {
		
		require __DIR__ .'/../config.php';

		
		wp_enqueue_style(
			'mwf_twitter-css',
			plugins_url('/../css/twitter.css',__FILE__ )
		);


		$cache = dirname(__FILE__) .'/../cache/tweets.tmp';

		if (time() - filemtime($cache) > $cacheLife) {
		  
		  $connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

		  $tweets = $connection->get('statuses/user_timeline', array('count' => $feedSize));
		  file_put_contents($cache, serialize($tweets));

		} else {

		  $tweets = unserialize(file_get_contents($cache));

		}
		ob_start();		
		?>
			<div id="tweets">
				<a class="follow-button right" title="Suivre <?php echo $twitterName; ?> sur Twitter"  role="button" href="https://twitter.com/<?php echo $twitterAccount;?>">
					<i class="ic-button-bird"></i>Suivre
				</a>
				<h2>Tweets <php echo $content; ?></h2>

				<ol>
				<?php foreach ($tweets as $tweet){
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
									<img class="avatar" src="<?php echo $tweet->retweeted_status->user->profile_image_url?>" />
									</a>
								</div>
								<p  class="e-entry-title" ><?php echo preg_replace('/((www|http:\/\/|https:\/\/)[^ ]+)/', '<a href="\1">\1</a>', $tweet->retweeted_status->text); ?></p>
								<div class="retweet-credit" class="right"><i class="ic-rt"></i>Retweet&eacute; par <?php echo $twitterName; ?></div>
							<?php 
							} else {
							?>
								<div class="h-card p-author" data-scribe="component:author">
									<a href="<?php echo $tweet->user->url?>">
										<strong class="fullname"><?php echo $tweet->user->name?></strong>
										<span class="p-nickname">@<?php echo $tweet->user->screen_name?></span>
										<img class="avatar" src="<?php echo $tweet->user->profile_image_url?>" />
									</a>
								</div>
								<p class="e-entry-title" ><?php echo preg_replace('/((www|http:\/\/|https:\/\/)[^ ]+)/', '<a href="\1">\1</a>', $tweet->text); ?></p>
							<?php 
							} ?>

							<?php if (isset($tweet->entities->media)) {
								foreach ($tweet->entities->media as $media) {
									if ( $media->type == "photo" ) { ?>
										<img src="<?php echo $media->media_url ?>" />
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