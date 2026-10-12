<?php


namespace app\model\api;


class VideoComment extends \think\Model
{
    protected $table = 'video_comment';
    // 关联子评论
    public function replies()
    {
        return $this->hasMany(self::class, 'pid')
            ->order('create_time', 'asc')
            ->with(['replies','member'=>function($query) {
                $query->field('member_id,nickname,headimg');
            }]); // 无限级关联
    }

    // 获取格式化内容
    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i', $value);
    }

    //用户
    public function member()
    {
        return $this->belongsTo(Member::class,'member_id','member_id');
    }
}