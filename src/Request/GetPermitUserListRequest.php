<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取会话内容存档开启成员列表
 * 企业需要使用会话内容存档应用secret所获取的accesstoken来调用（accesstoken如何获取？）；
 * 注：开启范围可设置为具体成员、部门、标签。通过此接口拉取成员列表，会将部门、标签进行打散处理，获取部门、标签范围内的全部成员。最终以成员userid的形式返回。
 *
 * @see https://developer.work.weixin.qq.com/document/path/91614
 */
class GetPermitUserListRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var int|null 拉取对应版本的开启成员列表。1表示办公版；2表示服务版；3表示企业版。非必填，不填写的时候返回全量成员列表。
     */
    private ?int $type = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/msgaudit/get_permit_user_list';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $json = [];
        if (null !== $this->getType()) {
            $json['type'] = $this->getType();
        }

        return [
            'json' => $json,
        ];
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): void
    {
        $this->type = $type;
    }
}
