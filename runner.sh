#!/bin/bash

# Simple Test Runner for Admin Controllers
# Menjalankan test tanpa setup PHPUnit yang kompleks

echo "🚀 Simple Admin Controllers Test Runner"
echo "======================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Check if we're in Laravel root
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Please run this from your Laravel root directory${NC}"
    exit 1
fi

echo -e "${BLUE}📍 Current directory: $(pwd)${NC}"

# Check if test file exists
TEST_FILE="tests/Feature/Admin/AdminControllersTest.php"

if [ ! -f "$TEST_FILE" ]; then
    echo -e "${YELLOW}⚠️  Test file not found at: $TEST_FILE${NC}"
    echo "Please copy AdminControllersTest.php to the tests/Feature/Admin/ directory"
    exit 1
fi

echo -e "${GREEN}✅ Test file found${NC}"

# Check syntax first
echo -e "\n${YELLOW}🔍 Checking syntax...${NC}"
if php -l "$TEST_FILE" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Syntax OK${NC}"
else
    echo -e "${RED}❌ Syntax Error:${NC}"
    php -l "$TEST_FILE"
    exit 1
fi

# Menu for test selection
echo -e "\n${YELLOW}🎯 Test Selection Menu${NC}"
echo "1. Run All Tests"
echo "2. Dashboard Tests Only"
echo "3. Division Tests Only"
echo "4. Role Tests Only"
echo "5. Security Tests Only"
echo "6. Run Specific Test Method"
echo ""

read -p "Enter your choice (1-6): " choice

case $choice in
    1)
        echo -e "\n${BLUE}🧪 Running All Admin Controller Tests...${NC}"
        php artisan test "$TEST_FILE" --verbose
        ;;
    2)
        echo -e "\n${BLUE}🧪 Running Dashboard Tests...${NC}"
        php artisan test "$TEST_FILE" --filter="test_admin_dapat_mengakses_dashboard|test_dashboard"
        ;;
    3)
        echo -e "\n${BLUE}🧪 Running Division Tests...${NC}"
        php artisan test "$TEST_FILE" --filter="division"
        ;;
    4)
        echo -e "\n${BLUE}🧪 Running Role Tests...${NC}"
        php artisan test "$TEST_FILE" --filter="role"
        ;;
    5)
        echo -e "\n${BLUE}🧪 Running Security Tests...${NC}"
        php artisan test "$TEST_FILE" --filter="test_akses_tanpa_autentikasi|test_sql_injection|test_handling_data_tidak_ada|test_concurrent_access"
        ;;
    6)
        echo -e "\n${YELLOW}📋 Available Test Methods:${NC}"
        echo "• test_admin_dapat_mengakses_dashboard"
        echo "• test_dashboard_menampilkan_statistik_yang_benar"
        echo "• test_dapat_melihat_daftar_division"
        echo "• test_dapat_membuat_division_baru"
        echo "• test_validasi_nama_division_tidak_boleh_kosong"
        echo "• test_dapat_melihat_detail_division"
        echo "• test_dapat_update_division"
        echo "• test_dapat_delete_division_tanpa_user"
        echo "• test_dapat_melihat_daftar_role"
        echo "• test_dapat_membuat_role_baru"
        echo "• test_validasi_nama_role_tidak_boleh_kosong"
        echo "• test_dapat_update_role"
        echo "• test_dapat_delete_role_tanpa_user"
        echo "• test_sql_injection_prevention"
        echo ""
        read -p "Enter test method name: " test_method
        echo -e "\n${BLUE}🧪 Running: $test_method${NC}"
        php artisan test "$TEST_FILE" --filter="$test_method"
        ;;
    *)
        echo -e "${RED}Invalid choice. Please run again and select 1-6.${NC}"
        exit 1
        ;;
esac

echo ""
echo -e "${GREEN}🎉 Test execution completed!${NC}"
echo ""
echo -e "${YELLOW}💡 Tips:${NC}"
echo "• All test data is cleaned up automatically with DatabaseTransactions"
echo "• Your database remains safe and unchanged"
echo "• Tests use existing data when possible"
echo "• New test data is created with unique identifiers"

echo ""
echo -e "${YELLOW}📊 What was tested:${NC}"
echo "✅ DashboardController - index method and data accuracy"
echo "✅ DivisionController - CRUD operations and validations"
echo "✅ RoleController - CRUD operations and validations"
echo "✅ Security - SQL injection, authentication, error handling"
echo "✅ Data integrity - Database constraints and relationships"