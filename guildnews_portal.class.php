<?php
/*	Project:	EQdkp-Plus
 *	Package:	Guildnews Portal Module
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class guildnews_portal extends portal_generic {

	protected static $path		= 'guildnews';
	protected static $data		= array(
		'name'			=> 'Guildnews',
		'version'		=> '0.3.1',
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
				'encounter'=>'encounter',	'character_achievement' => 'character_achievement', 
			),
		),
	);
	protected static $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '1',
	);
	
	protected static $apiLevel = 20;

	public function output() {
		if ($this->config->get('default_game') == 'wow'){
			if($this->config->get('servername') && $this->config->get('uc_server_loc')){
				$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));
				
				$arrActivity = $this->game->obj['armory']->guildActivity($this->config->get('guildtag'), $this->config->get('servername'));
				$maxItems = ($this->config('maxitems')) ? $this->config('maxitems') : 5;
				infotooltip_js();
				chartooltip_js();
				
				//Guildnews
				$arrNews = $this->pdc->get('portal.module.guildnews.'.$this->user->lang_name);
				if (!$arrNews){
					if ($this->config('options')) {
						$arrOptions = $this->config('options');
						if (count($arrOptions) < 1) $arrOptions = false;
					} else $arrOptions = false;
						
						
					$arrNews = $this->game->callFunc('parseGuildnews', array($arrActivity['activities'], $maxItems, $arrOptions));
					$this->pdc->put('portal.module.guildnews.'.$this->user->lang_name, $arrNews, 3600);
				}

				if (is_array($arrNews) && count($arrNews) > 0){
					if (count($arrNews) > $maxItems) $arrNews = array_slice($arrNews, 0, $maxItems);
					
					$out = '<table class="table fullwidth noborder colorswitch hoverrows">';

					foreach ($arrNews as $news){
						$icon = (strlen($news['icon'])) ? '<div class="user-avatar-small user-avatar-border"><img src="'.$news['icon'].'" alt="" class="user-avatar small" loading="lazy"/></div>' : '';
						
						if ($this->position == 'middle' || $this->position == 'bottom'){
							$out .= '<tr><td width="30">'.$icon.'</td><td>'.$news['text'].'</td><td width="80" class="nowrap">'.$this->time->nice_date($news['date'], 60*60*24*7).'</td></tr>';
						} else {
							$out .= '<tr><td width="30">'.$icon.'</td><td>'.$news['text'].'<div class="small italic">'.$this->time->nice_date($news['date'], 60*60*24*7).'</div></td></tr>';
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