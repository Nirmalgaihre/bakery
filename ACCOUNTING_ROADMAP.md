# 📊 Accounting Module (MVP) - Feature Roadmap

**Version:** 1.0 MVP  
**Status:** Planning Phase  
**Last Updated:** 2026-08-04

---

## 📋 Table of Contents
1. [Masters](#masters)
2. [Voucher Management](#voucher-management)
3. [Accounting Engine](#accounting-engine)
4. [Automatic Voucher Creation](#automatic-voucher-creation)
5. [Bill-wise Management](#bill-wise-management)
6. [Financial Reports](#financial-reports)
7. [Outstanding Reports](#outstanding-reports)
8. [Expense Management](#expense-management)
9. [Dashboard](#dashboard)
10. [Core Database Tables](#core-database-tables)
11. [MVP Business Rules](#mvp-business-rules)
12. [Excluded from MVP](#excluded-from-mvp)

---

## 🎯 Masters

### Ledger Groups
- [ ] Create ledger group master
- [ ] Define hierarchical structure for ledger classifications
- [ ] Support primary ledger groups (Assets, Liabilities, Equity, Income, Expenses)
- [ ] Enable nesting of sub-groups
- [ ] Ledger group management UI

### Ledger Master
- [ ] Create/Edit/Delete ledger accounts
- [ ] Associate ledgers with ledger groups
- [ ] Set ledger opening balance
- [ ] Configure ledger details (name, code, description)
- [ ] Ledger balance tracking
- [ ] Ledger listing and search functionality

### Voucher Types (Fixed)
- [ ] Journal Voucher (JV)
- [ ] Payment Voucher (PV)
- [ ] Receipt Voucher (RV)
- [ ] Contra Voucher (CV)
- [ ] Debit Note (DN)
- [ ] Credit Note (CN)
- [ ] Opening Balance Voucher (OB)

### Accounting Settings
- [ ] Configure fiscal year settings
- [ ] Set default accounting period
- [ ] Enable/disable double-entry accounting
- [ ] Configure voucher numbering prefix
- [ ] Set default bank and cash ledgers
- [ ] Manage accounting parameters

---

## 💼 Voucher Management

### Journal Voucher (JV)
- [ ] Create journal voucher for inter-account transfers
- [ ] Multiple debit/credit entries
- [ ] Validate debit = credit
- [ ] Attach supporting documents
- [ ] Journal voucher listing and search
- [ ] Edit/Cancel functionality with audit trail

### Payment Voucher (PV)
- [ ] Create payment voucher for supplier/vendor payments
- [ ] Link to bills and invoices
- [ ] Track payment method (Cash, Check, Transfer, etc.)
- [ ] Record payment date and amount
- [ ] Partial payment support
- [ ] Payment reconciliation

### Receipt Voucher (RV)
- [ ] Create receipt voucher for customer payments
- [ ] Link to sales invoices
- [ ] Record receipt method and date
- [ ] Support for partial receipts
- [ ] Outstanding tracking
- [ ] Receipt reconciliation

### Contra Voucher (CV)
- [ ] Create voucher for bank-to-bank transfers
- [ ] Manage inter-bank transactions
- [ ] Track transfer dates and amounts
- [ ] Reconciliation support

### Debit Note
- [ ] Issue debit notes for purchase returns
- [ ] Link to original purchase invoice
- [ ] Track returned quantity and amount
- [ ] Auto-reverse corresponding ledger entries
- [ ] Debit note numbering and tracking

### Credit Note
- [ ] Issue credit notes for sales returns
- [ ] Link to original sales invoice
- [ ] Track returned quantity and amount
- [ ] Auto-reverse corresponding ledger entries
- [ ] Credit note numbering and tracking

### Opening Balance Voucher
- [ ] Create opening balance entries
- [ ] Set initial balances for all ledgers
- [ ] Validate total debits = credits
- [ ] Single creation per financial year
- [ ] Edit capability before closure

### Voucher Register
- [ ] View all vouchers across types
- [ ] Filter by voucher type, date range, status
- [ ] Search by voucher number or description
- [ ] Bulk actions (print, export)
- [ ] Voucher status tracking (Draft, Posted, Cancelled)

---

## ⚙️ Accounting Engine

### Double Entry Accounting
- [ ] Enforce double-entry principle
- [ ] Ensure every transaction has equal debits and credits
- [ ] Automatic ledger posting from voucher entries
- [ ] Support for multi-part transactions

### Automatic Voucher Numbering
- [ ] Generate unique voucher numbers by type
- [ ] Configurable number format and prefix
- [ ] Support for manual override (with validation)
- [ ] Reset numbering by fiscal year
- [ ] Prevent duplicate voucher numbers

### Automatic Ledger Posting
- [ ] Auto-post voucher entries to respective ledgers
- [ ] Immediate posting on voucher approval
- [ ] Batch posting capability
- [ ] Transaction history tracking
- [ ] Reversal capability for cancelled vouchers

### Debit = Credit Validation
- [ ] Real-time validation during voucher entry
- [ ] Prevent saving of unbalanced vouchers
- [ ] Clear error messaging
- [ ] Validation rules configuration

### Database Transactions
- [ ] Implement ACID compliance
- [ ] Rollback on validation failures
- [ ] Atomic posting operations
- [ ] Data consistency across tables
- [ ] Transaction logging for audit

### Voucher Cancellation
- [ ] Cancel posted vouchers with reason
- [ ] Auto-reverse ledger entries
- [ ] Create cancellation audit trail
- [ ] Prevent cancellation of settled vouchers
- [ ] Restore cancelled voucher capability

### Auto Posting
- [ ] Background job for automatic posting
- [ ] Scheduled posting for bulk vouchers
- [ ] Manual trigger option
- [ ] Posting status tracking
- [ ] Error handling and logging

---

## 🔄 Automatic Voucher Creation

### From Purchase
- [ ] Auto-create journal voucher from purchase order
- [ ] Link to supplier ledger
- [ ] Populate debit (Purchase Account) and credit (Supplier Payable)
- [ ] Support for partial invoice posting
- [ ] Tax component handling

### From Sales
- [ ] Auto-create journal voucher from sales order
- [ ] Link to customer ledger
- [ ] Populate debit (Customer Receivable) and credit (Sales Account)
- [ ] Support for partial invoice posting
- [ ] Tax component handling

### From Supplier Payment
- [ ] Auto-create payment voucher
- [ ] Link to supplier bill allocations
- [ ] Record payment to supplier ledger
- [ ] Update bill payment status
- [ ] Track payment date and method

### From Customer Receipt
- [ ] Auto-create receipt voucher
- [ ] Link to customer bill allocations
- [ ] Record receipt from customer ledger
- [ ] Update invoice receipt status
- [ ] Track receipt date and method

### From Purchase Return
- [ ] Auto-create credit note voucher
- [ ] Link to original purchase invoice
- [ ] Reverse purchase and payable entries
- [ ] Update purchase return tracking
- [ ] Supplier credit management

### From Sales Return
- [ ] Auto-create debit note voucher
- [ ] Link to original sales invoice
- [ ] Reverse sales and receivable entries
- [ ] Update sales return tracking
- [ ] Customer debit management

### From Expense
- [ ] Auto-create journal voucher for expenses
- [ ] Link to expense category
- [ ] Post to expense ledger and payment method
- [ ] Support for tax and non-tax components
- [ ] Expense tracking and categorization

### From Opening Balance
- [ ] Auto-create opening balance voucher
- [ ] Set initial ledger balances
- [ ] Validate total opening balance = 0 (if applicable)
- [ ] Fiscal year association
- [ ] Single creation constraint

---

## 📦 Bill-wise Management

### Supplier Bill Allocation
- [ ] Track individual supplier bills/invoices
- [ ] Allocate payments against specific bills
- [ ] Support partial bill payment
- [ ] Calculate outstanding amount per bill
- [ ] Allocation adjustment and reversal
- [ ] Bill status tracking (Pending, Partially Paid, Paid)

### Customer Bill Allocation
- [ ] Track individual customer invoices
- [ ] Allocate receipts against specific invoices
- [ ] Support partial invoice payment
- [ ] Calculate outstanding amount per invoice
- [ ] Allocation adjustment and reversal
- [ ] Invoice status tracking (Pending, Partially Paid, Paid)

### Outstanding Invoice Tracking
- [ ] Dashboard view of all outstanding invoices
- [ ] Filter by customer, date range, amount
- [ ] Sort by due date, amount, age
- [ ] Days outstanding calculation
- [ ] Priority highlighting (overdue)

### Partial Payments
- [ ] Support multiple partial payments per bill/invoice
- [ ] Track payment allocation history
- [ ] Calculate remaining balance
- [ ] Adjust allocation on cancellation
- [ ] Payment date and method tracking

### Invoice Settlement
- [ ] Mark invoices as settled
- [ ] Automatic settlement on full payment
- [ ] Manual settlement option
- [ ] Settlement reversal capability
- [ ] Settlement history tracking

---

## 📈 Financial Reports

### Ledger Statement
- [ ] View opening balance, transactions, and closing balance
- [ ] Filter by date range
- [ ] Detail and summary views
- [ ] Drill-down to individual transactions
- [ ] Export to CSV/PDF
- [ ] Comparative analysis (current vs. previous period)

### Cash Book
- [ ] View all cash ledger transactions
- [ ] Opening and closing cash balance
- [ ] Date-wise transaction listing
- [ ] Daily cash summary
- [ ] Reconciliation with actual cash
- [ ] Export capability

### Bank Book
- [ ] View all bank ledger transactions
- [ ] Opening and closing bank balance
- [ ] Date-wise transaction listing
- [ ] Daily bank summary
- [ ] Bank reconciliation support
- [ ] Check clearance tracking
- [ ] Export capability

### Trial Balance
- [ ] Debit and credit balance summary of all ledgers
- [ ] Validate debit total = credit total
- [ ] Filter by date range
- [ ] Comparative view (current vs. previous period)
- [ ] Identify unbalanced accounts
- [ ] Export to CSV/PDF

### Profit & Loss (P&L)
- [ ] Revenue and expense summary
- [ ] Gross profit calculation
- [ ] Operating profit calculation
- [ ] Net profit/loss
- [ ] Period-wise comparison
- [ ] Percentage of sales analysis
- [ ] Departmental P&L (if applicable)
- [ ] Export capability

### Balance Sheet
- [ ] Assets, liabilities, and equity summary
- [ ] Hierarchical ledger grouping
- [ ] Validate assets = liabilities + equity
- [ ] Period comparison
- [ ] Ratio analysis
- [ ] Notes to financial statements
- [ ] Export capability

### Voucher Register
- [ ] Summary of all voucher types and counts
- [ ] Daily voucher register
- [ ] Voucher-wise totals
- [ ] Approval status tracking
- [ ] Export capability

### Day Book
- [ ] Chronological listing of all transactions
- [ ] Daily transaction summary
- [ ] Date-wise grouping
- [ ] Running balance calculation
- [ ] Filter by voucher type
- [ ] Export capability

---

## 📊 Outstanding Reports

### Supplier Outstanding
- [ ] List of all outstanding supplier invoices
- [ ] Amount outstanding for each supplier
- [ ] Date-wise breakdown
- [ ] Aging analysis (current, 30 days, 60 days, 90+ days)
- [ ] Total outstanding amount
- [ ] Export to CSV/PDF
- [ ] Drill-down to bill details

### Customer Outstanding
- [ ] List of all outstanding customer invoices
- [ ] Amount outstanding for each customer
- [ ] Date-wise breakdown
- [ ] Aging analysis (current, 30 days, 60 days, 90+ days)
- [ ] Total outstanding amount
- [ ] Export to CSV/PDF
- [ ] Drill-down to invoice details

### Ageing Report (Optional for MVP)
- [ ] Detailed aging analysis
- [ ] Bucket-wise breakdown (0-30, 31-60, 61-90, 90+)
- [ ] Customer/Supplier wise aging
- [ ] Days outstanding calculation
- [ ] Follow-up priority identification
- [ ] Export capability

---

## 💰 Expense Management

### Expense Categories
- [ ] Create/Edit/Delete expense categories
- [ ] Hierarchical category structure
- [ ] Link to expense ledger accounts
- [ ] Tax applicability configuration
- [ ] Category-wise budget allocation (optional)
- [ ] Inactive category management

### Expense Voucher
- [ ] Create expense vouchers
- [ ] Link to expense category
- [ ] Record expense amount and date
- [ ] Attach supporting documents/receipts
- [ ] Approval workflow
- [ ] Reimbursement tracking
- [ ] Edit/Cancel functionality

### Expense Ledger
- [ ] View expense category-wise transactions
- [ ] Running total by category
- [ ] Period-wise expense summary
- [ ] Expense vs. budget analysis
- [ ] Top expense categories
- [ ] Drill-down to individual expenses

---

## 📱 Dashboard

### Cash Balance
- [ ] Real-time cash balance display
- [ ] Opening and closing balance
- [ ] Today's cash in/out
- [ ] Weekly/Monthly trend
- [ ] Graphical representation

### Bank Balance
- [ ] Real-time bank balance display per bank account
- [ ] Opening and closing balance
- [ ] Today's deposits and withdrawals
- [ ] Weekly/Monthly trend
- [ ] Multi-bank summary
- [ ] Graphical representation

### Total Receivable
- [ ] Outstanding amount from all customers
- [ ] Receivable by customer (top 10)
- [ ] Aging breakdown
- [ ] Days outstanding average
- [ ] Overdue alert highlighting

### Total Payable
- [ ] Outstanding amount to all suppliers
- [ ] Payable by supplier (top 10)
- [ ] Aging breakdown
- [ ] Days to pay average
- [ ] Due date tracking

### Today's Receipts
- [ ] Total receipts received today
- [ ] Receipt-wise breakdown
- [ ] Customer details
- [ ] Payment method summary

### Today's Payments
- [ ] Total payments made today
- [ ] Payment-wise breakdown
- [ ] Supplier details
- [ ] Payment method summary

### Monthly Income
- [ ] Total income for current month
- [ ] Income trend (bar/line chart)
- [ ] Income by category
- [ ] Comparison with previous month
- [ ] Graphical representation

### Monthly Expenses
- [ ] Total expenses for current month
- [ ] Expense trend (bar/line chart)
- [ ] Expense by category
- [ ] Comparison with previous month
- [ ] Graphical representation

---

## 🗄️ Core Database Tables

### ledger_groups
```sql
- id (Primary Key)
- name (String)
- code (String, Unique)
- parent_id (Foreign Key - Self Reference)
- description (Text)
- status (Enum: Active, Inactive)
- created_by (Foreign Key - Users)
- updated_by (Foreign Key - Users)
- created_at (Timestamp)
- updated_at (Timestamp)
- deleted_at (Soft Delete)
```

### ledgers
```sql
- id (Primary Key)
- name (String)
- code (String, Unique)
- ledger_group_id (Foreign Key - ledger_groups)
- opening_balance (Decimal)
- balance_type (Enum: Debit, Credit)
- description (Text)
- status (Enum: Active, Inactive)
- created_by (Foreign Key - Users)
- updated_by (Foreign Key - Users)
- created_at (Timestamp)
- updated_at (Timestamp)
- deleted_at (Soft Delete)
```

### voucher_types
```sql
- id (Primary Key)
- name (String) - JV, PV, RV, CV, DN, CN, OB
- code (String, Unique)
- description (Text)
- status (Enum: Active, Inactive)
- created_at (Timestamp)
- updated_at (Timestamp)
```

### vouchers
```sql
- id (Primary Key)
- voucher_number (String, Unique)
- voucher_type_id (Foreign Key - voucher_types)
- reference_number (String, Nullable)
- voucher_date (Date)
- description (Text)
- total_debit (Decimal)
- total_credit (Decimal)
- status (Enum: Draft, Posted, Cancelled)
- posted_date (Date, Nullable)
- posted_by (Foreign Key - Users, Nullable)
- cancelled_date (Date, Nullable)
- cancelled_by (Foreign Key - Users, Nullable)
- cancellation_reason (Text, Nullable)
- created_by (Foreign Key - Users)
- updated_by (Foreign Key - Users)
- created_at (Timestamp)
- updated_at (Timestamp)
- deleted_at (Soft Delete)
```

### voucher_entries
```sql
- id (Primary Key)
- voucher_id (Foreign Key - vouchers)
- ledger_id (Foreign Key - ledgers)
- debit_amount (Decimal)
- credit_amount (Decimal)
- description (Text)
- reference_document (String, Nullable)
- line_number (Integer)
- created_by (Foreign Key - Users)
- updated_by (Foreign Key - Users)
- created_at (Timestamp)
- updated_at (Timestamp)
```

### bill_allocations
```sql
- id (Primary Key)
- bill_type (Enum: Purchase Invoice, Sales Invoice)
- bill_id (Integer)
- payment_voucher_id (Foreign Key - vouchers, Nullable)
- allocated_amount (Decimal)
- allocated_date (Date)
- adjustment_amount (Decimal, Default: 0)
- status (Enum: Pending, Partially Paid, Paid)
- created_by (Foreign Key - Users)
- updated_by (Foreign Key - Users)
- created_at (Timestamp)
- updated_at (Timestamp)
```

### expenses
```sql
- id (Primary Key)
- expense_category_id (Foreign Key - expense_categories)
- voucher_id (Foreign Key - vouchers)
- amount (Decimal)
- expense_date (Date)
- description (Text)
- attachment_path (String, Nullable)
- status (Enum: Draft, Approved, Reimbursed)
- created_by (Foreign Key - Users)
- approved_by (Foreign Key - Users, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

### expense_categories
```sql
- id (Primary Key)
- name (String)
- code (String, Unique)
- parent_id (Foreign Key - Self Reference, Nullable)
- ledger_id (Foreign Key - ledgers)
- tax_applicable (Boolean)
- description (Text)
- status (Enum: Active, Inactive)
- created_by (Foreign Key - Users)
- updated_by (Foreign Key - Users)
- created_at (Timestamp)
- updated_at (Timestamp)
```

### accounting_settings
```sql
- id (Primary Key)
- key (String, Unique)
- value (Text)
- description (Text)
- data_type (Enum: String, Integer, Boolean, JSON)
- created_at (Timestamp)
- updated_at (Timestamp)
```

---

## 📋 MVP Business Rules

### Double Entry Accounting
- Every transaction must have equal debit and credit amounts
- No transaction can be posted with unbalanced entries
- Automatic validation before posting

### Debit must equal Credit
- Sum of all debits in a voucher must equal sum of all credits
- Real-time validation during voucher entry
- Prevent saving of unbalanced vouchers

### Auto Voucher Number
- Generate unique voucher numbers automatically
- Format: `{VoucherType}-{Year}-{Sequence}`
- Example: `JV-2026-0001`, `PV-2026-0042`
- Reset sequence by fiscal year
- No manual override allowed (initial release)

### Auto Ledger Posting
- Automatically post voucher entries to ledgers
- Immediate posting on voucher approval
- Create audit trail for all postings
- Support batch posting for multiple vouchers

### Automatic Purchase & Sales Posting
- Auto-create vouchers from purchase and sales orders
- Link to respective ledger accounts
- Ensure ledger balance reflects all posted transactions
- Support partial posting

### Bill-wise Payment Allocation
- Track payments against specific bills/invoices
- Support partial allocations
- Calculate outstanding per bill
- Prevent double allocation of amounts

### Soft Deletes
- Never physically delete master data
- Mark records as deleted with timestamp
- Maintain historical data integrity
- Enable data recovery if needed

### Database Transactions
- Use database transactions for all posting operations
- Rollback on any validation failure
- Ensure data consistency across tables
- Atomic operations for critical processes

### Role-based Authorization
- Access control based on user roles
- Separate permissions for view, create, edit, approve
- Audit trail for all modifications
- Restrict sensitive operations (cancellation, adjustment)

### Financial Reports Generated from Voucher Entries Only
- All reports derived from voucher_entries table
- No separate calculation tables
- Real-time report generation
- Historical data integrity

---

## 🚫 Excluded from MVP

The following features are planned for future releases:

- **Multi Company** - Support for multiple companies in one instance
- **Multi Currency** - Support for foreign currencies
- **Cost Centers** - Cost center allocation for expenses
- **Budgeting** - Budget planning and variance analysis
- **Payroll** - Employee payroll processing
- **Manufacturing** - Manufacturing cost accounting
- **Bank Reconciliation** - Automated bank statement reconciliation
- **Fixed Assets** - Asset depreciation and tracking
- **GST/VAT Automation** - Tax automation and compliance
- **Audit Trail** - Detailed transaction audit logs
- **Approval Workflow** - Multi-level approval process
- **Financial Year Closing** - Year-end closing procedures
- **Branch Accounting** - Multiple branch support
- **Recurring Vouchers** - Automated recurring transaction posting
- **Foreign Currency** - Multiple currency transactions
- **Cheque Printing** - Cheque printing and management
- **E-Invoicing** - Digital invoice compliance

---

## 🚀 Implementation Priority

### Phase 1 (Foundation)
1. Database design and setup
2. Ledger Groups and Masters
3. Voucher Types configuration
4. Accounting Settings

### Phase 2 (Core Transactions)
1. Voucher CRUD operations
2. Double-entry validation
3. Automatic posting engine
4. Cancellation logic

### Phase 3 (Business Processes)
1. Automatic voucher creation
2. Bill-wise allocation
3. Payment tracking
4. Receipt tracking

### Phase 4 (Reporting)
1. Basic financial reports
2. Day Book and Registers
3. Outstanding tracking
4. Dashboard

### Phase 5 (Polish & Optimization)
1. Performance optimization
2. Bulk operations
3. Export functionality
4. UI/UX refinement

---

## 📞 Support & Contribution

For questions or contributions to this roadmap, please reach out to the development team.

**Last Updated:** August 4, 2026  
**Maintained By:** Nirmal Gaihre
