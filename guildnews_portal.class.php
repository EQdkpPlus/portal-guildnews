<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2012-12-01 23:46:33 +0100 (Sa, 01. Dez 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12530 $
 * 
 * $Id: guildnews_portal.class.php 12530 2012-12-01 22:46:33Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class guildnews_portal extends portal_generic {

	protected static $path		= 'guildnews';
	protected static $data		= array(
		'name'			=> 'Guildnews',
		'version'		=> '0.1.0',
		'author'		=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Show Guildnews from WoW Armory',
		'lang_prefix'	=> 'guildnews_',
		'icon'			=> 'fa-trophy',
	);
	protected static $positions = array('left1', 'left2', 'right', 'middle', 'bottom');
	protected $settings	= array(
		'maxitems'	=> array(
			'type'		=> 'text',
			'size'		=> '3',
			'default'	=> 5,
		),
		'options'	=> array(
			'type'		=> 'multiselect',
			'options'	=> array(
				'guildCreated'=>'guildCreated',	'itemLoot' => 'itemLoot', 'itemPurchase' => 'itemPurchase', 'guildLevel' => 'guildLevel', 'guildAchievement' => 'guildAchievement', 'playerAchievement' => 'playerAchievement'
			),
		),
	);
	protected static $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '1',
	);

	public function output() {
		if ($this->config->get('default_game') == 'wow'){
			if($this->config->get('uc_servername') && $this->config->get('uc_server_loc')){
				$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));
				$guilddata = $this->game->obj['armory']->guild($this->config->get('guildtag'), $this->config->get('uc_servername'));
				$maxItems = ($this->config('maxitems')) ? $this->config('maxitems') : 5;
				infotooltip_js();
				chartooltip_js();
				
				//Guildnews
				$arrNews = $this->pdc->get('portal.module.guildnews');
				if (!$arrNews){
					if ($this->config('options')) {
						$arrOptions = unserialize($this->config('options'));
						if (count($arrOptions) < 1) $arrOptions = false;
					} else $arrOptions = false;
						
						
					$arrNews = $this->game->callFunc('parseGuildnews', array($guilddata['news'], $maxItems, $arrOptions));
					$this->pdc->put('portal.module.guildnews', $arrNews, 3600);
				}

				if (is_array($arrNews) && count($arrNews) > 0){
					if (count($arrNews) > $maxItems) $arrNews = array_slice($arrNews, 0, $maxItems);
					
					$out = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="noborder colorswitch hoverrows">';

					foreach ($arrNews as $news){

						if ($this->position == 'middle' || $this->position == 'bottom'){
							$out .= '<tr><td width="30"><div style="text-align:center;"><img src="'.$news['icon'].'" alt="" /></div></td><td>'.$news['text'].'</td><td width="80" class="nowrap">'.$this->time->nice_date($news['date'], 60*60*24*7).'</td></tr>';
						} else {
							$out .= '<tr><td width="30"><div style="text-align:center;"><img src="'.$news['icon'].'" alt="" /></div></td><td>'.$news['text'].'<div class="small italic">'.$this->time->nice_date($news['date'], 60*60*24*7).'</div></td></tr>';
						}
					}

				
					$out .= '</table>';
				} else {

					$out = $this->user->lang('guildnews_no_news');
				}

			}
		
		} else {
			$out = $this->user->lang('guildnews_wrong_game');
		}
		
		return $out;
	}
}
?>