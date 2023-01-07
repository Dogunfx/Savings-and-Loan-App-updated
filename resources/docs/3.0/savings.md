# Savings

- [Savings](#savings)
    - [Charges](#view-charges)
    - [Savings Products](#view-savings-products)
    - [Add Savings Product](#add-savings-product)
    - [View Savings](#view-savings)
    - [Add Savings](#add-savings)
    - [Savings Details](#savings-details)
      - [Account Details](#savings-account-details)
      - [Transactions](#savings-transactions)
      - [Charges](#savings-charges)


<a name="savings"></a>
## Savings

<a name="view-charges"></a>
## Charges   

Charges may be associated with a product. When an account is opened based on a product that charges have been associated with, the account will include (inherit) all charges that are associated with the product. In addition, charges that are not defined for a product may be associated with individual client accounts. 

<a name="add-charge"></a>
### Add Charge

To add new charge , click on Savings->Manage Charges->Add Charge.
This will open the charge create page offering a series of text  inputs.
The form contains:

- **Name:** The name of the charge
- **Charge Type:** This specifies when the charge should be applied. The following charge types are supported:

  **Savings Activation:** Gets charged at the time of savings activation. 
  
  **Specified due date:** Gets charged on the provided date. This charge can manually be added later on the savings
   
  **Withdrawal Fee:** Gets charged on every savings withdrawal. 
  
  **Monthly Fee:** Gets charged at the end of the month.
   
  **Inactivity Fee:** Gets charged when savings account has been inactive for a period.  
  
  **Quarterly Fee:** Gets charged every quarter of a year.  
  
- **Amount:** The charge amount
- **Charge Option:** This is how the total amount of the charge will be calculated
- **Currency:** Select the currency to be used when the charge is applied. The product and charge currency must be the same to be valid. Set up multiple charges if the same charge is required for products set up in different currencies.
- **Override:** If checked, savings administrators can change the charge amount when creating a savings account
- **Active:** If checked, this charge is available to associate with products and accounts. If unchecked, this charge is not available to associate with products and accounts.

<a name="view-savings-products"></a>
## Savings Products

Savings products define the rules, default settings, and constraints for a financial institution's lending offerings. A savings product provides a template for multiple savings accounts for the financial institution's clients.

Navigate to Savings->Manage Products to open the Savings Products list page. On this page you will see a table with columns below:

- **Name:** The name of the product
- **Short Name:** The short name to be used when the name cannot fit
- **Active:** Whether the product is active or not. If not active then it will not be available for selection
- **Action:** Further actions that you can take on the row like edit or details

<a name="add-savings-product"></a>
## Add Savings Product

To add new savings product , click on Savings->Manage Products->Add Product.
This will open the product create page offering a series of text  inputs.
The form contains:

- **Name:** The name of the product
- **Short Name:** The short name to be used when the name cannot fit
- **Description:** The description is used to provide additional information regarding the purpose and characteristics of the savings product.
- **Currency:** The currency in which the savings will be disbursed. Only charges with the same currency as the product will be available for selection
- **Decimal Places:** The number of decimal places to be used to track and report on savings.
- **Category:** Whether the product is voluntary or mandatory
- **Auto Create:** If auto create is selected, then when client is activated then savings account is also opened
- **Compounding Period:** The period at which interest rate is compounded
- **Interest Posting Period Type:** The period at which interest rate is posted or credited to a saving account
- **Interest Calculation Type:** The method used to calculate interest
- **Lockin Period Frequency:** Used to indicate the length of time that a savings account of this saving product type is locked-in and withdrawals are not allowed
- **Lockin Period Frequency Type:** The lockin period frequency type
- **Automatic Opening Balance:** The balance when an account is opened
- **Minimum Balance for interest calculation:** Sets the balance required for interest calculation
- **Allow Overdraft:** Indicates whether saving accounts based on this saving product may have an overdraft
- **Charges:** You can choose applicaple charges here. The list will only be populated when you choose currency.
- **Accounting Rule:** By default Accounting will be disabled - None:- Meaning If you are using this product for various transactions like disbursement, repayment etc. These transactions are not passed in journal entry. 

    To enable accounting, You should have created chart of accounts then select any rule which is not none. You will need to Map accounts with respect to the savings product accounts. 
- **Savings Reference:** An Asset account (typically a Cash account), to which the amount is debited when a deposit is made by the account holder and credit when the account holder makes a withdrawal
- **Overdraft Portfolio:** An Asset account (typically a Cash account), that increases when a client makes an overdraft withdrawal
- **Savings Control:** A Liability account which denotes the Savings deposit accounts portfolio and is credited when a deposit is made and debited when a withdrawal is done
- **Interest on Savings:** An Expense account, which is debited when interest is due to be paid to the customer
- **Write-off Savings:** An Expense account, which increases when a client does not repay overdraft balance
- **Income from fees:** An Income account which is credited when a fee is paid by account holder on this account
- **Income from penalties:** An Income account, which is credited when a penalty is paid by account holder on this account
- **Income from interest (Overdraft):** An Income account, where income generated when client pays interest on overdrafts
- **Active:** Whether the product is active or not. If not active then it will not be available for selection


<a name="view-savings"></a>
## View Savings

Clicking View Savings will open savings list page. On this page you will see a table with columns below:

- **System ID:** The system id of the savings
- **Branch:** The branch of the savings
- **Savings Officer:** The financial institution representative who has responsibility for, and interacts with, the client/group associated with a savings account
- **Client:** The client who owns the savings.
- **Interest Rate:** The interest rate of the product
- **Balance:** The current savings amount
- **Status:** The current savings status
- **Product:** The savings product
- **Action:** Further actions that you can take on the row like edit or details

<a name="add-savings"></a>
## Add Savings

To add new savings , click on Savings menu then click Create Savings.
This will open the savings create page offering a series of text  inputs.
The form contains:

- **Client:** The  client
- **Savings Product:** The savings product. Once this is selected, some fields will be prepopulated with values from the savings product
- **Savings Officer:** The financial institution representative who has responsibility for, and interacts with, the client/group associated with a savings account
- **Interest Rate:** The interest rate of the product
- **Automatic Opening Balance:** The balance when an account is opened
- **Lockin Period Frequency:** Used to indicate the length of time that a savings account of this saving product type is locked-in and withdrawals are not allowed
- **Lockin Period Frequency Type:** The lockin period frequency type
- **Submitted On:** The date when the account was submitted
- **Charges:** You can add additional charges here or override charge amounts.

<a name="savings-process"></a>
## Savings Process

The process of attaining a savings from an MFI begins with a savings application.

1. An MFI will have some financial credit service offering or savings products that may be suitable for a range of customers.
2. Potential customers (savings applicants) who are interested in praticular savings product will submit a savings application (most likely through the help of a savings officer)
3. This savings application will be reviewed. The savings application process can differ widely from MFI to MFI depending on their business process and the microfinance credit methodology they employ. But eventually a person or persons (credit committee) take a decision to approve the savings application.

The following process is supported:

|   Savings Application State   |   Action   |   Resultant Application Status |
|   -------  |----------|  -----------------------------|
| Pending Approval   |    **Approve** <br> **Reject** <br> **Withdraw**        | Awaiting Activation <br> Rejected <br> Withdrawn   |
| Awaiting Activation   |    **Activate** <br> **Undo Approval**        | Active <br> Pending Approval  |
| Active   |    **Undo Activation** <br> **Close Savings**        | Awaiting Activation <br> Closed  |

<a name="savings-details"></a>
## Savings Details

Clicking details will open the savings details page. You can perform various actions on the savings.


<a name="account-details"></a>
### Account Details

This tab shows quick details about the savings settings like Savings Transaction Processing Strategy.


<a name="savings-transactions"></a>
### Transactions

This tab shows all the savings transactions. 

<a name="savings-charges"></a>
### Charges

This tab shows all the savings charges. You can waive a charge. Only specified due date charge types can be added to the savings.

 
 



