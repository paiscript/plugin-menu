<?php

pai_add_filter('pageinfo', 'pai_menu_pageinfo');
pai_add_filter('config', 'pai_menu_config');

function pai_menu_pageinfo($pagesinfo) {
	foreach($pagesinfo AS $page => $pageinfo) {
		foreach($pageinfo AS $key => $menu) {
			if ($key == 'menu' && is_array($menu)) {

				foreach($menu AS $name => $value) {

					// create info array
					if (is_string($value)) {
						$value = array('text' => $value);
					} else if (is_numeric($value)) {
						$value = array('order' => $value);
					} else if (is_array($value)) {
						$i = array();
						foreach($value AS $v) {
							if (is_string($v)) {
								$i['text'] = $v;
							} elseif (is_int($v)) {
								$i['order'] = $v;
							}
						}
						$value = $i;
					} else if(is_object($value)) {
						$value = (array) $value;
					} else if($value) {
						$value = array();
					} else {
						unset($pagesinfo->$page->$key->$name);
						continue;
					}


					$pagesinfo->$page->$key->$name = $value;

				} // foreach
			} // if
		} // foreach
	} // foreach

	return $pagesinfo;
}

function pai_menu_config($config) { echo 'run pai_menu_config<br>';
	if (isset($config['plugins']['menu']) && !is_array()) {
		$config['plugins']['menu'] = array();
	}
	return $config;
}


//echo '<pre>'; $m = pai_pageInfo('menu', null); echo "\n"; print_r($m); exit();

class PAI_MenuItem {
	public $children = array();
	public $items = array();
	public $text = '';
	public $order = 0;
	public $parent;
	public $item;

	function __construct($page, $info) {
		$this->page = $page;
		foreach($info AS $key => $value) {
			$this->$key = $value;
		}
	}
	function addChild($child) {
		$this->children[$child->page] = $child;
	}
	function addItem($item) {
		$this->items[] = $item->page;
	}
	function isCurrent() {
		return ($this->page == PAI_PAGE || in_array(PAI_PAGE, $this->items));
	}
	function isChildCurrent() {
		foreach($this->children AS $child) {
			if ($child->isCurrent() || $child->isChildCurrent()) {
				return true;
			}
		}
		return false;
	}
}

function pai_menu_getInfo($name) {
	$menu = (array) pai_pageInfo(array('menu', $name), null);

	foreach($menu AS $page => $info) {
		$info = new PAI_MenuItem($page, $info);

		if (!$info->text) {
			$info->text = pai_pageInfo('title', $page);
		}
		if (!$info->text) {
			$info->text = ucfirst($page);
		}

		$menu[$page] = $info;
	}

	foreach($menu AS $page => $item) {
		if (isset($item->parent)) {
			$menu[ $item->parent ]->addChild($item);
		}
		if (isset($item->item)) {
			$menu[ $item->item ]->addItem($item);
			unset($menu[ $page ]);
		}
	}

	foreach($menu AS $page => $item) {
		if (isset($item->parent)) {
			unset($menu[ $page ]);
		}
	}

	return $menu;
}

function pai_menu_build($menu, $conf, $name = '') {
	$rootElement = pai_conf('url', 'rootElement');


	$menu = sortObjByField($menu, 'order');
	$class = pai_conf('plugins', 'menu', 'ulclass');

	$html = ($name ? '<ul id="pai_menu-'.$name.'" class="'.$class.'">' : '<ul class="dropdown-menu">');
	foreach($menu AS $page => $item) {
		$classess = array('pai_menu-page-'.str_replace('/', '_', $page));

		if(count($item->children) AND !@$conf['childDisabled']) {
      array_push($classess, 'dropdown');
      $dropdown = ' class="dropdown-toggle" data-toggle="dropdown"';
      $dropdowncaret = ' <b class="caret"></b>';
		}

		if ($item->isCurrent()) { $classess[] = (isset($conf['currentClass']) ? $conf['currentClass'] : 'pai_menu-current'); }
		if ($item->isChildCurrent()) { $classess[] = (isset($conf['currentChildClass']) ? $conf['currentChildClass'] : 'pai_menu-child_current'); }

		if(isset($conf['icon']['enabled'])) {
			$icon = $conf['icon']['before'].@pai_pageInfo('icon', $page).$conf['icon']['after'];
		}

		$html .= '<li class="'.implode(' ', $classess).'"><a href="'.PAI_PATH.($page == $rootElement ? '' : $page).'"'.@$dropdown.'>'.@$icon.$item->text.@$dropdowncaret.'</a>';

		if (count($item->children) AND !$conf['childDisabled']) {
			$html .= pai_menu_build($item->children, $conf);

			$dropdown = null;
			$dropdowncaret = null;
		}

		$html .= '</li>';
	}
	$html .= '</ul>';
	return $html;
}


function pai_menu($name, $options = null, $return = false) {
	pai_set_js_options("plugins-menu-list-$name", (object) $options);


	$menu = pai_menu_getInfo($name);

	$conf = pai_deep_merge( @ pai_conf('plugins', 'menu', $name), (array) $options);

	$html = pai_menu_build($menu, $conf, $name);

	if ($return) {
		return $html;
	}
	print $html;
}

if (!function_exists('sortObjByField')) {
	function sortObjByField($multArray,$sortField,$desc=true){
	            $tmpKey='';
	            $ResArray=array();

	            $maIndex=array_keys($multArray);
	            $maSize=count($multArray)-1;

	            for($i=0; $i < $maSize ; $i++) {

	               $minElement=$i;
	               $tempMin=$multArray[$maIndex[$i]]->$sortField;
	               $tmpKey=$maIndex[$i];

	                for($j=$i+1; $j <= $maSize; $j++)
	                  if($multArray[$maIndex[$j]]->$sortField < $tempMin ) {
	                     $minElement=$j;
	                     $tmpKey=$maIndex[$j];
	                     $tempMin=$multArray[$maIndex[$j]]->$sortField;

	                  }
	                  $maIndex[$minElement]=$maIndex[$i];
	                  $maIndex[$i]=$tmpKey;
	            }

	           if($desc)
	               for($j=0;$j<=$maSize;$j++)
	                  $ResArray[$maIndex[$j]]=$multArray[$maIndex[$j]];
	           else
	              for($j=$maSize;$j>=0;$j--)
	                  $ResArray[$maIndex[$j]]=$multArray[$maIndex[$j]];

	           return $ResArray;
	       }
}
