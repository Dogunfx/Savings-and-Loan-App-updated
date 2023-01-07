# Loans

- [Loans](#loans)
    - [Charges](#view-charges)
    - [Loan Products](#view-loan-products)
    - [Add Loan Product](#add-loan-product)
    - [View Loans](#view-loans)
    - [View Applications](#view-loan-applications)
    - [Add Loan](#add-loan)
    - [Loan Details](#loan-details)
      - [Account Details](#loan-account-details)
      - [Repayment Schedule](#loan-repayment-schedule)
      - [Transactions](#loan-transactions)
      - [Charges](#loan-charges)
      - [Collateral](#loan-collateral)
      - [Guarantors](#loan-guarantors)
      - [Files](#loan-files)
      - [Notes](#loan-notes)
    - [Loan Calculator](#loan-calculator)

<a name="loans"></a>
## Loans

<a name="view-charges"></a>
## Charges   

We support two types of charges—fees and penalties. Fees are charged for services—for example, membership fees, loan disbursement fees, withdrawal fees. Penalties are charged to discourage clients from deviating from the terms of a product—for example, late payment penalty for a loan account, early withdrawal penalty for a fixed deposit account. 
Charges may be associated with a product. When an account is opened based on a product that charges have been associated with, the account will include (inherit) all charges that are associated with the product. In addition, charges that are not defined for a product may be associated with individual client accounts. 

<a name="add-charge"></a>
### Add Charge

To add new charge , click on Loans->Manage Charges->Add Charge.
This will open the charge create page offering a series of text  inputs.
The form contains:

- **Name:** The name of the charge
- **Charge Type:** This specifies when the charge should be applied. The following charge types are supported:

  **Disbursement:** Gets charged at the time of loan disbursement, provide amount gets collected separately (Not from the loan amount). 
  
  **Specified due date:** Gets charged on the provided date. This charge can manually be added later on the loan
   
  **Overdue Installment Fee:** Gets charged on every loan installment. 
  
  **Overdue Fees:** Gets charged if there is an overdue, this is a penalty charge.
   
  **Disbursement-Paid with repayment:** Gets charged at the time of loan disbursement and also gets paid.  
  
  **Loan Rescheduling Fee:** Gets charged when a loan is rescheduled.  
  
- **Amount:** The charge amount
- **Charge Option:** This is how the total amount of the charge will be calculated
- **Currency:** Select the currency to be used when the charge is applied. The product and charge currency must be the same to be valid. Set up multiple charges if the same charge is required for products set up in different currencies.
- **Penalty:** If checked, this charge is marked as a penalty, rather than a fee
- **Override:** If checked, loan administrators can change the charge amount when creating a loan
- **Active:** If checked, this charge is available to associate with products and accounts. If unchecked, this charge is not available to associate with products and accounts.

<a name="view-loan-products"></a>
## Loan Products

Loan products define the rules, default settings, and constraints for a financial institution's lending offerings. A loan product provides a template for multiple loan accounts for the financial institution's clients.

Navigate to Loans->Manage Products to open the Loan Products list page. On this page you will see a table with columns below:

- **Name:** The name of the product
- **Short Name:** The short name to be used when the name cannot fit
- **Fund:** These are funds that will be used for lending
- **Active:** Whether the product is active or not. If not active then it will not be available for selection
- **Action:** Further actions that you can take on the row like edit or details

<a name="add-loan-product"></a>
## Add Loan Product

To add new loan product , click on Loans->Manage Products->Add Product.
This will open the product create page offering a series of text  inputs.
The form contains:

- **Name:** The name of the product
- **Short Name:** The short name to be used when the name cannot fit
- **Description:** The description is used to provide additional information regarding the purpose and characteristics of the loan product.
- **Fund:** Loan products may be assigned to a fund set up by your financial institution. If available, the fund field can be used for tracking and reporting on groups of loans.
- **Currency:** The currency in which the loan will be disbursed. Only charges with the same currency as the product will be available for selection
- **Decimal Places:** The number of decimal places to be used to track and report on loans.
- **Default Principal:** The principal that will be pre entered when creating a loan with this product
- **Minimum Principal:** The Minimum principal allowed when creating a loan with this product
- **Maximum Principal:** The Maximum principal allowed when creating a loan with this product
- **Default Loan Term:** The Loan Term that will be pre entered when creating a loan with this product
- **Minimum Loan Term:** The Minimum Loan Term allowed when creating a loan with this product
- **Maximum Loan Term:** The Maximum Loan Term allowed when creating a loan with this product
- **Repayment Frequency:** This field is input to calculating the repayment schedule for a loan account and is used to determine when payments are due.
- **Type:** This field is input to calculating the repayment schedule for a loan account and is used to determine when payments are due.
- **Default Interest Rate:** The Interest Rate that will be pre entered when creating a loan with this product.The minimum, default, and maximum nominal interest rates are expressed as percentages
- **Minimum Interest Rate:** The Minimum Interest Rate allowed when creating a loan with this product
- **Maximum Interest Rate:** The Maximum Interest Rate allowed when creating a loan with this product
- **Per:** The interest rate type
- **Grace On Principal Payment:** The period for which the client will only pay interest
- **Grace On Interest Payment:** The period for which the client will only pay principal
- **Grace On Interest Charged:** The period for which interest is not charged
- **Interest Methodology:** The Interest method value is input to calculating the payments amount for repayment of the loan
- **Amortization Method:** The Amortization value is input to calculating the repayment amounts for repayment of the loan.
- **Loan Transaction Processing Strategy:** The transaction processing strategy determines the sequence in which each of the components is paid.
- **Charges:** You can choose applicaple charges here. The list will only be populated when you choose currency.
- **Credit Checks:** Here you can choose any checks that should be checked when creating a loan
- **Accounting Rule:** By default Accounting will be disabled - None:- Meaning If you are using this product for various transactions like disbursement, repayment etc. These transactions are not passed in journal entry. 

    To enable accounting, You should have created chart of accounts then select any rule which is not none. You will need to Map accounts with respect to the loan product accounts. 
- **Fund Source:** an Asset account(typically Bank or cash) that is debited during repayments/payments an credited using disbursals.
- **Loan Portfolio:** an Asset account that is debited during disbursement and credited during principal repayment/writeoff.
- **Overpayments:** an Liability account that is credited on overpayments and credited when refunds are made to client.
- **Suspended Income:** an Asset account that is used a suspense account for tracking portfolios of loans under transfer.
- **Income from Interest:** an Income account that is credited during interest payment.
- **Income from penalties:** An Income account, which is credited when a penalty is paid by account holder on this account
- **Income from fees:** An Income account which is credited when a fee is paid by account holder on this account
- **Income from recovery:** an Income account that is credited during Recovery Repayment.
- **Losses Written Off:** An expense account that is debited on principal writeoff (also debited in the events of interest, fee and penalty written-off in case of accrual based accounting.)
- **Interest Written Off:** An expense account that is debited on interest writeoff 
- **Auto Disburse:** If checked, the loan will be disbursed on creation
- **Grace On Principal Payment:** The interest rate type
- **Grace On Principal Payment:** The interest rate type
- **Grace On Principal Payment:** The interest rate type
- **Active:** Whether the product is active or not. If not active then it will not be available for selection


<a name="view-loans"></a>
## View Loans

Clicking View Loans will open loans list page. On this page you will see a table with columns below:

- **System ID:** The system id of the loan
- **Branch:** The branch of the loan
- **Loan Officer:** The financial institution representative who has responsibility for, and interacts with, the client/group associated with a loan account
- **Client:** The client who owns the loan.
- **Amount:** The amount disbursed
- **Balance:** The outstanding loan amount
- **Disbursed:** The date when the loan was disbursed
- **Status:** The current loan status
- **Product:** The loan product
- **Action:** Further actions that you can take on the row like edit or details

<a name="add-loan"></a>
## Add Loan

To add new loan , click on Loans menu then click Create Loan.
This will open the loan create page offering a series of text  inputs.
The form contains:

- **Client Type:** The client type
- **Client:** The  client
- **Loan Product:** The loan product. Once this is selected, some fields will be prepopulated with values from the loan product
- **Principal:** The loan principal
- **Fund:** The loan fund used
- **Loan Term:** The loan term duration. The duration type should be the same as the repayment frequency type
- **Repayment Frequency:** This field is input to calculating the repayment schedule for a loan account and is used to determine when payments are due.
- **Type:** The repayment frequency and loan term type
- **Interest Rate (% Per Year ):** The interest rate
- **Expected Disbursement Date:** The date that the loan account is expected to be disbursed
- **Loan Officer:** The financial institution representative who has responsibility for, and interacts with, the client/group associated with a loan account
- **Loan Purpose:** Provides an indication of how the funds provided through the loan will be directed and can be used to group loans with the same purpose for reporting
- **Expected First Repayment date:** The date that the first repayment is expected.
- **Charges:** You can add additional charges here or override charge amounts.

<a name="loan-process"></a>
## Loan Process

The process of attaining a loan from an MFI begins with a loan application.

1. An MFI will have some financial credit service offering or loan products that may be suitable for a range of customers.
2. Potential customers (loan applicants) who are interested in praticular loan product will submit a loan application (most likely through the help of a loan officer)
3. This loan application will be reviewed. The loan application process can differ widely from MFI to MFI depending on their business process and the microfinance credit methodology they employ. But eventually a person or persons (credit committee) take a decision to approve the loan application.

The following process is supported:

|   Loan Application State   |   Action   |   Resultant Application Status |
|   -------  |----------|  -----------------------------|
| Pending Approval   |    **Approve** <br> **Reject** <br> **Withdraw**        | Awaiting Disbursement <br> Rejected <br> Withdrawn   |
| Awaiting Disbursement   |    **Disburse** <br> **Undo Approval**        | Active <br> Pending Approval  |
| Active   |    **Undo Disbursement** <br> **Write off Loan** <br> **Reschedule Loan**        | Awaiting Disbursement <br> Written Off <br> Rescheduled  |

<a name="loan-details"></a>
## Loan Details

Clicking details will open the loan details page. You can perform various actions on the loan.


<a name="account-details"></a>
### Account Details

This tab shows quick details about the loan settings like Loan Transaction Processing Strategy.

<a name="loan-repayment-schedule"></a>
### Repayment Schedule

This tab shows the loan repayment schedule. You can print, email and download the schedule.


<a name="loan-transactions"></a>
### Transactions

This tab shows all the loan transactions. 

<a name="loan-charges"></a>
### Charges

This tab shows all the loan charges. You can waive a charge. Only specified due date charge types can be added to the loan.

 
<a name="loan-files"></a>
### Files

This tab shows all the loan files. 

<a name="loan-collateral"></a>
### Collateral

This tab shows all the loan collateral. 

<a name="add-loan-collateral"></a>
### Add Collateral

To add new collateral , click on Collateral Tab then click Add Collateral.
This will open the collateral create page offering a series of text  inputs.
The form contains:

- **Type:** The collateral type. You can add these by clicking Settings->Orgaisation Settings->Collateral Types
- **Value:** The collateral value.
- **File:** Collateral file or picture
- **Description:**  Any additional details.

<a name="loan-guarantors"></a>
### Guarantors

This tab shows all the loan guarantors. You can add a guarantor either from an existing client or adding a new one. 
 
<a name="loan-notes"></a>
### Notes

This tab shows all the loan notes.
 



