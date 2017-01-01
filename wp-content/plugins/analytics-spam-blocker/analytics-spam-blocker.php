<?php
/*
Plugin Name: Analytics Spam Blocker
Description: This plugin blocks spam sites like semalt.com and buttons-for-website.com from reaching your website and affecting your Google Analytics statistics.
Version: 1.8.1
Author: Luke Williamson
Author URI: http://lukewilliamson.com.au
License: GPLv2
*/

add_action('admin_notices', 'cfs_wdc_admin_notice');

function cfs_wdc_admin_notice() {
	global $current_user ;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
	if ( ! get_user_meta($user_id, 'cfs_wdc_ignore_notice') ) {
        echo '<div class="updated"><p style="float:left;">'; 
        printf(__('Analytics Spam Blocker has now been activated, please consider making a small donation. Thanks! :) <br> <br> <a href="%1$s">Hide Notice</a>'), '?cfs_wdc_nag_ignore=0');
        echo "</p>";
		
		echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="float:right;">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="PL46QJ4KG7H7E">
<table>
<tr><td><input type="hidden" name="on0" value="Select donation amount">Select donation amount</td></tr><tr><td><select name="os0">
	<option value="Buy me a coffee">Buy me a coffee $5.00 AUD</option>
	<option value="Buy me a beer">Buy me a beer $10.00 AUD</option>
	<option value="Motivate me to keep developing Plugins">Motivate me to keep developing Plugins $20.00 AUD</option>
	<option value="Too generous! Thank you!">Too generous! Thank you! $50.00 AUD</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="AUD">
<input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
</form>';
		
		echo "<div style='clear:both'></div>";
		echo "</div>";
	}
}

add_action('admin_init', 'cfs_wdc_nag_ignore');

function cfs_wdc_nag_ignore() {
	global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['cfs_wdc_nag_ignore']) && '0' == $_GET['cfs_wdc_nag_ignore'] ) {
             add_user_meta($user_id, 'cfs_wdc_ignore_notice', 'true', true);
	}
}

defined('ABSPATH') or die("No script kiddies please!");

register_activation_hook( __FILE__, 'AnalyticsSpamBlockerActivate' );
register_deactivation_hook( __FILE__, 'AnalyticsSpamBlockerDeactivate' );


function AnalyticsSpamBlockerActivate()
{
	$home_path = get_home_path();
	$parsed_url = parse_url(site_url());
	if ( ( ! file_exists( $home_path.'.htaccess' ) && is_writable( $home_path ) ) || is_writable( $home_path . '.htaccess' ) ) {
		// We can make our changes
		if(file_exists( $home_path.'.htaccess' )){
			// Edit File
			$lines = file($home_path.'.htaccess');			
			//$lines[] = "\n";
			$lines[] = "\n# Analytics Spam Blocker - Start\n";
			$lines[] = "SetEnvIfNoCase Referer semalt.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer darodar.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer buttons-for-website.com spambot=yes\n";	
			$lines[] = "SetEnvIfNoCase Referer fbdownloader.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer descargar-musicas-gratis.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer baixar-musicas-gratis.comsavetubevideo.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer srecorder.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer kambasoft.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer ilovevitaly.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer ilovevitaly.co spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer ilovevitaly.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer blackhatworth.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer priceg.com spambot=yes\n";		
			$lines[] = "SetEnvIfNoCase Referer backgroundpictures.net spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer embedle.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer extener.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer extener.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer fbfreegifts.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer feedouble.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer feedouble.net spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer japfm.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer joinandplay.me spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer joingames.org spambot=yes\n";	
			$lines[] = "SetEnvIfNoCase Referer iskalko.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer musicprojectfoundation.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer myprintscreen.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer slftsdybbg.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer edakgfvwql.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer openfrost.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer openfrost.net spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer openmediasoft.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer serw.clicksor.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer socialseet.ru spambot=yes\n";	
			$lines[] = "SetEnvIfNoCase Referer sharebutton.net spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer cityadspix.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer screentoolkit.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer softomix.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer softomix.net spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer softomix.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer gobongo.info spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer myftpupload.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer websocial.me spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer luxup.ru spambot=yes\n";		
			$lines[] = "SetEnvIfNoCase Referer ykecwqlixx.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer soundfrost.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer seoexperimenty.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer cenokos.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer star61.de spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer superiends.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer vapmedia.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer econom.co spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer vodkoved.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer adcash.com spambot=yes\n";	
			$lines[] = "SetEnvIfNoCase Referer videofrost.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer youtubedownload.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer zazagames.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer 7makemoneyonline.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer ranksonic.info spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer hulfingtonpost.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer viandpet.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer a-hau.mk spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer cfsrating.sonicwall.com:8080 spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer yougetsignal.com spambot=yes\n";	
			$lines[] = "SetEnvIfNoCase Referer cenoval.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer bestwebsiteawards.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer bestwebsitesawards.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer simple-share-buttons.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer adviceforum.info spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer o-o-6-o-o.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer o-o-8-o-o.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer humanorightswatch.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer smailik.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer s.click.aliexpress.com spambot=yes\n";		
			$lines[] = "SetEnvIfNoCase Referer social-buttons.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer 4webmasters.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer best-seo-offer.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer best-seo-solution.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer buttons-for-your-website.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer www.get-free-traffic-now.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer free-share-buttons.co spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer theguardlan.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer googlsucks.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer buy-cheap-online.info spambot=yes\n";		
			$lines[] = "SetEnvIfNoCase Referer forum69.info spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer meendo-free-traffic.ga spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer www.kabbalah-red-bracelets.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer pornhub-forum.ga spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer pornhubforum.tk spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer anal-acrobats.hol.es spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer youporn-forum.ga spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer sexyteens.hol.es spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer amanda-porn.ga spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer generalporn.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer depositfiles-porn.ga spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer rapidgator-porn.ga spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer torture.ml spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer domination.ml spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer webmaster-traffic.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer youporn-forum.uni.me spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer www.event-tracking.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer free-share-buttons.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer free-social-buttons.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer guardlink.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer redtube-talk.ga spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer sanjosestartups.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer trafficmonetize.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer sitevaluation.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer 100dollars-seo.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer dailyrank.net spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer howtostopreferralspam.eu spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer videos-for-your-business.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer semaltmedia.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer success-seo.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer synerity.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer anticrawler.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer search.tb.ask.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer gotovim-doma.ru spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer lomb.co spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer lombia.co spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer prodvigator.ua spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer offers.bycontext.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer website-errors-scanner.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer traffic-paradise.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer cambridgeshire.libnet.info spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer webmonetizer.net spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer notify.bluecoat.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer sharebutton.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer netvibes.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer webcrawler.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer view.contextualyield.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer resellerclub.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer powitania.pl spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer tasteidea.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer descargar-musica-gratis.net spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer torontoplumbinggroup.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer cyprusbuyproperties.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer ranksonic.org spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer securesuite.co.uk spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer symbaloo.com spambot=yes\n";
			$lines[] = "SetEnvIfNoCase Referer start.otgmanagement.com spambot=yes\n";
			
			$lines[] = "Order allow,deny\n";
			$lines[] = "Allow from all\n";
			$lines[] = "Deny from env=spambot\n";
			$lines[] = "# Analytics Spam Blocker - End";
			
			$fp = fopen($home_path.'.htaccess', 'w');
			foreach($lines as $line){
				fwrite($fp, "$line");
			}
			fclose($fp);
		} else {
			// New File
			$fp = fopen($home_path.'.htaccess','w');
			fwrite($fp, "# Analytics Spam Blocker - Start\n");
			fwrite($fp,"SetEnvIfNoCase Referer semalt.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer darodar.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer buttons-for-website.com spambot=yes\n");			
			fwrite($fp,"SetEnvIfNoCase Referer fbdownloader.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer descargar-musicas-gratis.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer baixar-musicas-gratis.comsavetubevideo.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer srecorder.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer kambasoft.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer ilovevitaly.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer ilovevitaly.co spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer ilovevitaly.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer blackhatworth.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer priceg.com spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer backgroundpictures.net spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer embedle.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer extener.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer extener.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer fbfreegifts.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer feedouble.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer feedouble.net spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer japfm.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer joinandplay.me spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer joingames.org spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer iskalko.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer musicprojectfoundation.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer myprintscreen.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer slftsdybbg.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer edakgfvwql.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer openfrost.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer openfrost.net spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer openmediasoft.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer serw.clicksor.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer socialseet.ru spambot=yes\n");	
			fwrite($fp,"SetEnvIfNoCase Referer sharebutton.net spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer cityadspix.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer screentoolkit.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer softomix.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer softomix.net spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer softomix.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer gobongo.info spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer myftpupload.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer websocial.me spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer luxup.ru spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer ykecwqlixx.ru spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer soundfrost.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer seoexperimenty.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer cenokos.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer star61.de spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer superiends.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer vapmedia.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer econom.co spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer vodkoved.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer adcash.com spambot=yes\n");	
			fwrite($fp,"SetEnvIfNoCase Referer videofrost.com spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer youtubedownload.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer zazagames.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer 7makemoneyonline.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer ranksonic.info spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer hulfingtonpost.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer viandpet.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer a-hau.mk spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer cfsrating.sonicwall.com:8080 spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer yougetsignal.com spambot=yes\n");	
			fwrite($fp,"SetEnvIfNoCase Referer cenoval.ru spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer bestwebsiteawards.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer bestwebsitesawards.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer simple-share-buttons.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer adviceforum.info spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer o-o-6-o-o.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer o-o-8-o-o.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer humanorightswatch.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer smailik.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer s.click.aliexpress.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer social-buttons.com spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer 4webmasters.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer best-seo-offer.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer best-seo-solution.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer buttons-for-your-website.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer www.get-free-traffic-now.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer free-share-buttons.co spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer theguardlan.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer googlsucks.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer buy-cheap-online.info spambot=yes\n");	
			fwrite($fp,"SetEnvIfNoCase Referer forum69.info spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer meendo-free-traffic.ga spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer www.kabbalah-red-bracelets.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer pornhub-forum.ga spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer pornhubforum.tk spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer anal-acrobats.hol.es spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer youporn-forum.ga spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer sexyteens.hol.es spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer amanda-porn.ga spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer generalporn.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer depositfiles-porn.ga spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer rapidgator-porn.ga spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer torture.ml spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer domination.ml spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer webmaster-traffic.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer youporn-forum.uni.me spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer www.event-tracking.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer free-share-buttons.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer free-social-buttons.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer guardlink.org spambot=yes\n");	
			fwrite($fp,"SetEnvIfNoCase Referer redtube-talk.ga spambot=yes\n");		
			fwrite($fp,"SetEnvIfNoCase Referer sanjosestartups.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer trafficmonetize.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer sitevaluation.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer 100dollars-seo.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer dailyrank.net spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer howtostopreferralspam.eu spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer videos-for-your-business.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer semaltmedia.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer success-seo.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer synerity.se spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer anticrawler.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer search.tb.ask.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer gotovim-doma.ru spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer lomb.co spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer lombia.co spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer prodvigator.ua spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer offers.bycontext.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer website-errors-scanner.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer traffic-paradise.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer cambridgeshire.libnet.info spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer webmonetizer.net spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer notify.bluecoat.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer sharebutton.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer netvibes.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer webcrawler.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer view.contextualyield.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer resellerclub.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer powitania.pl spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer tasteidea.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer descargar-musica-gratis.net spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer torontoplumbinggroup.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer cyprusbuyproperties.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer ranksonic.org spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer securesuite.co.uk spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer securesuite.net spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer symbaloo.com spambot=yes\n");
			fwrite($fp,"SetEnvIfNoCase Referer start.otgmanagement.com spambot=yes\n");
			
			fwrite($fp,"Order allow,deny\n");
			fwrite($fp,"Allow from all\n");
			fwrite($fp,"Deny from env=spambot\n");
			fwrite($fp, "# Analytics Spam Blocker - End");
			fclose($fp);
		}
	} else {
		// Not writable
		wp_die(_e('Your .htaccess file or root WordPress directory is not writable'));
	}
	
	//Delete old 1.5 plugin area
	if ( is_writable( $home_path.'.htaccess' ) ) {
		$fileOld = file_get_contents($home_path.'.htaccess');
		$fileOld = deleteOldText('# BEGIN Analytics Spam Blocker', '# END Analytics Spam Blocker', $fileOld);
		
		$fpOld = fopen($home_path.'.htaccess', 'w');
		fwrite($fpOld, $fileOld);
		fclose($fpOld);
	} else {
		// Not writable
		wp_die(_e('Your .htaccess file is not writable or doesn\'t exist.'));
	}
}
function deleteOldText($startOld, $endOld, $stringOld)
{
  $beginningPosOld = strpos($stringOld, $startOld);
  $endPosOld = strpos($stringOld, $endOld);
  if ($beginningPosOld === false || $endPosOld === false) {
    return $stringOld;
  }

  $deleteOld = substr($stringOld, $beginningPosOld, ($endPosOld + strlen($endOld)) - $beginningPosOld);

  return str_replace($deleteOld, '', $stringOld);
}

function AnalyticsSpamBlockerDeactivate()
{
	$home_path = get_home_path();
	$parsed_url = parse_url(site_url());
	if ( is_writable( $home_path.'.htaccess' ) ) {
		// We can make our changes
		$file = file_get_contents($home_path.'.htaccess');
		$file = DeleteCurrentCode('# Analytics Spam Blocker - Start', '# Analytics Spam Blocker - End', $file);
		
		$fp = fopen($home_path.'.htaccess', 'w');
		fwrite($fp, $file);
		fclose($fp);
	} else {
		// Not writable
		wp_die(_e('Your .htaccess file is not writable or doesn\'t exist.'));
	}
}

function DeleteCurrentCode($start, $end, $string)
{
  $beginningPos = strpos($string, $start);
  $endPos = strpos($string, $end);
  if ($beginningPos === false || $endPos === false) {
    return $string;
  }

  $delete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

  return str_replace($delete, '', $string);
}