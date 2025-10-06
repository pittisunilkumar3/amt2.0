# Advance Payment Feature - Testing Plan

## Overview
This document outlines the comprehensive testing plan for the newly implemented Advance Payment feature in the AMT Fee Collection system.

## Database Migration
**First Step: Run the database migration**
```sql
-- Execute the migration file
SOURCE application/migrations/002_create_advance_payment_tables.sql;
```

## Test Scenarios

### 1. Database Integration Tests
- [ ] Verify all new tables are created correctly
- [ ] Test foreign key constraints
- [ ] Verify indexes are created for performance
- [ ] Test the view `v_student_advance_balance` returns correct data

### 2. Advance Payment Creation Tests
- [ ] Create advance payment with valid data
- [ ] Test form validation for required fields
- [ ] Test amount validation (positive numbers only)
- [ ] Test payment mode selection
- [ ] Test date validation
- [ ] Test account selection
- [ ] Verify invoice ID generation is unique
- [ ] Test advance payment receipt generation

### 3. Advance Balance Tracking Tests
- [ ] Verify advance balance calculation is correct
- [ ] Test balance display in student search
- [ ] Test balance updates after advance payment creation
- [ ] Test balance updates after advance usage

### 4. Fee Collection with Advance Payment Tests
- [ ] Test automatic advance application when paying fees
- [ ] Test partial advance usage (advance < fee amount)
- [ ] Test full advance usage (advance >= fee amount)
- [ ] Test multiple advance payments usage (FIFO order)
- [ ] Test fee payment when no advance balance exists
- [ ] Verify receipt shows advance usage details

### 5. Receipt Generation Tests
- [ ] Test advance payment receipt generation
- [ ] Test fee receipt with advance usage display
- [ ] Verify receipt formatting and styling
- [ ] Test print functionality for both receipt types

### 6. User Interface Tests
- [ ] Test advance payment search interface
- [ ] Test student search and selection
- [ ] Test advance payment modal functionality
- [ ] Test form submission and error handling
- [ ] Test responsive design on different screen sizes

### 7. Integration Tests
- [ ] Test existing fee collection workflow remains intact
- [ ] Test account transaction recording
- [ ] Test SMS/email notifications (if applicable)
- [ ] Test with different user roles and permissions

### 8. Edge Cases and Error Handling
- [ ] Test with zero advance balance
- [ ] Test with insufficient advance balance
- [ ] Test with deleted/inactive advance payments
- [ ] Test database transaction rollback on errors
- [ ] Test concurrent advance payment usage

### 9. Performance Tests
- [ ] Test with large number of advance payments
- [ ] Test search performance with many students
- [ ] Test balance calculation performance
- [ ] Test receipt generation speed

### 10. Security Tests
- [ ] Test CSRF protection on forms
- [ ] Test input sanitization
- [ ] Test access control and permissions
- [ ] Test SQL injection prevention

## Test Data Setup

### Sample Students
Create test students in different classes and sections:
- Student A: Class 1, Section A
- Student B: Class 2, Section B  
- Student C: Class 3, Section A

### Sample Advance Payments
- Small advance payment (₹500)
- Medium advance payment (₹2000)
- Large advance payment (₹5000)

### Sample Fee Structures
- Monthly fee: ₹1000
- Quarterly fee: ₹3000
- Annual fee: ₹10000

## Expected Results

### Advance Payment Creation
- Advance payment record created in `student_advance_payments`
- Balance equals amount for new payments
- Unique invoice ID generated
- Account transaction recorded
- Receipt generated successfully

### Fee Collection with Advance
- Advance balance automatically applied
- Remaining amount processed as cash payment
- Usage recorded in `advance_payment_usage`
- Advance payment balance updated
- Receipt shows breakdown of payment sources

### Balance Tracking
- Current balance calculated correctly
- Balance decreases after usage
- Zero balance when fully utilized
- Multiple advance payments handled correctly

## Rollback Plan
If issues are found during testing:

1. **Database Rollback**
   ```sql
   DROP VIEW IF EXISTS v_student_advance_balance;
   DROP TABLE IF EXISTS advance_payment_usage;
   DROP TABLE IF EXISTS student_advance_payments;
   ```

2. **Code Rollback**
   - Remove advance payment model
   - Remove controller methods
   - Remove view files
   - Revert fee collection logic changes

## Success Criteria
- [ ] All test scenarios pass
- [ ] No existing functionality is broken
- [ ] Performance is acceptable
- [ ] User interface is intuitive
- [ ] Data integrity is maintained
- [ ] Security requirements are met

## Post-Deployment Monitoring
- Monitor database performance
- Track advance payment usage patterns
- Monitor error logs for issues
- Collect user feedback
- Monitor system resource usage

## Documentation Updates Required
- [ ] Update user manual with advance payment procedures
- [ ] Update admin guide with new features
- [ ] Update API documentation if applicable
- [ ] Create training materials for staff

## Notes
- Test in a staging environment before production deployment
- Backup production database before deployment
- Plan for gradual rollout if possible
- Have support team ready for user questions
