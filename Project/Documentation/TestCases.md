#Test cases for 1dv408 project#

##Test case 1.1 - Show login form##
When user wants to login a login form should be shown

###Input###
* User navigates to page
* User presses 'Login'-link

###Output###
* A login form is shown
* A button for 'Go back' is shown

##Test case 1.2 - Login##
* Test cases for login can be found [here for regular login](https://github.com/dntoll/1DV608/blob/master/Assignments/Assignment_2/Assignment2_Test_Cases_Mandatory.md) and [here for login with cookies](https://github.com/dntoll/1DV608/blob/master/Assignments/Assignment_2/Assignment2_Extra_Test_cases.md)

The tests are the same, but the output can vary from my result. Some messages are not the same or the site that you get redirected is not the same, but functionality should be the same.

##Test case 1.3 - Register##
* Test cases for registration can be found [here](https://github.com/dntoll/1DV608/blob/master/Assignments/Assignment_4/TestCases.md)

The tests are the same, but the output can vary from my result. Some messages are not the same or the site that you get redirected is not the same, but functionality should be the same.

##Test case 1.4 - Product pagination##
###Input###
* User is logged in and sees 'All products' page with four first products
* User pressed '2' on the pagination links

###Output###
* The next four products in webbshop is shown

##Test case 1.5 - View product##
##Input###
* User presses 'View product' on the product he/she wants to see

##Output##
* Information about that specific product is shown with image, name, quantity and description
* Button for adding product to basket is shown

##Alternate Scenarios###
If user is not logged in, no button is shown for adding product to basket

##Test case 1.6 - Add product to basket##
##Pre condition###
* User must be logged in

##Input###
* User presses 'Add to basket' button

###Output###
* Message "This product has been added to your basket." is shown

###Alternate Scenarios###
1.1 The quantity of the product is 0.
* 1.a No button is shown
* 1.b 'Not in stock' text is shown

##Test case 1.7 - View basket##
###Pre condition###
* User must be logged in

###Input###
* User presses the shoppingcart icon up on the right

###Output###
* All the products that the user has saved in basket is shown
* Only one of each products is shown in table, if user added two of one product that shows under the 'quantity' title
* Total price for all products combined is shown
* A button with text 'Checkout' is shown

####Alternate Scenarios###
1. If user has no products in basket the text 'You have no products in your basket yet!' is shown
* 1.1 No button for 'Checkout' is shown

##Test case 1.8 - Remove one product from basket##
###Pre condition###
* User has one item in basket

####Input####
* User presses the 'x' in the table next to the product he/she wants to remove

###Output###
* Text 'You have no products in your basket yet!' is shown

###Alternate Scenarios###
1.1 User has two of one kind of product in basket - quantity 2 is shown
* 1.a After pressing 'x' quantity 1 is shown
* 1.b Message 'Removed one item from basket.' is shown

##Test case 1.9 - Remove all products of one kind from basket##
###Pre condition###
* User has more than one typ of item in basket

####Input####
* User presses the 'Remove all' text in the table next to the product he/she wants to remove

###Output###
* Message 'Removed all items of this kind in basket.' is shown
* Product that user pressed link on is removed from basket and only the othr product is shown

###Alternate Scenarios###
1.1 User has only one kind of product in basket - quantity 2 is shown
* 1.a After pressing 'Remove all' all products of that kind is removed from basket
* 1.b Text 'You have no products in your basket yet!' is shown

##Test case 1.8 - View checkout##
###Pre condition###
* User has atleast one item in basket

###Input###
* User presses 'Checkout' button

###Output###
* Table with all products in basket is shown
* Form for customer information is shown
* Information about payment and order is shown

##Test case 2.1 - Order products##

###Input###
* User fills in ssn, firstname, lastname and email correctly
* User presses 'Confirm order' button

###Output###
* 'All products' page is shown
* Message 'Thank you for your order!' is shown
* Link to 'View receipt' is shown

* New customer is created in database
* New order is created in database
* New orderitems are created in database
* Basket items are removed

###Alternate Scenarios###
1.1 User has more products of one kind in basket than exists in database
* 1.a User presses 'Confirm order' button
* 1.b 'view checkout' page is shown again
* 1.c Message 'Something went wrong! Make sure the quantity of your ordered objects exists.' is shown
 
1.2 User fills in wrong format in social security number field
* 1.a User presses 'Confirm order' button
* 1.b 'view checkout' page is shown again
* 1.c Message 'Social security number must be in correct format (xxxxxxxx-xxxx)' is shown

1.3 User leaves firstname field empty
* 1.a User presses 'Confirm order' button
* 1.b 'view checkout' page is shown again
* 1.c Message 'Firstname must be atleast 3 characters long and only contain valid characters.' is shown

1.4 User leaves lastname field empty
* 1.a User presses 'Confirm order' button
* 1.b 'view checkout' page is shown again
* 1.c Message 'Lastname must be atleast 3 characters long and only contain valid characters.' is shown

1.5 User fills in wrong format in email field
* 1.a User presses 'Confirm order' button
* 1.b 'view checkout' page is shown again
* 1.c Message 'Email must be a valid email address' is shown

##Test case 2.2 - View receipt##
###Pre condition###
* User has successfully created a new order

###Input###
* User presses 'View receipt' link

###Output###
* Information about order is shown with customer information, orderid and orderitems.
* Information about payment is shown
* 'You should also have received a confirmation on your email about your order.' is shown (this is not impleented yet)
* Total price is shown
