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

    private $repository;
    private $boatView;
    private $navView;
    private $cookie;
    private $message;

    private static $name = "MemberView::Name";
    private static $ssn = "MemberView::Ssn";
    private static $registration = "MemberView::Register";
    private static $messageId = "MemberView::MessageId";
    private static $deleteMember = "MemberView::Delete";
    private static $addProductToBasket = "ProductView::AddToBasket";
    private static $flashLocation = 'ProductView::FlashMessage';

    private static $loginForm = 'LoginView::LoginForm';
    private static $logout = 'LoginView::Logout';

    private static $ProductPosition = "product";

    public function __construct(\model\dal\ProductRepository $repo, NavigationView $navView){
        $this->repository = $repo;
        $this->navView = $navView;
        $this->loginModel = new \model\LoginModel();
        $this->loginView = new \view\LoginView($this->loginModel);

        $this->cookie = new \common\CookieStorage();
    }

    public function response(){
        $message = $this->message;

        if($this->loginView->isLoggedIn()){
            $ret = $this->generateLogoutButtonHTML($message);
        }
        else{
            $ret = "";
        }
        $ret .= '<h1>Welcome</h1>';
        $ret .= '<div class="jumbotron">';
        $ret .= '<p id="' . self::$messageId . '">' . $message . '</p>';
        $ret .= '<h1>All products</h1>';
        $ret .= '<ul>';

        foreach($this->repository->getAllProducts() as $product){
            $ret .= '<li>Name: ' . $product->getName() . ' - Price: ' . $product->getPrice() . ' - ID: ' . $product->getId() . ' ' . $this->getViewLink($product) . PHP_EOL;
            $ret .= '</li>';
        }
        $ret .= '</ul>';
        $ret .= '</div>';
        return $ret;
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

        $ret = '<h1>Welcome</h1>';
        $ret .= '<div class="jumbotron">';
        $ret .= '<h1>All products</h1>';
        $ret .= '<p id="' . self::$messageId . '">' . $message . '</p>';
        $ret .= '<ul>';

        foreach($this->repository->getAllProducts() as $product){
            $ret .= '<li>Name: ' . $product->getName() . ' - Price: ' . $product->getPrice() . ' - ID: ' . $product->getId() . ' ' . $this->getViewLink($product) . PHP_EOL;
            $ret .= '</li>';
        }
        $ret .= '</ul>';
        $ret .= '</div>';
        return $ret;
    }

    public function viewProduct(){

        $message = $this->message;


        $id = $_GET['product'];
        $productToShow = $this->repository->getProductById($id);

        $ret = '<div class="jumbotron">';
        $ret .= '<p id="' . self::$messageId . '">' . $message . '</p>';
        $ret .= '<h1>'.$productToShow->getName().'</h1>';
        $ret .= '<ul>';
        $ret .= '<li>Price: $' . $productToShow->getPrice(). '</li>';
        $ret .= '<li>Description: ' . $productToShow->getDescription(). '</li>';
        $ret .= '
            <form method="post">
                <input id="submit" class="btn btn-primary" type="submit" name="' . self::$addProductToBasket . '"  value="Add to basket" />
            </form>';
        $ret .= '</li>';
        $ret .= '</ul>';
        $ret .= '</div>';
        return $ret;
    }

    public function wantsToAddProductToBasket(){
        if(isset($_POST[self::$addProductToBasket])){
            return $_POST[self::$addProductToBasket];
        }
    }

    private function getViewLink(\model\ProductModel $product){
        return $this->navView->getViewProductLink(self::$ProductPosition . '=' . $product->GetID(), "View " . $product->getName()) . ' ';
    }

    public function getProductToAdd(){
        $id = $_GET['product'];
        $productToAdd = $this->repository->getProductById($id);
        return $productToAdd;
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