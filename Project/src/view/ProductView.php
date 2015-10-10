<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-02
 * Time: 18:30
 */

namespace view;


class ProductView implements IView
{

    private $productRepository;
    private $navView;
    private $cookie;
    private $message;
    private $persistentBasketDAL;
    private $expirationDate;
    private static $cookieProductId = 'LoginView::CookieProductId';
    private static $cookieProduct = 'LoginView::CookieProduct';

    private static $name = "MemberView::Name";
    private static $ssn = "MemberView::Ssn";
    private static $removeFromBasket = "ProductView::RemoveFromBasket";
    private static $messageId = "MemberView::MessageId";
    private static $orderBasket = "ProductView::OrderBasket";
    private static $addProductToBasket = "ProductView::AddToBasket";
    private static $flashLocation = 'ProductView::FlashMessage';

    private static $loginForm = 'LoginView::LoginForm';
    private static $logout = 'LoginView::Logout';

    private static $ProductPosition = "product";

    public function __construct(\model\dal\ProductRepository $repo, NavigationView $navView){
        $this->productRepository = $repo;
        $this->navView = $navView;
        $this->loginModel = new \model\LoginModel();
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->persistentBasketDAL = new \model\dal\ProductBasketDAL();

        $this->cookie = new \common\CookieStorage();
    }

    public function response(){
        /*$message = $this->message;

        if($this->loginView->isLoggedIn()){
            $ret = $this->generateLogoutButtonHTML($message);
        }
        else{
            $ret = "";
        }
        $ret .= '<div class="jumbotron">';
        $ret .= '<h1>Welcome</h1>';
        $ret .= '<p id="' . self::$messageId . '">' . $message . '</p>';
        $ret .= '<h1>All products</h1>';
        $ret .= '<ul>';

        foreach($this->productRepository->getAllProducts() as $product){
            $ret .= '<li>Name: ' . $product->getName() . ' - Price: ' . $product->getPrice() . ' - ID: ' . $product->getId() . ' ' . $this->getViewLink($product) . PHP_EOL;
            $ret .= '</li>';
        }
        $ret .= '</ul>';
        $ret .= '</div>';
        return $ret;*/
    }


    //If user presses logout-button
    public function logoutAttempt(){
        return isset($_POST[self::$logout]);
    }

    public function userWantsToLogout(){
        if(isset($_POST[self::$loginForm])){
            return $_POST[self::$loginForm];
        }
    }




    public function viewAllProducts(){

        $message = $this->message;

        $ret = '<div class="jumbotron">';
        $ret .= '<h1>Welcome</h1>';
        $ret .= '<h3>All products</h3>';
        $ret .= '<p id="' . self::$messageId . '">' . $message . '</p>';
        $ret .= '<div class="row">';

        foreach($this->productRepository->getAllProducts() as $product) {
            $ret .= '<div class="col-sm-6 col-md-3">';
            $ret .= '<div class="thumbnail">';
            $ret .= '<p>'.$product->getImage().'</p>';
            $ret .= '<div class="caption">';
            $ret .= '<h3>'. $product->getName() .'</h3>';
            $ret .= '<p>$'. $product->getPrice() .'</p>';
            $ret .= '<p>'. $product->getDescription() .'</p>';
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


        $id = $_GET['product'];
        $productToShow = $this->productRepository->getProductById($id);

        $ret = '<div class="jumbotron">';

        $ret .= '<div class="col-md-6">';
        $ret .= '</div>';
        $ret .= '<p id="' . self::$messageId . '">' . $message . '</p>';
        $ret .= '<h1>'.$productToShow->getName().'</h1>';
        $ret .= '<ul>';
        $ret .= '<p>'.$productToShow->getImage().'</p>';
        $ret .= '<li>Price: $' . $productToShow->getPrice(). '</li>';
        $ret .= '<li>Description: ' . $productToShow->getDescription(). '</li>';
        if($this->loginView->isLoggedIn() === true) {
            $ret .= '
            <form method="post">
                <input id="submit" class="btn btn-primary" type="submit" name="' . self::$addProductToBasket . '"  value="Add to basket" />
            </form>';
        }
        $ret .= '</li>';
        $ret .= '</ul>';
        $ret .= '</div>';
        return $ret;
    }

    public function viewBasket(){
        $message = $this->message;

        if($basket = $this->persistentBasketDAL->load() === false){
            $ret = '<div class="jumbotron">';
            $ret .= '<p>You have no products in your basket yet!</p>';
            $ret .= '</div>';
        }
        else {
            //Load cookies from file
            $basket = $this->persistentBasketDAL->load();
            $pieces = explode("\n", $basket);

            $objectsToShow = array();
            $allObjectsInCookie = array();
            $totalPrice = 0;
            $quantity = 1;

            foreach ($pieces as $productInBasket) {

                if ($productInBasket != "") {


                    if(in_array($productInBasket, $objectsToShow)) {
                    }
                    else{

                        array_push($objectsToShow, $productInBasket);
                    }

                    array_push($allObjectsInCookie, $productInBasket);

                }
            }
            $ret = '<div class="jumbotron">';
            $ret .= '<p>Products in your basket:</p>';
            $ret .= '<table class="table">';
            $ret .= '<tr>';
            $ret .= '<th>Product</th>';
            $ret .= '<th>Price per product</th>';
            $ret .= '<th>Quantity</th>';
            $ret .= '</tr>';
            foreach ($objectsToShow as $basketItem) {
                $getObjectFromName = $this->productRepository->getProductByName($basketItem);

                $array_count = array_count_values($allObjectsInCookie);
                $totalPrice += $getObjectFromName->getPrice() * $array_count["$basketItem"];
                $quantity = $array_count["$basketItem"];


                $ret .= '<tr>';
                $ret .= '<td><h3>'. $getObjectFromName->getImage(50) .' '.  $this->getViewLinkFromBasket($getObjectFromName) . PHP_EOL .'</h3>';
                $ret .= '<td>$'. $getObjectFromName->getPrice() .'</td>';
                $ret .= '<td>'. $quantity .'</td>';
                $ret .= '<td>';
                $ret .= '
                    <form method="post">
                        <input id="submit" class="btn btn-primary" type="submit" name="' . self::$removeFromBasket . '"  value="x" />
                    </form>';
                $ret .= '</td>';
                $ret .= '</tr>';
            }
            $ret .= '</table>';
            $ret .= '<h2>Total: $'. $totalPrice .'</h2>';
            $ret .= '
            <form method="post">
                <input id="submit" class="btn btn-primary" type="submit" name="' . self::$orderBasket . '"  value="Checkout" />
            </form>';
            $ret .= '</div>';
        }
        return $ret;

    }

    public function wantsToAddProductToBasket(){
        if(isset($_POST[self::$addProductToBasket])){
            return $_POST[self::$addProductToBasket];
        }
    }

    private function getViewLinkFromBasket(\model\ProductModel $product){
        return $this->navView->getViewProductLink(self::$ProductPosition . '=' . $product->GetID(), $product->getName()) . ' ';
    }

    private function getViewLink(\model\ProductModel $product){
        return $this->navView->getViewProductLink(self::$ProductPosition . '=' . $product->GetID(), "Visa " . $product->getName()) . ' ';
    }

    public function getProductToAdd(){
        $id = $_GET['product'];
        $productToAdd = $this->productRepository->getProductById($id);
        return $productToAdd;
    }

    public function rememberBasketForUser(){
        $id = $_GET['product'];
        $productToSave = $this->productRepository->getProductById($id);

        $this->expirationDate = time() + (86400 * 30);

        //Save cookie for name and password
        //$this->cookie->save(self::$cookieName, $this->userModel->getUsername(), $this->expirationDate);
        $this->cookie->save(self::$cookieProductId, $id, $this->expirationDate);
        $this->cookie->save(self::$cookieProduct, $productToSave->getName(), $this->expirationDate);

        return $productToSave->getName();
    }

    //Delete cookie
    public function forgetBasket(){
        $this->cookie->delete(self::$cookieProduct);
        $this->cookie->delete(self::$cookieProductId);
    }



    public function reloadPage(){
        header('Location: /Project/index.php' );
    }

    //Set message to show user
    public function setMessage($message){
        assert(is_string($message));
        return $this->message = $message;
    }

    public function getFlashMessage()
    {
        if ($this->cookie->isCookieSet(self::$flashLocation)) {
            $this->message = $this->cookie->load(self::$flashLocation);
            $this->cookie->delete(self::$flashLocation);
        }
    }

    public function setFlashMessage($message) {
        assert(is_string($message), "ProductView::setMessage needs a string as argument");
        $this->cookie->save(self::$flashLocation, $message, time() + 3600);
    }
}