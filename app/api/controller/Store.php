<?php


namespace app\api\controller;

use app\model\store\Store as StoreModel;

/**
 * 门店
 * @author Administrator
 *
 */
class Store extends BaseApi
{
	
	/**
	 * 列表信息
	 */
	public function page()
	{
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		if (empty($site_id)) {
			return $this->response($this->error('', 'REQUEST_SITE_ID'));
		}
		
		$store_model = new StoreModel();
		$condition = [
			[ 'site_id', "=", $site_id ],
			[ 'status', '=', 1 ],
			[ 'is_frozen', '=', 0 ]
		];
		
		$list = $store_model->getStorePageList($condition, $page, $page_size, 'create_time desc', 'store_id,store_name,telphone,store_image,site_id,site_name,address,full_address,longitude,latitude,open_date,username');
		
		return $this->response($list);
	}
	
	/**
	 * 基础信息
	 * @return false|string
	 */
	public function info()
	{
		$store_id = isset($this->params['store_id']) ? $this->params['store_id'] : 0;
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		if (empty($site_id)) {
			return $this->response($this->error('', 'REQUEST_SITE_ID'));
		}
		
		if (empty($store_id)) {
			return $this->response($this->error('', 'REQUEST_STORE_ID'));
		}
		$condition = [
			[ 'store_id', "=", $store_id ],
			[ 'site_id', "=", $site_id ],
			[ 'status', '=', 1 ]
		];
		$store_model = new StoreModel();
		$list = $store_model->getStoreInfo($condition, 'store_id,store_name,telphone,store_image,site_id,site_name,address,full_address,longitude,latitude,open_date,username');
		
		return $this->response($list);
	}

	/**
     * 门店列表
     */
    public function storeList()
    {
        $data = [
            'page' => isset($this->params['page']) ? $this->params['page'] : 1,
            'page_size' => isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS,
            'latitude' => $this->params["latitude"] ?? null,
            'longitude' => $this->params["longitude"] ?? null,
            'keyword' => $this->params['keyword'] ?? '', //搜索字段
            'website_id' => $this->params['website_id'] ?? 0,
        ];
        $store_model = new StoreModel();
        $result = $store_model->storeList($data);
        return $this->response($result);
	}

	/**
     * 门店详情
     */
    public function storeDetail()
    {
        $token = $this->checkToken();
        $store_id = $this->params['store_id'] ?? $this->params['id'] ?? 0;
        $site_id = $this->params['site_id'] ?? 0;
        $data = [
            'member_id' => $this->member_id,
            'store_id'  => $store_id,
            'site_id'   => $site_id,
        ];
        $store_model = new StoreModel();
        $result = $store_model->storeDetail($data);
        return $this->response($result);
	}

	/**
     * 城市下商家
     */
    public function websiteStore()
    {
        $data = [
            'website_id' => $this->params['website_id'] ?? 0,
        ];
        $store_model = new StoreModel();
        $result = $store_model->websiteStore($data);
        return $this->response($result);
	}
	
}