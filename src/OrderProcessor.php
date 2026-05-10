<?php
namespace App;

class OrderProcessor {
    public function process($name, $service) {
        if (empty($name) || strlen($name) < 10) {
            return ["status" => "error", "message" => "Invalid Name"];
        }
        
        $validServices = ['Web Development', 'DevOps Automation', 'Cloud Migration'];
        if (!in_array($service, $validServices)) {
            return ["status" => "error", "message" => "Invalid Service"];
        }

        return [
            "status" => "success",
            "message" => "Order Confirmed for $name"
        ];
    }
}

