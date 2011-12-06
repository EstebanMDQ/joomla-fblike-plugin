<?php 
/**
 * Joomla! 1.5 plugin jsLoader 2.0
 *
 * @author Esteban Soler
 * @copyright Copyright (C) 2011 Informal Thinkers
 * @license GNU/GPL http://www.gnu.org/licenses/gpl.html
 * @link http://www.informalthinkers.com/
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
# jimport('joomla.filesystem.file');

class plgSystemfbLikes extends JPlugin {
    
    function onAfterDispatch() {

        $mainframe =& JFactory::getApplication();
        // Do not run plugin in administration area
        if ($mainframe->isAdmin()) return;
    	
    	$plugin 		=& JPluginHelper::getPlugin('system','fblikes');
		$pluginParams 	= new JParameter( $plugin->params );

		$excluded = $pluginParams->get('excludedComponents');
		
		if( !empty($excluded) ) {
			$excluded = explode("\n", $excluded);
			$component = JRequest::getVar( 'option' , '' );
			$view = JRequest::getVar( 'view' , '' );
			foreach( $excluded as $c ) {
				if( strpos($c,'[')!==False ) {
					$c = preg_match_all('/([a-z0-9_-]+)\[([a-z0-9,_-]*)\]/Ui',$c,$m);
					if( empty($m[1][0]) || empty($m[2][0]) ) {
						error_log('error on fbLikes plugin config');
						return;
					}
					$c = $m[1][0];
					$v = explode(',',$m[2][0]);
				}
				if( $component==$c && (empty($v) || (!empty($v) && $view==$v)) ) {
					return;
				}
			}
		}
		$container = $pluginParams->get('containerName');
		
        $uri  =& JURI::getInstance();
        $url = $uri->toString();

        $html		 = '<div id="'.$container.'">';
		$html		.= '<iframe ';
		$html		.= 'src="http://www.facebook.com/plugins/like.php';
		$html		.= '?href=' . $url;
		$html		.= '&layout='.$pluginParams->get('style');
		$html		.= '&show_faces=' . $pluginParams->get('faces');
		$html		.= '&width=' . $pluginParams->get('width');
		$html		.= '&action='.$pluginParams->get('verb');
		$html		.= '&font='. $pluginParams->get('font');
		$html		.= '&colorscheme='.$pluginParams->get('scheme');
		$html		.= '&height='.$pluginParams->get('height');
		$html		.= '" scrolling="no" frameborder="0" style="border:none; overflow:hidden;height:80px;" allowTransparency="true"></iframe>';
		$html		.= '</div></body>';
		
		$doc = &JFactory::getDocument();
		$buffer = $doc->getBuffer('component').$html;
        $doc->setBuffer($buffer, 'component');
    }   //  function onAfterRender() 
    
}

