<?php

namespace WechatWorkMsgAuditBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Request\GetPermitUserListRequest;

class GetPermitUserListRequestTest extends TestCase
{
    public function test_request_path_is_correct(): void
    {
        $request = new GetPermitUserListRequest();
        
        $this->assertSame('/cgi-bin/msgaudit/get_permit_user_list', $request->getRequestPath());
    }

    public function test_request_options_with_no_type(): void
    {
        $request = new GetPermitUserListRequest();
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertSame([], $options['json']);
    }

    public function test_request_options_with_type_set(): void
    {
        $request = new GetPermitUserListRequest();
        $request->setType(1);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertSame(1, $options['json']['type']);
    }

    public function test_type_getter_and_setter(): void
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

    public function test_type_boundary_values(): void
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

    public function test_request_options_with_zero_type(): void
    {
        $request = new GetPermitUserListRequest();
        $request->setType(0);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertSame(0, $options['json']['type']);
    }

    public function test_multiple_type_changes(): void
    {
        $request = new GetPermitUserListRequest();
        
        // 多次设置type
        $request->setType(1);
        $request->setType(2);
        $request->setType(3);
        
        $this->assertSame(3, $request->getType());
        
        $options = $request->getRequestOptions();
        $this->assertSame(3, $options['json']['type']);
    }

    public function test_request_implements_agent_aware(): void
    {
        $request = new GetPermitUserListRequest();
        
        // 检查AgentAware trait的方法是否可用
        $this->assertTrue(method_exists($request, 'getAgent'));
        $this->assertTrue(method_exists($request, 'setAgent'));
    }
} 