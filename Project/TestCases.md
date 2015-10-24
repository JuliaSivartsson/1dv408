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
1. The quantity of the product is 0.
* 1.1 No button is shown
* 1.2 'Not in stock' text is shown

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
##Test case 1.9 - Remove all products of one kind from basket##
##Test case 1.8 - View checkout##

##Test case 2.1 - Order products##

