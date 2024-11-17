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
        self::assertEqual($delivery->getStatus(), 'pending', "Status", $testsPassed, $testsFailed);
        self::assertEqual($delivery->getDeliveryDetails(), 'Delivering electronics', "Delivery Details", $testsPassed, $testsFailed);

        // Test updating delivery date
        echo "Testing Delivery Date Update...\n";
        $delivery->setDeliveryDate('2024-12-02');
        self::assertEqual($delivery->getDeliveryDate(), '2024-12-02', "Updated Delivery Date", $testsPassed, $testsFailed);

        // Test updating start location
        echo "Testing Start Location Update...\n";
        $delivery->setStartLocation('Warehouse B');
        self::assertEqual($delivery->getStartLocation(), 'Warehouse B', "Updated Start Location", $testsPassed, $testsFailed);

        // Test updating end location
        echo "Testing End Location Update...\n";
        $delivery->setEndLocation('Customer C');
        self::assertEqual($delivery->getEndLocation(), 'Customer C', "Updated End Location", $testsPassed, $testsFailed);

        // Test updating status
        echo "Testing Status Update...\n";
        $delivery->setStatus('delivering');
        self::assertEqual($delivery->getStatus(), 'delivering', "Updated Status", $testsPassed, $testsFailed);

        // Attempt setting invalid status
        echo "Testing Invalid Status Update...\n";
        $delivery->setStatus('invalid_status');
        self::assertEqual($delivery->getStatus(), 'delivering', "Status should remain 'delivering' on invalid input", $testsPassed, $testsFailed);

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
