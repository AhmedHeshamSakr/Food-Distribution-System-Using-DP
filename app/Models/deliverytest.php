<?php

require_once 'Delivery.php'; // Adjust this path to where the Delivery class file is located
require_once '../../config/DB.php';

class DeliveryTest
{
    public static function runTests()
    {
        echo "Running Delivery Tests...\n";

        // Test results counter
        $testsPassed = 0;
        $testsFailed = 0;

        // Test inserting a new delivery
        echo "Testing Delivery Insertion...\n";
        $delivery = new Delivery(
            '2024-12-01',
            'Warehouse A',
            'Customer B',
            1,
            'pending',
            'Delivering electronics'
        );
        $delivery->insertDelivery($delivery);

        if ($delivery->getDeliveryID()) {
            echo "✔ Delivery insertion test passed.\n";
            $testsPassed++;
        } else {
            echo "✘ Delivery insertion test failed.\n";
            $testsFailed++;
        }

        // Test retrieving and verifying initial values
        echo "Testing Getter Methods...\n";
        self::assertEqual($delivery->getDeliveryDate(), '2024-12-01', "Delivery Date", $testsPassed, $testsFailed);
        self::assertEqual($delivery->getStartLocation(), 'Warehouse A', "Start Location", $testsPassed, $testsFailed);
        self::assertEqual($delivery->getEndLocation(), 'Customer B', "End Location", $testsPassed, $testsFailed);
        self::assertEqual($delivery->getDeliveryGuy(), 1, "Delivery Guy ID", $testsPassed, $testsFailed);
        self::assertEqual($delivery->getCurrentStatus(), 'pending', "Initial Status", $testsPassed, $testsFailed);
        self::assertEqual($delivery->getDeliveryDetails(), 'Delivering electronics', "Delivery Details", $testsPassed, $testsFailed);

        // Test state transitions
        echo "Testing State Transitions...\n";
        try {
            $delivery->request(); // pending -> delivering
            self::assertEqual($delivery->getCurrentStatus(), 'delivering', "Transition to Delivering", $testsPassed, $testsFailed);
            
            $delivery->request(); // delivering -> delivered
            self::assertEqual($delivery->getCurrentStatus(), 'delivered', "Transition to Delivered", $testsPassed, $testsFailed);
        } catch (Exception $e) {
            echo "✘ State transition failed with exception: " . $e->getMessage() . "\n";
            $testsFailed++;
        }

        // Test invalid state transition
        echo "Testing Invalid State Transition...\n";
        try {
            $delivery->request(); // Should throw exception as already delivered
            echo "✘ Should have thrown exception for invalid transition from delivered state.\n";
            $testsFailed++;
        } catch (Exception $e) {
            if ($e->getMessage() === "Delivery is already in final state") {
                echo "✔ Correctly threw exception for invalid transition.\n";
                $testsPassed++;
            } else {
                echo "✘ Unexpected exception message: " . $e->getMessage() . "\n";
                $testsFailed++;
            }
        }

        // Test creating delivery with custom initial state
        echo "Testing Custom Initial State...\n";
        $customDelivery = new Delivery(
            '2024-12-01',
            'Warehouse A',
            'Customer B',
            1,
            'delivering',
            'Test delivery'
        );
        $customDelivery->insertDelivery($customDelivery);
        self::assertEqual($customDelivery->getCurrentStatus(), 'delivering', "Custom Initial State", $testsPassed, $testsFailed);

        // Test updating delivery details
        echo "Testing Delivery Details Update...\n";
        $delivery->setDeliveryDetails('Updated details for delivery');
        self::assertEqual($delivery->getDeliveryDetails(), 'Updated details for delivery', "Updated Delivery Details", $testsPassed, $testsFailed);

        // Test deletion
        echo "Testing Delivery Deletion...\n";
        $result = $delivery->deleteDelivery();
        if ($result === true) {
            echo "✔ Delivery deletion test passed.\n";
            $testsPassed++;
        } else {
            echo "✘ Delivery deletion test failed.\n";
            $testsFailed++;
        }

        // Summary of tests
        echo "\nTest Summary:\n";
        echo "Tests Passed: $testsPassed\n";
        echo "Tests Failed: $testsFailed\n";
        echo $testsFailed === 0 ? "All tests passed successfully!" : "Some tests failed.";
    }

    // Helper function for assertions with feedback
    private static function assertEqual($actual, $expected, $testName, &$testsPassed, &$testsFailed)
    {
        if ($actual === $expected) {
            echo "✔ $testName test passed.\n";
            $testsPassed++;
        } else {
            echo "✘ $testName test failed. Expected '$expected', got '$actual'.\n";
            $testsFailed++;
        }
    }
}

// Run all tests
DeliveryTest::runTests();

?>