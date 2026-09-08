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
     * 智能解析签到码/扫码结果中的 member_id
     */
    protected function parseMemberId($code)
    {
        if (empty($code)) return 0;

        // 1. 如果本身是正整数
        if (is_numeric($code) && (int)$code > 0) {
            return (int)$code;
        }

        if (!is_string($code)) return 0;

        // 多次 urldecode，防止小程序或扫码对参数进行了多层编码
        $str = urldecode($code);
        $str = urldecode($str);

        // 2. 如果是 JSON 格式
        if (strpos(trim($str), '{') === 0) {
            $json = json_decode($str, true);
            if (!empty($json)) {
                if (!empty($json['member_id'])) return (int)$json['member_id'];
                if (!empty($json['code'])) return $this->parseMemberId($json['code']);
                if (!empty($json['scene'])) return $this->parseMemberId($json['scene']);
            }
        }

        // 3. 匹配 member_id 分隔符 数字（支持 =、-、:、_、/、空格 等任何常见分隔符）
        if (preg_match('/member_?id[=\-_:\/\s]+(\d+)/i', $str, $m)) {
            return (int)$m[1];
        }

        // 4. 匹配 scene 参数中的内容（如 ?scene=member_id-4 或 &scene=4）
        if (preg_match('/scene[=\-_:\/\s]+([^&?#]+)/i', $str, $m)) {
            $scene = urldecode($m[1]);
            // scene 里可能包含 member_id-4 或 member_id=4
            if (preg_match('/member_?id[=\-_:\/\s]+(\d+)/i', $scene, $m2)) {
                return (int)$m2[1];
            }
            if (preg_match('/(?:uid|suid|user_id)[=\-_:\/\s]+(\d+)/i', $scene, $m2)) {
                return (int)$m2[1];
            }
            if (is_numeric($scene) && (int)$scene > 0) {
                return (int)$scene;
            }
        }

        // 5. 匹配 uid 或 suid 等别名（支持 uid-4, suid=4 等）
        if (preg_match('/(?:suid|user_?id|uid)[=\-_:\/\s]+(\d+)/i', $str, $m)) {
            return (int)$m[1];
        }

        // 6. 匹配 sign_4 或 sign-4 等文件名或标识格式
        if (preg_match('/sign[_\-](\d+)/i', $str, $m)) {
            return (int)$m[1];
        }

        // 7. 匹配路由中携带的 id 参数，例如 signCode?id=4
        if (preg_match('/(?:signCode|sign)[\/?&=#]+(?:id=)?(\d+)/i', $str, $m)) {
            return (int)$m[1];
        }

        return 0;
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

        $member_id = $this->parseMemberId($code);

        if (empty($member_id)) {
            return $this->response([
                'code' => -1,
                'message' => '未能识别有效的会员签到码（扫码内容: ' . mb_substr($code, 0, 80) . '）'
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