# Dashboard Widgets Permission Analysis

## Current Dashboard Widgets and Their Permission Requirements

### 1. **Financial Summary Cards** (New - Need Permission Checks)
- **Total Income Card**: Currently no permission check
- **Total Expense Card**: Currently no permission check  
- **Fee Collection Card**: Currently no permission check
- **Net Profit/Loss Card**: Currently no permission check

**Required Permissions:**
- `income_donut_graph` (can_view) - for Income card
- `expense_donut_graph` (can_view) - for Expense card
- `fees_collection` (can_view) - for Fee Collection card
- Both income and expense permissions - for Net Profit card

### 2. **Progress Bar Widgets** (Already Have Permission Checks)
- **Fees Awaiting Payment**: `fees_awaiting_payment_widegts` (can_view)
- **Converted Leads**: `conveted_leads_widegts` (can_view)
- **Staff Present Today**: `staff_present_today_widegts` (can_view)
- **Student Present Today**: `student_present_today_widegts` (can_view)

### 3. **Chart Widgets** (Already Have Permission Checks)
- **Monthly Collection/Expense Bar Chart**: `fees_collection_and_expense_monthly_chart` (can_view)
- **Yearly Collection/Expense Line Chart**: `fees_collection_and_expense_yearly_chart` (can_view)
- **Income Donut Chart**: `income_donut_graph` (can_view)
- **Expense Donut Chart**: `expense_donut_graph` (can_view)

### 4. **Info Box Widgets** (Already Have Permission Checks)
- **Monthly Fee Collection**: `Monthly fees_collection_widget` (can_view)
- **Monthly Expense**: `monthly_expense_widget` (can_view)
- **Student Count**: `student_count_widget` (can_view)
- **Staff Role Count**: `staff_role_count_widget` (can_view)

### 5. **Overview Widgets** (Already Have Permission Checks)
- **Fees Overview**: `fees_overview_widegts` (can_view)
- **Enquiry Overview**: `enquiry_overview_widegts` (can_view)
- **Library Overview**: `book_overview_widegts` (can_view)
- **Student Today Attendance**: `today_attendance_widegts` (can_view)

### 6. **Calendar Widget** (Already Has Permission Check)
- **Calendar To-Do List**: `calendar_to_do_list` (can_view)

## Missing Permission Categories

The following permission categories need to be added to the database:

1. **Financial Summary Cards Permissions**:
   - `financial_summary_income` - for Total Income card
   - `financial_summary_expense` - for Total Expense card  
   - `financial_summary_fees` - for Fee Collection card
   - `financial_summary_profit` - for Net Profit/Loss card

## Module Dependencies

All widgets also require their respective modules to be active:
- `fees_collection` module - for fee-related widgets
- `income` module - for income-related widgets
- `expense` module - for expense-related widgets
- `front_office` module - for enquiry widgets
- `library` module - for library widgets
- `student_attendance` module - for attendance widgets
- `calendar_to_do_list` module - for calendar widget

## Current Issues

1. **New Financial Summary Cards** have no permission checks
2. **AJAX getDashboardSummary** method doesn't check permissions before returning data
3. **Dashboard controller** loads all data regardless of permissions (inefficient)

## Recommended Solution

1. Add missing permission categories to database
2. Update dashboard controller to check permissions before loading data
3. Update AJAX method to respect permissions
4. Ensure all widgets are properly protected
