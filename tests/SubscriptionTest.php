<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/SubscriptionManager.php';

class SubscriptionTest extends TestCase {
    
    public function testDaysRemainingCalculation() {
        $sub = new \App\SubscriptionManager();
        
        // اشتراك 30 يوم، استهلكنا منهم 10، المفروض يفضل 20
        $result = $sub->getDaysRemaining(30, 10);
        
        $this->assertEquals(20, $result);
    }

    public function testExpiredSubscription() {
        $sub = new \App\SubscriptionManager();
        
        // اشتراك 30 يوم، استهلكنا 35 يوم، المفروض يرجع 0
        $result = $sub->getDaysRemaining(30, 35);
        
        $this->assertEquals(0, $result);
    }
}