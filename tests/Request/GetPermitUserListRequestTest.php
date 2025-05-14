<?php

namespace WechatWorkMsgAuditBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkMsgAuditBundle\Request\GetPermitUserListRequest;

class GetPermitUserListRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetPermitUserListRequest();
        $this->assertEquals('/cgi-bin/msgaudit/get_permit_user_list', $request->getRequestPath());
    }
    
    public function testGetRequestOptionsWithNullType(): void
    {
        $request = new GetPermitUserListRequest();
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertEmpty($options['json']);
    }
    
    public function testGetRequestOptionsWithType(): void
    {
        $request = new GetPermitUserListRequest();
        $request->setType(1);
        
        $options = $request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('type', $options['json']);
        $this->assertEquals(1, $options['json']['type']);
    }
    
    public function testTypeGetterSetter(): void
    {
        $request = new GetPermitUserListRequest();
        
        // 默认值应为null
        $this->assertNull($request->getType());
        
        // 设置值
        $request->setType(2);
        $this->assertEquals(2, $request->getType());
        
        // 设置null
        $request->setType(null);
        $this->assertNull($request->getType());
    }
    
    public function testAgentAwareIntegration(): void
    {
        $request = new GetPermitUserListRequest();
        
        // 验证AgentAware trait的方法存在
        $this->assertTrue(method_exists($request, 'setAgent'));
        $this->assertTrue(method_exists($request, 'getAgent'));
    }
} 