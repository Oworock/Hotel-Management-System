# HR Management Plugin

A comprehensive, self-contained HR management system plugin for the hotel platform.

## Features

### 1. Employee Management
- Complete employee profiles with personal and contact information
- Track employment type (full-time, part-time, contract)
- Monitor employee status (active, inactive, terminated)
- Salary and compensation tracking
- Document storage and management

### 2. Department Management
- Create and manage organizational departments
- Assign department managers
- Budget tracking per department
- View all employees in a department

### 3. Attendance Tracking
- Daily attendance marking (present, absent, leave, half-day)
- Check-in/check-out time tracking
- Automatic duration calculation
- Attendance reports by employee, department, or date range
- Paginated attendance records

### 4. Leave Management
- Employee leave requests
- Multiple leave types (Annual, Sick, Casual, Maternity, Paternity, Unpaid)
- Leave balance tracking
- Approval workflow for managers
- Pending leave dashboard

### 5. Payroll Management
- Monthly payroll processing
- Salary structure management (basic, allowances, deductions, tax)
- Automatic net salary calculation
- Payroll slip generation and printing
- Payment status tracking (draft, finalized, paid)

### 6. Performance Reviews
- Employee performance evaluations
- 5-point rating system
- Strengths and areas for improvement tracking
- Goal setting and monitoring
- Performance history

### 7. Leave Types
The plugin comes pre-seeded with 6 common leave types:
- Annual Leave (20 days/year)
- Sick Leave (10 days/year)
- Casual Leave (5 days/year)
- Maternity Leave (90 days/year)
- Paternity Leave (14 days/year)
- Unpaid Leave

## Access URLs

All HR features are accessible under `/admin/hr/` path:

- **Dashboard**: `/admin/hr/`
- **Employees**: `/admin/hr/employees`
- **Departments**: `/admin/hr/departments`
- **Attendance**: `/admin/hr/attendance`
- **Leaves**: `/admin/hr/leaves`
- **Payroll**: `/admin/hr/payroll`
- **Performance**: `/admin/hr/performance`

## Database Tables

The plugin creates the following tables:
- `employees` - Employee profiles
- `departments` - Organization structure
- `designations` - Job titles
- `attendance` - Daily attendance records
- `leaves` - Leave requests
- `leave_types` - Types of leaves available
- `payroll` - Monthly payroll records
- `performance_reviews` - Employee performance evaluations
- `hr_documents` - HR documents and files

## Models

- `Employee` - Employee profile model
- `Department` - Department model
- `Designation` - Job title/designation model
- `Attendance` - Attendance records
- `Leave` - Leave requests
- `LeaveType` - Types of leaves
- `Payroll` - Salary and payment records
- `PerformanceReview` - Performance evaluations
- `HRDocument` - HR documents

## Controllers

- `HRDashboardController` - Dashboard with statistics
- `EmployeeController` - Employee CRUD operations
- `DepartmentController` - Department management
- `AttendanceController` - Attendance tracking and marking
- `LeaveController` - Leave request and approval
- `PayrollController` - Payroll management
- `PerformanceReviewController` - Performance reviews

## Usage

### Adding an Employee
1. Navigate to `/admin/hr/employees`
2. Click "+ Add Employee"
3. Fill in employee details (code, name, department, designation, salary)
4. Click "Add Employee"

### Marking Attendance
1. Go to `/admin/hr/attendance/mark`
2. Select employee and date
3. Choose status (present, absent, leave, half-day)
4. Submit

### Requesting Leave
1. Visit `/admin/hr/leaves/request`
2. Select employee, leave type, and dates
3. Add reason (optional)
4. Submit request

### Creating Payroll
1. Navigate to `/admin/hr/payroll/create`
2. Select employee and month/year
3. Enter salary components (basic, allowances, deductions, tax)
4. Create payroll entry
5. View and print payroll slip

### Performance Reviews
1. Go to `/admin/hr/performance`
2. Create new review
3. Set rating (1-5) and add comments
4. Track strengths and areas for improvement

## Architecture

The plugin is completely self-contained:
- All code is in `plugins/HRManagement/` directory
- No modifications to core application files
- Uses Laravel's plugin system with ServiceProvider
- Automatically loaded routes, views, and migrations
- Follows PSR-4 autoloading standards (`Plugins\HRManagement` namespace)

## Permissions

The plugin uses role-based access control:
- Requires `admin` or `super_admin` role to access
- All HR features are protected by authentication middleware

## Features Highlights

✅ Complete employee lifecycle management
✅ Flexible leave management system
✅ Attendance tracking with duration calculation
✅ Payroll processing and slip generation
✅ Performance review system
✅ Department organization
✅ Printable payroll slips
✅ Paginated lists for all modules
✅ Search and filter capabilities
✅ Role-based access control
✅ Clean, responsive UI with Tailwind CSS

## Future Enhancements

- Employee onboarding/offboarding workflows
- Training and development tracking
- Employee benefits management
- Employee certifications and qualifications
- HR analytics and reporting
- Email notifications for leave requests
- API endpoints for mobile access
- Import/export employee data

---

**Version**: 1.0.0  
**Author**: Hotel Platform  
**Status**: Production Ready
