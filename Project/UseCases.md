# Uce Cases

####UC1 Register new user [find it here](https://github.com/dntoll/1DV608/blob/master/Assignments/Assignment_4/UC4.md)
---------------
####UC2 Login user [find it here](https://github.com/dntoll/1DV608/blob/master/Assignments/Assignment_2/Assignment2_Use_Cases.md)
---------------
####UC3 Logout user [find it here](https://github.com/dntoll/1DV608/blob/master/Assignments/Assignment_2/Assignment2_Use_Cases.md)
---------------

####UC4 View product
**Main scenario**

* Starts when a user wants has logged in
* System shows all products that exists

---------------

####UC5 Add product på basket
**Main scenario**

* Starts when a user wants to add a product to basket
* User navigates to ViewProducts page
* User presses button to save product in basket
* System retrieves the product and display success message to user

---------------

####UC6 View basket
**Main scenario**

* Starts when a user wants to view his/hers basket
* User presses the basket icon
* Products in basket is shown in a table

**Alternate Scenarios**

3a. No items are placed in the basket
1. System presents a message to the user that the basket is empty
 
---------------

####UC7 Make order on products in basket
**Main scenario**

1. Starts when a user wants to buy the products in basket
2. User presses button to checkout
3. A table with items in basket is shown
4. A form is presented to create customer
5. User provides ssn, firstname, lastname and email
6. System saves the customer, order and orderitems and presents a success message

**Alternate Scenarios**

6a. The quantity of product is larger than exists in system and order could not be saved

1. System presents an error message
2. Step 2 in main scenario.


6b. Customer could not be saved (wrong information in form)

1. System presents an error message
2. Step 2 in main scenario.

---------------

####UC 8 User and Admin recieve payment information
