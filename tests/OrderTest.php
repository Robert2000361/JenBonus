<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/OrderProcessor.php';

class OrderTest extends TestCase {
    public function testOrderProcessingSuccess() {
        $processor = new \App\OrderProcessor();
        $response = $processor->process("Ahmed", "DevOps Automation");
        
        $this->assertEquals("success", $response['status']);
    }

    public function testOrderProcessingInvalidName() {
        $processor = new \App\OrderProcessor();
        $response = $processor->process("Ab", "Cloud Migration"); 
        
        $this->assertEquals("error", $response['status']);
    }
}