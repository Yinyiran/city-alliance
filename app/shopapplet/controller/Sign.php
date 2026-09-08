<?php
namespace app\shopapplet\controller;

use app\model\shopapplet\Shop as ShopModel;
use app\model\shopapplet\ShopEmployee as ShopEmployeeModel;
use Carbon\Carbon;
use app\model\order\OrderCommon;
use app\model\message\Message;
use app\model\shopapplet\Records as RecordsModel;
use app\model\signin\Records as SigninRecordsModel;
use think\facade\Db;

/**
 * 商家端 - 会员签到管理
 */
class Sign extends Common
{
    protected $recordmodel;

    public function __construct()
    {
        // 执行父类构造函数
        parent::__construct();
        $this->recordmodel = new RecordsModel();
    }

    /**
     * 获取商铺ID辅助函数
     */
    protected function getShopId()
    {
        return request()->param("shop_id") ?: ($this->site_id ?: (session("shop_id") ?: session("site_id")));
    }

    /**
     * 签到首页统计数据
     */
    public function index()
    {
        $shop_id = $this->getShopId();
        $result = [
            'code' => 0,
            'data' => [
                'today_count' => $this->recordmodel->todaycount($shop_id),
                'total_count' => $this->recordmodel->totalcount($shop_id)
            ],
            'message' => '操作成功'
        ];
        return $this->response($result);
    }

    /**
     * 签到列表
     */
    public function getList()
    {
        $params = request()->param();
        $page = !empty($params["page"]) ? (int)$params["page"] : 1;
        $sortbycount = request()->has("sortcount") ? $params["sortcount"] : 1;
        $sortbytime = request()->has("sorttime") ? $params["sorttime"] : 1;
        $searchtxt = request()->has("searchtxt") ? trim($params["searchtxt"]) : "";

        $shop_id = $this->getShopId();
        $res = $this->recordmodel->signlistbyshop($shop_id, $searchtxt, $sortbycount, $sortbytime, $page);
        return $this->response($res);
    }

    /**
     * 商家扫码为会员执行签到
     */
    public function dosignin()
    {
        $code = request()->param("code") ?: (request()->param("member_id") ?: request()->param("scene"));
        if (empty($code)) {
            return $this->response([
                'code' => -1,
                'message' => '缺少签到码或会员信息'
            ]);
        }

        $member_id = 0;
        // 尝试多种格式解析 member_id
        if (is_numeric($code)) {
            $member_id = (int)$code;
        } elseif (is_string($code)) {
            // 1. 如果是 JSON 格式
            if (strpos(trim($code), '{') === 0) {
                $json = json_decode($code, true);
                if (!empty($json['member_id'])) {
                    $member_id = (int)$json['member_id'];
                }
            }
            // 2. 如果包含 member_id=
            if (!$member_id && preg_match('/member_id[=:](\d+)/i', $code, $matches)) {
                $member_id = (int)$matches[1];
            }
            // 3. 如果包含 scene=member_id%3D123 或 scene=member_id=123
            if (!$member_id && preg_match('/scene[=:]([^&]+)/i', $code, $matches)) {
                $scene = urldecode($matches[1]);
                if (preg_match('/member_id[=:](\d+)/i', $scene, $m2)) {
                    $member_id = (int)$m2[1];
                } elseif (is_numeric($scene)) {
                    $member_id = (int)$scene;
                }
            }
        }

        if (empty($member_id)) {
            return $this->response([
                'code' => -1,
                'message' => '未能识别有效的会员签到码'
            ]);
        }

        $shop_id = $this->getShopId();
        if (empty($shop_id)) {
            return $this->response([
                'code' => -1,
                'message' => '未获取到当前商家门店信息'
            ]);
        }

        // 查询会员基本信息
        $member = Db::name('member')->field('member_id, nickname, headimg, mobile, member_level_name')->where('member_id', $member_id)->find();
        if (empty($member)) {
            return $this->response([
                'code' => -1,
                'message' => '会员信息不存在，无法完成签到'
            ]);
        }

        // 调用签到业务模型执行签到
        $signin_records = new SigninRecordsModel();
        $result = $signin_records->shopsignin($member_id, $shop_id);

        return $this->response([
            'code' => $result['code'],
            'message' => $result['message'],
            'data' => [
                'member' => $member,
                'member_id' => $member_id,
                'shop_id' => $shop_id
            ]
        ]);
    }
}