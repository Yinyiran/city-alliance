<?php
/**

 */
namespace app\model\system;

use app\model\BaseModel;
/**
 * 活动整体管理
 */
class Promotion extends BaseModel
{
    /**
     * 获取营销活动展示
     */
    public function getPromotions()
    {
        $show = event("ShowPromotion", []);
        // halt($show);
        $admin_promotion = [];
        $shop_promotion = [];
        foreach ($show as $k => $v)
        {
            if(!empty($v['admin']))
            {
                $admin_promotion = array_merge($admin_promotion, $v['admin']);
            }
            if(!empty($v['shop']))
            {
                $shop_promotion = array_merge($shop_promotion, $v['shop']);
            }
        }
        foreach ($admin_promotion as $key => $value) {
            if (in_array($value['name'], ['goodscircle', 'gift', 'notes', 'shopcomponent'])) {
                unset($admin_promotion[$key]);
            }
        }
        
        return [
            'admin' => $admin_promotion,
            'shop'  => $shop_promotion
        ];
    }

    /**
     * 获取营销类型
     */
    public function getPromotionType(){
        $promotion_type = event("PromotionType");
        $promotion_type[] = ["type" => "empty", "name" => "无营销活动"];
        return $promotion_type;
    }
}