<?php

namespace BsPaySdk\request;

use BsPaySdk\enums\FunctionCodeEnum;

/**
 * 个人商户基本信息入驻(2022)
 *
 * @author sdk-generator
 * @Description
 */
class V2MerchantBasicdataIndvRequest extends BaseRequest
{

    /**
     * 请求流水号
     */
    private $reqSeqId;
    /**
     * 请求日期
     */
    private $reqDate;
    /**
     * 上级主体ID
     */
    private $upperHuifuId;
    /**
     * 商户名
     */
    private $regName;
    /**
     * 经营省
     */
    private $provId;
    /**
     * 经营市
     */
    private $areaId;
    /**
     * 经营区
     */
    private $districtId;
    /**
     * 经营详细地址
     */
    private $detailAddr;
    /**
     * 联系人姓名
     */
    private $contactName;
    /**
     * 联系人手机号
     */
    private $contactMobileNo;
    /**
     * 联系人电子邮箱
     */
    private $contactEmail;
    /**
     * 结算卡信息配置
     */
    private $cardInfo;


    /**
     * 	所属行业
     */
    private $mcc;
    /**
     * 	场景类型
     */
    private $sceneType;
    /**
     * 	负责人证件号码
     */
    private $legalCertNo;
    /**
     * 	负责人证件有效期开始日期
     */
    private $legalCertBeginDate;
    /**
     * 	负责人证件有效期类型
     */
    private $legalCertValidityType;
    /**
     * 	负责人证件有效期截止日
     */
    private $legalCertEndDate;
    /**
     * 	负责人身份证地址
     */
    private $legalAddr;
    /**
     * 	负责人身份证国徽面
     */
    private $legalCertBackPic;
    /**
     * 	负责人身份证人像面
     */
    private $legalCertFrontPic;

    /**
     * 	结算卡正面
     */
    private $settleCardFrontPic;

    //回调
    private $asyncReturnUrl;

    public function getFunctionCode() {
        return FunctionCodeEnum::$V2_MERCHANT_BASICDATA_INDV;
    }


    public function getReqSeqId() {
        return $this->reqSeqId;
    }

    public function setReqSeqId($reqSeqId) {
        $this->reqSeqId = $reqSeqId;
    }

    public function getReqDate() {
        return $this->reqDate;
    }

    public function setReqDate($reqDate) {
        $this->reqDate = $reqDate;
    }

    public function getUpperHuifuId() {
        return $this->upperHuifuId;
    }

    public function setUpperHuifuId($upperHuifuId) {
        $this->upperHuifuId = $upperHuifuId;
    }

    public function getRegName() {
        return $this->regName;
    }

    public function setRegName($regName) {
        $this->regName = $regName;
    }

    public function getProvId() {
        return $this->provId;
    }

    public function setProvId($provId) {
        $this->provId = $provId;
    }

    public function getAreaId() {
        return $this->areaId;
    }

    public function setAreaId($areaId) {
        $this->areaId = $areaId;
    }

    public function getDistrictId() {
        return $this->districtId;
    }

    public function setDistrictId($districtId) {
        $this->districtId = $districtId;
    }

    public function getDetailAddr() {
        return $this->detailAddr;
    }

    public function setDetailAddr($detailAddr) {
        $this->detailAddr = $detailAddr;
    }

    public function getContactName() {
        return $this->contactName;
    }

    public function setContactName($contactName) {
        $this->contactName = $contactName;
    }

    public function getContactMobileNo() {
        return $this->contactMobileNo;
    }

    public function setContactMobileNo($contactMobileNo) {
        $this->contactMobileNo = $contactMobileNo;
    }

    public function getContactEmail() {
        return $this->contactEmail;
    }

    public function setContactEmail($contactEmail) {
        $this->contactEmail = $contactEmail;
    }

    public function getCardInfo() {
        return $this->cardInfo;
    }

    public function setCardInfo($cardInfo) {
        $this->cardInfo = $cardInfo;
    }

    public function getMcc() {
        return $this->mcc;
    }

    public function setMcc($mcc) {
        $this->mcc = $mcc;
    }

    public function getSceneType() {
        return $this->sceneType;
    }

    public function setSceneType($sceneType) {
        $this->sceneType = $sceneType;
    }

    public function getLegalCertNo() {
        return $this->legalCertNo;
    }

    public function setLegalCertNo($legalCertNo) {
        $this->legalCertNo = $legalCertNo;
    }

    public function getLegalCertBeginDate() {
        return $this->legalCertBeginDate;
    }

    public function setLegalCertBeginDate($legalCertBeginDate) {
        $this->legalCertBeginDate = $legalCertBeginDate;
    }

    public function getLegalCertValidityType() {
        return $this->legalCertValidityType;
    }

    public function setLegalCertValidityType($legalCertValidityType) {
        $this->legalCertValidityType = $legalCertValidityType;
    }

    public function getLegalCertEndDate() {
        return $this->legalCertEndDate;
    }

    public function setLegalCertEndDate($legalCertEndDate) {
        $this->legalCertEndDate = $legalCertEndDate;
    }

    public function getLegalAddr() {
        return $this->legalAddr;
    }

    public function setLegalAddr($legalAddr) {
        $this->legalAddr = $legalAddr;
    }

    public function getLegalCertBackPic() {
        return $this->legalCertBackPic;
    }

    public function setLegalCertBackPic($legalCertBackPic) {
        $this->legalCertBackPic = $legalCertBackPic;
    }

    public function getLegalCertFrontPic() {
        return $this->legalCertFrontPic;
    }

    public function setLegalCertFrontPic($legalCertFrontPic) {
        $this->legalCertFrontPic = $legalCertFrontPic;
    }

    public function getSettleCardFrontPic() {
        return $this->settleCardFrontPic;
    }

    public function setSettleCardFrontPic($settleCardFrontPic) {
        $this->settleCardFrontPic = $settleCardFrontPic;
    }

    public function getAsyncReturnUrl() {
        return $this->asyncReturnUrl;
    }

    public function setAsyncReturnUrl($asyncReturnUrl) {
        $this->asyncReturnUrl = $asyncReturnUrl;
    }
}
