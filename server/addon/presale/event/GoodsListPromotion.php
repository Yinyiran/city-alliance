<?php
/**

 */


namespace addon\presale\event;

use addon\presale\model\Presale;

/**
 * 商品营销活动信息
 */
class GoodsListPromotion
{

	/**
	 * 商品营销活动信息
	 * @param $param
	 * @return array
	 */
	public function handle($param)
	{
		if (empty($param[ 'promotion' ]) || $param[ 'promotion' ] != 'presale') return [];
		$promotion_name = $param[ 'promotion_name' ] ?? '';
        $site_id = $param[ 'site_id' ] ?? 0;
		$condition[] = [
			['pp.status', '=', 1]
		];

        if($site_id > 0 ){
            $condition[] = ['pp.site_id', '=', $param[ 'site_id' ]];
        }
		if (!empty($promotion_name)) {
			$condition[] = ['pp.presale_name', 'like', '%' . $param[ 'promotion_name' ] . '%'];
		}
		$model = new Presale();
		$list = $model->getPresaleGoodsPageList($condition, $param[ 'page' ], $param[ 'page_size' ], 'pp.presale_id desc');
		return $list;
	}
}