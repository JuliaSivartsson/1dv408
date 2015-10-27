<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-02
 * Time: 18:30
 */

namespace view;

use common\CookieStorage;
use model\dal\ProductBasketDAL;
use model\dal\ProductRepository;
use model\LoginModel;
use model\ProductCatalog;
use model\CustomerCatalog;
use model\OrderCatalog;
use model\OrderItemCatalog;

class ProductView
{

    private $productCatalog;
    private $customerCatalog;
    private $orderItemCatalog;
    private $orderCatalog;

    private $navView;
    private $cookie;
    private $message;
    private $successMessage;
    private $persistentBasketDAL;
    private $expirationDate;

    private static $cookieProductId = 'LoginView::CookieProductId';
    private static $cookieProduct = 'LoginView::CookieProduct';

    private static $messageId = "MemberView::MessageId";
    private static $addProductToBasket = "ProductView::AddToBasket";
    private static $checkoutSSN = 'ProductView:CheckoutSSN';
    private static $checkoutFirstName = 'ProductView:CheckoutFirstName';
    private static $checkoutLastName = 'ProductView:CheckoutLastName';
    private static $checkoutAddress = 'ProductView:CheckoutAddress';
    private static $checkoutPostalCode = 'ProductView:CheckoutPostalCode';
    private static $checkoutCity = 'ProductView:CheckoutCity';
    private static $checkoutEmail = 'ProductView:CheckoutEmail';
    private static $checkoutButton = 'ProductView::CheckoutButton';

    private static $ProductPosition = "product";

    public function __construct(NavigationView $navView){
        $this->productCatalog = new ProductCatalog();
        $this->customerCatalog = new CustomerCatalog();
        $this->orderItemCatalog = new OrderItemCatalog();
        $this->orderCatalog = new OrderCatalog();

        $this->navView = $navView;
        $this->loginModel = new LoginModel();
        $this->loginView = new LoginView($this->loginModel);
        $this->persistentBasketDAL = new ProductBasketDAL();
        $this->cookie = new CookieStorage();
    }

    public function viewAllProducts(){

        $message = $this->message;
        $successMessage = $this->successMessage;
        if($successMessage != ""){
            $successMessageContainer = '<div class="checkoutMessage"><p class="alert alert-success" id="' . self::$messageId . '">' . $successMessage . '</p></div>';
        }
        else{
            $successMessageContainer = "";
        }

        $limit      = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 4;
        $page       = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1;
        $links      = ( isset( $_GET['links'] ) ) ? $_GET['links'] : 3;
        $paginationLinks = new \common\PaginationLinks();


        $paginationResults = $this->productCatalog->getProductsPagination($page, $limit);
        $allProducts = $this->productCatalog->getAllProducts();

        $totalRows = count($allProducts);

        $ret = '<div class="jumbotron">';
        $ret .= '<h1>Welcome</h1>';
        $ret .= $successMessageContainer;
        $ret .= '<h3>All products</h3>';
        $ret .= '<p id="' . self::$messageId . '">' . $message . '</p>';
        $ret .= '<div class="pagination-links">'. $paginationLinks->createLinks($page, $limit, $totalRows, $links, 'pagination pagination-sm').'</div>';
        $ret .= '<div class="row">';

        foreach($paginationResults->data as $product) {
            $ret .= '<div class="col-sm-6 col-md-3">';
            $ret .= '<div class="thumbnail">';
            $ret .= '<p>'. $product->getImage($product->getId()).'</p>';
            $ret .= '<div class="caption">';
            $ret .= '<h3>'. $product->getName() .'</h3>';
            $ret .= '<p>$'. $product->getPrice() .'</p>';
            $ret .= '<p>' . $product->getQuantity(). ' in store</p>';
            $ret .= '<p>' . substr($product->getDescription(),0, 100) . '</p>';
            $ret .= '<p class="btn btn-primary">' . $this->getViewLink($product) . PHP_EOL .'</p>';
            $ret .= '</div>';
            $ret .= '</div>';
            $ret .= '</div>';
        }
        $ret .= '</div>';
        $ret .= '</div>';
        return $ret;
    }

    public function viewProduct(){

        $message = $this->message;

        if($message != ""){
            $messageContainer = '<p class="alert alert-success" id="' . self::$messageId . '">' . $message . '</p>';
        }
        else{
            $messageContainer = '<p" id="' . self::$messageId . '">' . $message . '</p>';
        }

        $id = $_GET['product'];
        $productToShow = $this->productCatalog->getProductById($id);

        $ret = '<div class="jumbotron">';
        $ret .= '<div class="col-md-6">';
        $ret .= '</div>';
        $ret .= $messageContainer;
        $ret .= '<h1>'.$productToShow->getName().'</h1>';
        $ret .= '<p>'.$productToShow->getImage($productToShow->getId()).'</p>';
        $ret .= '<p>Price: $' . $productToShow->getPrice(). '</p>';
        if($productToShow->getQuantity() == 0){
            $ret .= '<p class="alert alert-danger">Not in stock.</p>';
        }
        else{
            $ret .= '<p>' . $productToShow->getQuantity(). ' in store</p>';
        }
        $ret .= '<div class="description"><p>' . $productToShow->getDescription(). '</p></div>';
        if($this->loginView->isLoggedIn() === true && $productToShow->getQuantity() != 0) {
            $ret .= '
            <form method="post">
                <input id="submit" class="btn btn-primary" type="submit" name="' . self::$addProductToBasket . '"  value="Add to basket" />
            </form>';
        }
        $ret .= '</div>';
        return $ret;
    }

    public function viewBasket(){

        $successMessage = $this->message;
        if($successMessage != ""){
            $successMessageContainer = '<div class="checkoutMessage"><p class="alert alert-success" id="' . self::$messageId . '">' . $successMessage . '</p></div>';
        }
        else{
            $successMessageContainer = "";
        }

        if($basket = $this->persistentBasketDAL->load() === false){
            $ret = '<div class="jumbotron">';
            $ret .= '<p>You have no products in your basket yet!</p>';
            $ret .= '</div>';
        }
        else {

            $pieces = $this->getProductsFromCookie();

            $allObjectsInCookie = array();
            $objectsToShow = array();
            $totalPrice = 0;

            foreach ($pieces as $productInBasket) {
                if ($productInBasket != "") {
                    if(!in_array($productInBasket, $objectsToShow)) {
                        array_push($objectsToShow, $productInBasket);
                    }
                    array_push($allObjectsInCookie, $productInBasket);
                }
            }

            $ret = '<div class="jumbotron">';
            $ret .= $successMessageContainer;
            $ret .= '<p>Products in your basket:</p>';
            $ret .= '<table class="table">';
            $ret .= '<tr>';
            $ret .= '<th>Product</th>';
            $ret .= '<th>Price per product</th>';
            $ret .= '<th>Quantity</th>';
            $ret .= '</tr>';
            foreach ($objectsToShow as $basketItem) {
                $getObjectFromName = $this->productCatalog->getProductByName($basketItem);

                $array_count = array_count_values($allObjectsInCookie);
                $totalPrice += $getObjectFromName->getPrice() * $array_count["$basketItem"];
                $quantity = $array_count["$basketItem"];


                $ret .= '<tr>';
                $ret .= '<td><h3>'. $getObjectFromName->getImage($getObjectFromName->getId(), 50) .' '.  $this->getViewLinkFromBasket($getObjectFromName) . PHP_EOL .'</h3>';
                $ret .= '<td>$'. $getObjectFromName->getPrice() .'</td>';
                $ret .= '<td>'. $quantity .'</td>';
                $ret .= '<td>' . $this->removeOneItemFromBasketLink($getObjectFromName) . PHP_EOL .'</td>';
                $ret .= '<td>' . $this->removeItemFromBasketLink($getObjectFromName) . PHP_EOL .'</td>';
                $ret .= '</tr>';
            }
            $ret .= '</table>';
            $ret .= '<h2>Total: $'. $totalPrice .'</h2>';
            $ret .= '<div class="btn btn-primary btn-sm checkoutButton">' . $this->getViewCheckoutLink() . PHP_EOL .'</div>';
            $ret .= '</div>';
        }
        return $ret;

    }

    public function viewCheckout(){

        $message = $this->message;
        $successMessage = $this->successMessage;

        //If there are no items in the basket
        if($basket = $this->persistentBasketDAL->load() === false){
            $ret = '<div class="jumbotron">';
            $ret .= '<p>You have no products in your basket yet!</p>';
            $ret .= '</div>';
        }

        else {
            $objectsToShow = array();
            $pieces = $this->getProductsFromCookie();
            $allObjectsInCookie = array();
            $totalPrice = 0;

            //Loop through all the products in basket and place one of each in an array, and all the products (even duplicates) in another array.
            foreach ($pieces as $productInBasket) {
                if ($productInBasket != "") {
                    if(!in_array($productInBasket, $objectsToShow)) {
                        array_push($objectsToShow, $productInBasket);
                    }
                    array_push($allObjectsInCookie, $productInBasket);
                }
            }

            if($successMessage != ""){
                $successMessageContainer = '<div class="checkoutMessage"><p class="alert alert-success" id="' . self::$messageId . '">' . $successMessage . '</p></div>';
            }
            else{
                $successMessageContainer = "";
            }

            $ret = '<div class="jumbotron">';
                $ret .= $successMessageContainer;
                $ret .= '<h2>Checkout</h2>';
                $ret .= '<table class="table table-striped table-hover">';
                $ret .= '<tr>';
                $ret .= '<th>Product</th>';
                $ret .= '<th>Price per product</th>';
                $ret .= '<th>Quantity</th>';
                $ret .= '</tr>';
                foreach ($objectsToShow as $basketItem) {
                    $getObjectFromName = $this->productCatalog->getProductByName($basketItem);
                    $array_count = array_count_values($allObjectsInCookie);
                    $totalPrice += $getObjectFromName->getPrice() * $array_count["$basketItem"];
                    $quantity = $array_count["$basketItem"];

                    $ret .= '<tr>';
                    $ret .= '<td>'. $getObjectFromName->getName() .'</td>';
                    $ret .= '<td>$'. $getObjectFromName->getPrice() .'</td>';
                    $ret .= '<td>'. $quantity .'</td>';
                    $ret .= '</tr>';
                }

                if($message != ""){
                    $messageContainer = '<div class="checkoutMessage"><p class="alert alert-danger" id="' . self::$messageId . '">' . $message . '</p></div>';
                }
                else{
                    $messageContainer = "";
                }

                $ret .= '</table>';
                $ret .= '<h2>Total: $'. $totalPrice .'</h2>';

                $ret .= '<div class="row">';
                    $ret .= '<div class="checkoutForm col-md-5">';
                        $ret .= '<form method="post">';
                            $ret .= '<legend>Enter your information</legend>';
                            $ret .= $messageContainer;
                            $ret .= '<fieldset>';
                            $ret .= '<div class="form-group">';
                            $ret .= '<label for="' . self::$checkoutSSN . '">Social security number: </label>';
                            $ret .= '<input type="text"  class="form-control" id="' . self::$checkoutSSN . '" placeholder="xxxxxxxx-xxxx" name="' . self::$checkoutSSN . '" value="' . $this->getCheckoutSSN()  .'" />';
                            $ret .='</div>';
                            $ret .= '<div class="form-group">';
                            $ret .= '<label for="' . self::$checkoutFirstName . '">Firstname: </label>';
                            $ret .= '<input type="text"  class="form-control" id="' . self::$checkoutFirstName . '" name="' . self::$checkoutFirstName . '" value="' . $this->getCheckoutFirstName()  .'" />';
                            $ret .='</div>';
                            $ret .= '<div class="form-group">';
                            $ret .= '<label for="' . self::$checkoutLastName . '">Lastname: </label>';
                            $ret .= '<input type="text"  class="form-control" id="' . self::$checkoutLastName . '" name="' . self::$checkoutLastName . '" value="' . $this->getCheckoutLastName()  .'" />';
                            $ret .='</div>';
                            $ret .= '<div class="form-group">';
                            $ret .= '<label for="' . self::$checkoutEmail . '">Email: </label>';
                            $ret .= '<input type="text"  class="form-control" id="' . self::$checkoutEmail . '" name="' . self::$checkoutEmail . '" value="' . $this->getCheckoutEmail()  .'" />';
                            $ret .='</div>';
                            $ret .= '<div class="form-group">';
                            $ret .= '<input id="submit" class="btn btn-primary" type="submit" name="' . self::$checkoutButton . '"  value="Confirm order" />';
                            $ret .= '</div>';
                            $ret .= '</fieldset>';
                        $ret .= '</form>';
                    $ret .= '</div>';
                    $ret .= '<div class="col-md-5 checkoutInfo">';
                        $ret .= '<p>You are now only one step from actually ordering these products!
                                All you have to do is fill in your information on the left and confirm your order.</p>
                                <p>Do not forget to double check your basket so that you are happy with your purchase.</p>
                                <p>Here on itzys webshop we use Swish as payment method, you can read more about that <a href="https://www.getswish.se/" target="_blank">here</a>.</p>
                                <p>The telephonenumber for payment will be in your reciept that you will recieve when the order is complete.</p>

                                ';
                    $ret .= '</div>';
                $ret .= '</div>';
            $ret .= '</div>';
        }
        return $ret;
    }

    public function viewReceipt(){

        $id = $_GET['Customer'];

        //Get order by customer
        $customer = $this->customerCatalog->getCustomerById($id);
        $order = $this->orderCatalog->getLatestOrderByCustomerId($customer->getId());

        $orderItems = $this->orderItemCatalog->getAllOrderItemsWithOrderId($order->getId());

        $products = array();
        $allBoughtItems = array();
        $message = $this->message;
        $totalPrice = 0;

        foreach($orderItems as $item){
            $productName = $this->productCatalog->getProductById($item->getProductId());

            if(!in_array($productName->getName(), $products)) {
                array_push($products, $productName->getName());
            }
            array_push($allBoughtItems, $productName->getName());
        }

        $ret = '<div class="jumbotron">';
        $ret .= '<p id="' . self::$messageId . '">' . $message . '</p>';
        $ret .= '<h3>Thank you for your order!</h3>';

        $ret .= '<div class="checkoutInfo">';
        $ret .= '<p>Order id: '. $order->getId() .' (this is good to remember for further reference to us)</p>';
        $ret .= '<p>'. $customer->getFirstName() .' '. $customer->getLastName() .'</p>';
        $ret .= '<p>'. $customer->getSSN() .'</p>';
        $ret .= '<p>'. $customer->getEmail() .'</p>';
        $ret .= '</div>';
        $ret .= '<p>Payment method: Swish account 123-456 7890</p>';
        $ret .= '<table class="table table-striped table-hover">';
        $ret .= '<tr>';
        $ret .= '<th>Product</th>';
        $ret .= '<th>Price per product</th>';
        $ret .= '<th>Quantity</th>';
        $ret .= '</tr>';
        foreach ($products as $product) {
            $productObject = $this->productCatalog->getProductByName($product);
            $array_count = array_count_values($allBoughtItems);
            $name = $productObject->getName();
            $totalPrice += $productObject->getPrice() * $array_count["$name"];
            $quantity = $array_count["$name"];

            $ret .= '<tr>';
            $ret .= '<td>'. $productObject->getName() .'</td>';
            $ret .= '<td>$'. $productObject->getPrice() .'</td>';
            $ret .= '<td>'. $quantity .'</td>';
            $ret .= '</tr>';
        }
        $ret .= '</table>';
        $ret .= '<h2>Total: $'. $totalPrice .'</h2>';
        $ret .= '</div>';
        return $ret;
    }

    public function getAllOrderItems(){
        //Load basket file
        $basket = $this->persistentBasketDAL->load();
        $products = array();
        $pieces = explode("\n", $basket);

        //Add item to array if it doesn't already exists in array
        foreach($pieces as $item){
            if(!in_array($item, $products)) {
                array_push($products, $item);
            }
        }

        foreach($products as $item){
            if($item != "") {
                $productName = $this->productCatalog->getProductByName($item);
                if(substr_count($basket,$item) > $productName->getQuantity()){
                    return false;
                }
            }
        }
        return true;
    }

    public function wantToPurchase() {
        if(isset($_POST[self::$checkoutButton])) {
            return $_POST[self::$checkoutButton];
        }
        return null;
    }

    public function getCheckoutSSN() {
        if(isset($_POST[self::$checkoutSSN])) {
            return $_POST[self::$checkoutSSN];
        }
        return null;
    }
    public function getCheckoutFirstName() {
        if(isset($_POST[self::$checkoutFirstName])) {
            return $_POST[self::$checkoutFirstName];
        }
        return null;
    }
    public function getCheckoutLastName() {
        if(isset($_POST[self::$checkoutLastName])) {
            return $_POST[self::$checkoutLastName];
        }
        return null;
    }
    public function getCheckoutEmail() {
        if(isset($_POST[self::$checkoutEmail])) {
            return $_POST[self::$checkoutEmail];
        }
        return null;
    }
    public function getCheckoutAddress() {
        if(isset($_POST[self::$checkoutAddress])) {
            return $_POST[self::$checkoutAddress];
        }
        return null;
    }
    public function getCheckoutPostalCode() {
        if(isset($_POST[self::$checkoutPostalCode])) {
            return $_POST[self::$checkoutPostalCode];
        }
        return null;
    }
    public function getCheckoutCity() {
        if(isset($_POST[self::$checkoutCity])) {
            return $_POST[self::$checkoutCity];
        }
        return null;
    }

    public function getProductsToOrder(){
        $itemsInBasket = $this->getProductsFromCookie();


        $getObjectFromName = Array();
        foreach ($itemsInBasket as $productInBasket) {

            if ($productInBasket != "") {
                array_push($getObjectFromName, $this->productCatalog->getProductByName($productInBasket));
            }
        }
        return $getObjectFromName;
    }

    public function getProductsFromCookie(){
        $basket = $this->persistentBasketDAL->load();
        $pieces = explode("\n", $basket);
        return $pieces;
    }

    public function wantsToAddProductToBasket(){
        if(isset($_POST[self::$addProductToBasket])){
            return $_POST[self::$addProductToBasket];
        }
    }

    public function getItemToRemoveFromBasket(){
        $id = $_GET['product'];
        $productToAdd = $this->productCatalog->getProductById($id);
        return $productToAdd;
    }

    private function getViewCheckoutLink(){
        return $this->navView->getViewCheckoutLink("Checkout");
    }

    public function getPurchaseProductsLink(){
            return $this->navView->getPurchaseProductsLink("Confirm");
    }

    private function removeOneItemFromBasketLink(\model\ProductModel $product){
        return $this->navView->getRemoveOneItemFromBasket(self::$ProductPosition . '=' . $product->GetID(), "<i class='fa fa-times'></i>");
    }

    private function removeItemFromBasketLink(\model\ProductModel $product){
        return $this->navView->getRemoveItemFromBasket(self::$ProductPosition . '=' . $product->GetID(), "Remove all");
    }

    private function getViewLinkFromBasket(\model\ProductModel $product){
        return $this->navView->getViewProductLink(self::$ProductPosition . '=' . $product->GetID(), $product->getName()) . ' ';
    }

    private function getViewLink(\model\ProductModel $product){
        return $this->navView->getViewProductLink(self::$ProductPosition . '=' . $product->GetID(), "Visa " . $product->getName()) . ' ';
    }

    public function rememberBasketForUser(){
        $id = $_GET['product'];
        $productToSave = $this->productCatalog->getProductById($id);

        $this->expirationDate = time() + (86400 * 30);

        //Save cookie for name and password
        $this->cookie->save(self::$cookieProductId, $id, $this->expirationDate);
        $this->cookie->save(self::$cookieProduct, $productToSave->getName(), $this->expirationDate);

        return $productToSave->getName();
    }

    public function reloadBasketPage(){
        header('Location: /project-inlog/index.php?action=ViewBasket' );
    }

    //Delete cookie
    public function forgetBasket(){
        $this->cookie->delete(self::$cookieProduct);
        $this->cookie->delete(self::$cookieProductId);
    }

    //Set message to show user
    public function setMessage($message){
        assert(is_string($message));
        return $this->message = $message;
    }

    //Set message to show user
    public function setSuccessMessage($message){
        assert(is_string($message));
        return $this->successMessage = $message;
    }
}