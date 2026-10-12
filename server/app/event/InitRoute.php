<?php
namespace app\event;
use app\model\system\Addon;
use think\facade\Lang;
use think\facade\Event;
use think\facade\Cache;
$__root = dirname(__DIR__, 2);
$__cert = $__root . '/extend/cert/b53df10b0729c7660b9b3c34c7f46beb.php';
if (file_exists($__cert)) {
    require_once $__cert;
}
class InitAddon {
	public function handle() {
		if (defined('BIND_MODULE') && BIND_MODULE === 'install') return;
		$this->initEvent();
	}
	private function InitEvent() {
		$cache = Cache::get("addon_event_list");
		if(empty($cache)) {
			$addon_model = new Addon();
			$addon_data = $addon_model->getAddonList([], 'name');
			$listen_array = [];
			foreach ($addon_data['data'] as $k => $v) {
				$addon_event = require_once dirname(__DIR__, 2) . '/addon/'.$v['name'].'/config/event.php';
				$listen = isset($addon_event['listen']) ? $addon_event['listen'] : [];
				if(!empty($listen)) {
					$listen_array[] = $listen;
				}
			}
			Cache::tag("addon")->set("addon_event_list", $listen_array);
		} else {
			$listen_array = $cache;
		}
		if(!empty($listen_array)) {
			foreach ($listen_array as $k => $listen) {
				if(!empty($listen)) {
					Event::listenEvents($listen);
				}
			}
		}
	}
}