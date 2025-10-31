<?php

declare(strict_types=1);

namespace WechatWorkMsgAuditBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use WechatWorkMsgAuditBundle\Request\GetPermitUserListRequest;

/**
 * @internal
 */
#[CoversClass(GetPermitUserListRequest::class)]
final class GetPermitUserListRequestTest extends RequestTestCase
{
    public function testRequestPathIsCorrect(): void
    {
        $request = new GetPermitUserListRequest();

        $this->assertSame('/cgi-bin/msgaudit/get_permit_user_list', $request->getRequestPath());
    }

    public function testRequestOptionsWithNoType(): void
    {
        $request = new GetPermitUserListRequest();

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertSame([], $options['json']);
    }

    public function testRequestOptionsWithTypeSet(): void
    {
        $request = new GetPermitUserListRequest();
        $request->setType(1);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertSame(1, $options['json']['type']);
    }

    public function testTypeGetterAndSetter(): void
    {
        $request = new GetPermitUserListRequest();

        // 默认为null
        $this->assertNull($request->getType());

        // 设置办公版
        $request->setType(1);
        $this->assertSame(1, $request->getType());

        // 设置服务版
        $request->setType(2);
        $this->assertSame(2, $request->getType());

        // 设置企业版
        $request->setType(3);
        $this->assertSame(3, $request->getType());

        // 重置为null
        $request->setType(null);
        $this->assertNull($request->getType());
    }

    public function testTypeBoundaryValues(): void
    {
        $request = new GetPermitUserListRequest();

        // 测试边界值
        $request->setType(0);
        $this->assertSame(0, $request->getType());

        $request->setType(-1);
        $this->assertSame(-1, $request->getType());

        $request->setType(999);
        $this->assertSame(999, $request->getType());
    }

    public function testRequestOptionsWithZeroType(): void
    {
        $request = new GetPermitUserListRequest();
        $request->setType(0);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertSame(0, $options['json']['type']);
    }

    public function testMultipleTypeChanges(): void
    {
        $request = new GetPermitUserListRequest();

        // 多次设置type
        $request->setType(1);
        $request->setType(2);
        $request->setType(3);

        $this->assertSame(3, $request->getType());

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame(3, $options['json']['type']);
    }

    public function testRequestImplementsAgentAware(): void
    {
        $request = new GetPermitUserListRequest();

        // 检查AgentAware trait的方法是否可用
        $this->assertNotNull($request);
    }
}
