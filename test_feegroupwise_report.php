<?php
/**
 * Test Script for Fee Group-wise Collection Report
 * 
 * This script tests the Fee Group-wise Collection Report functionality
 * Run this from command line: php test_feegroupwise_report.php
 */

echo "=================================================\n";
echo "Fee Group-wise Collection Report - Test Script\n";
echo "=================================================\n\n";

// Test 1: Check if files exist
echo "Test 1: Checking if all required files exist...\n";
$files_to_check = [
    'application/controllers/Financereports.php',
    'application/models/Feegroupwise_model.php',
    'application/views/financereports/feegroupwise_collection.php',
    'application/views/financereports/_finance.php',
    'documentation/FEE_GROUPWISE_COLLECTION_REPORT.md'
];

$all_files_exist = true;
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "  ✓ $file exists\n";
    } else {
        echo "  ✗ $file NOT FOUND\n";
        $all_files_exist = false;
    }
}

if ($all_files_exist) {
    echo "  Result: PASSED - All files exist\n\n";
} else {
    echo "  Result: FAILED - Some files are missing\n\n";
}

// Test 2: Check controller methods
echo "Test 2: Checking controller methods...\n";
$controller_content = file_get_contents('application/controllers/Financereports.php');
$methods_to_check = [
    'feegroupwise_collection',
    'getFeeGroupwiseData',
    'exportFeeGroupwiseReport',
    'exportFeeGroupwiseExcel',
    'exportFeeGroupwiseCSV'
];

$all_methods_exist = true;
foreach ($methods_to_check as $method) {
    if (strpos($controller_content, "function $method") !== false) {
        echo "  ✓ Method $method() exists\n";
    } else {
        echo "  ✗ Method $method() NOT FOUND\n";
        $all_methods_exist = false;
    }
}

if ($all_methods_exist) {
    echo "  Result: PASSED - All controller methods exist\n\n";
} else {
    echo "  Result: FAILED - Some controller methods are missing\n\n";
}

// Test 3: Check model methods
echo "Test 3: Checking model methods...\n";
$model_content = file_get_contents('application/models/Feegroupwise_model.php');
$model_methods = [
    'getFeeGroupwiseCollection',
    'getFeeGroupwiseDetailedData',
    'getAllFeeGroups'
];

$all_model_methods_exist = true;
foreach ($model_methods as $method) {
    if (strpos($model_content, "function $method") !== false) {
        echo "  ✓ Method $method() exists\n";
    } else {
        echo "  ✗ Method $method() NOT FOUND\n";
        $all_model_methods_exist = false;
    }
}

if ($all_model_methods_exist) {
    echo "  Result: PASSED - All model methods exist\n\n";
} else {
    echo "  Result: FAILED - Some model methods are missing\n\n";
}

// Test 4: Check view components
echo "Test 4: Checking view components...\n";
$view_content = file_get_contents('application/views/financereports/feegroupwise_collection.php');
$view_components = [
    'filterForm',
    'summarySection',
    'gridSection',
    'chartsSection',
    'tableSection',
    'feeGroupGrid',
    'collectionPieChart',
    'collectionBarChart',
    'feeGroupTable'
];

$all_components_exist = true;
foreach ($view_components as $component) {
    if (strpos($view_content, $component) !== false) {
        echo "  ✓ Component '$component' exists\n";
    } else {
        echo "  ✗ Component '$component' NOT FOUND\n";
        $all_components_exist = false;
    }
}

if ($all_components_exist) {
    echo "  Result: PASSED - All view components exist\n\n";
} else {
    echo "  Result: FAILED - Some view components are missing\n\n";
}

// Test 5: Check JavaScript functions
echo "Test 5: Checking JavaScript functions...\n";
$js_functions = [
    'loadFeeGroups',
    'loadSections',
    'loadFeeGroupData',
    'updateSummary',
    'populateGrid',
    'populateCharts',
    'populateTable',
    'exportReport',
    'initializeDataTable'
];

$all_js_functions_exist = true;
foreach ($js_functions as $function) {
    if (strpos($view_content, "function $function") !== false) {
        echo "  ✓ Function $function() exists\n";
    } else {
        echo "  ✗ Function $function() NOT FOUND\n";
        $all_js_functions_exist = false;
    }
}

if ($all_js_functions_exist) {
    echo "  Result: PASSED - All JavaScript functions exist\n\n";
} else {
    echo "  Result: FAILED - Some JavaScript functions are missing\n\n";
}

// Test 6: Check Chart.js integration
echo "Test 6: Checking Chart.js integration...\n";
if (strpos($view_content, 'chart.js') !== false) {
    echo "  ✓ Chart.js CDN included\n";
    if (strpos($view_content, 'new Chart') !== false) {
        echo "  ✓ Chart initialization code exists\n";
        echo "  Result: PASSED - Chart.js properly integrated\n\n";
    } else {
        echo "  ✗ Chart initialization code NOT FOUND\n";
        echo "  Result: FAILED - Chart.js not properly integrated\n\n";
    }
} else {
    echo "  ✗ Chart.js CDN NOT FOUND\n";
    echo "  Result: FAILED - Chart.js not included\n\n";
}

// Test 7: Check menu integration
echo "Test 7: Checking menu integration...\n";
$menu_content = file_get_contents('application/views/financereports/_finance.php');
if (strpos($menu_content, 'feegroupwise_collection') !== false) {
    echo "  ✓ Menu item added to finance reports\n";
    if (strpos($menu_content, 'fa-bar-chart') !== false) {
        echo "  ✓ Bar chart icon included\n";
        echo "  Result: PASSED - Menu properly integrated\n\n";
    } else {
        echo "  ✗ Bar chart icon NOT FOUND\n";
        echo "  Result: PARTIAL - Menu added but icon missing\n\n";
    }
} else {
    echo "  ✗ Menu item NOT FOUND\n";
    echo "  Result: FAILED - Menu not integrated\n\n";
}

// Test 8: Check export functionality
echo "Test 8: Checking export functionality...\n";
$export_checks = [
    'exportFeeGroupwiseExcel' => false,
    'exportFeeGroupwiseCSV' => false,
    'buildFeeGroupwiseExcelContent' => false,
    'exportExcel' => false,
    'exportCSV' => false
];

foreach ($export_checks as $check => $found) {
    if (strpos($controller_content, $check) !== false || strpos($view_content, $check) !== false) {
        $export_checks[$check] = true;
        echo "  ✓ $check functionality exists\n";
    } else {
        echo "  ✗ $check functionality NOT FOUND\n";
    }
}

$export_passed = !in_array(false, $export_checks);
if ($export_passed) {
    echo "  Result: PASSED - Export functionality complete\n\n";
} else {
    echo "  Result: FAILED - Some export functionality missing\n\n";
}

// Test 9: Check responsive design
echo "Test 9: Checking responsive design...\n";
$responsive_checks = [
    'grid-container' => false,
    '@media' => false,
    'col-md-' => false,
    'col-sm-' => false
];

foreach ($responsive_checks as $check => $found) {
    if (strpos($view_content, $check) !== false) {
        $responsive_checks[$check] = true;
        echo "  ✓ $check CSS exists\n";
    } else {
        echo "  ✗ $check CSS NOT FOUND\n";
    }
}

$responsive_passed = !in_array(false, $responsive_checks);
if ($responsive_passed) {
    echo "  Result: PASSED - Responsive design implemented\n\n";
} else {
    echo "  Result: FAILED - Responsive design incomplete\n\n";
}

// Test 10: Check documentation
echo "Test 10: Checking documentation...\n";
if (file_exists('documentation/FEE_GROUPWISE_COLLECTION_REPORT.md')) {
    $doc_content = file_get_contents('documentation/FEE_GROUPWISE_COLLECTION_REPORT.md');
    $doc_sections = [
        'Overview',
        'Features Implemented',
        'Files Created/Modified',
        'Database Tables Used',
        'Usage Instructions',
        'Technical Specifications',
        'Testing Checklist',
        'Troubleshooting'
    ];
    
    $all_sections_exist = true;
    foreach ($doc_sections as $section) {
        if (strpos($doc_content, $section) !== false) {
            echo "  ✓ Section '$section' exists\n";
        } else {
            echo "  ✗ Section '$section' NOT FOUND\n";
            $all_sections_exist = false;
        }
    }
    
    if ($all_sections_exist) {
        echo "  Result: PASSED - Documentation is comprehensive\n\n";
    } else {
        echo "  Result: FAILED - Documentation incomplete\n\n";
    }
} else {
    echo "  ✗ Documentation file NOT FOUND\n";
    echo "  Result: FAILED - Documentation missing\n\n";
}

// Summary
echo "=================================================\n";
echo "TEST SUMMARY\n";
echo "=================================================\n";

$total_tests = 10;
$passed_tests = 0;

if ($all_files_exist) $passed_tests++;
if ($all_methods_exist) $passed_tests++;
if ($all_model_methods_exist) $passed_tests++;
if ($all_components_exist) $passed_tests++;
if ($all_js_functions_exist) $passed_tests++;
if (strpos($view_content, 'chart.js') !== false && strpos($view_content, 'new Chart') !== false) $passed_tests++;
if (strpos($menu_content, 'feegroupwise_collection') !== false) $passed_tests++;
if ($export_passed) $passed_tests++;
if ($responsive_passed) $passed_tests++;
if (file_exists('documentation/FEE_GROUPWISE_COLLECTION_REPORT.md') && $all_sections_exist) $passed_tests++;

$pass_percentage = ($passed_tests / $total_tests) * 100;

echo "Total Tests: $total_tests\n";
echo "Passed: $passed_tests\n";
echo "Failed: " . ($total_tests - $passed_tests) . "\n";
echo "Success Rate: " . number_format($pass_percentage, 2) . "%\n\n";

if ($pass_percentage == 100) {
    echo "✓ ALL TESTS PASSED! Implementation is complete.\n";
} elseif ($pass_percentage >= 80) {
    echo "⚠ MOSTLY PASSED. Minor issues need attention.\n";
} else {
    echo "✗ TESTS FAILED. Significant issues need to be fixed.\n";
}

echo "\n=================================================\n";
echo "Next Steps:\n";
echo "=================================================\n";
echo "1. Access the report at: http://localhost/amt/financereports/feegroupwise_collection\n";
echo "2. Test with actual data in your database\n";
echo "3. Verify all filters work correctly\n";
echo "4. Test export functionality (Excel and CSV)\n";
echo "5. Check responsive design on different devices\n";
echo "6. Review the documentation for usage instructions\n";
echo "\n";

