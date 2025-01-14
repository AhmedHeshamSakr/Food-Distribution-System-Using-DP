<?php

require_once __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
//use Kreait\Firebase\ServiceAccount;

if (class_exists('Kreait\Firebase\ServiceAccount')) {
    echo "ServiceAccount class loaded successfully!";
} else {
    echo "ServiceAccount class not found!";
}
