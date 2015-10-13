<?php

namespace view;


class NavigationView
{
    const ViewAllProducts = "ViewAllProducts";
    const RemoveItemFromBasket = "RemoveItemFromBasket";
    const ViewProduct = "ViewProduct";
    const LoginUser = "LoginUser";
    const AddProductToBasket = "AddProductToBasket";
    const ViewBasket = "ViewBasket";
    const RegisterUser = "RegisterUser";
    const ViewCheckout = "ViewCheckout";
    const PurchaseProducts = "PurchaseProducts";
    //const DeleteBoat = "DeleteBoat";
    private static $action = "action";
    public function GetAction(){
        if(isset($_GET[self::$action])){
            return $_GET[self::$action];
        }
        return null;
    }
    public function GetRegisterUserLink($title){
        return '<a href="?' . self::$action . '=' . self::RegisterUser . '">' . $title . '</a>';
    }
    public function GetBasketLink($title){
        return '<a href="?' . self::$action . '=' . self::ViewBasket . '">' . $title . '</a>';
    }

    public function GetLoginUserLink($title){
        return '<a href="?' . self::$action . '=' . self::LoginUser . '">' . $title . '</a>';
    }
    public function getViewProductLink($extra, $title){
        return '<a href="?' . self::$action . '=' . self::ViewProduct . '&' . $extra . '">' . $title . '</a>';
    }

    public function getAddToBasketLink($extra, $title){
        return '<a href="?' . self::$action . '=' . self::AddProductToBasket . '&' . $extra . '">' . $title . '</a>';
    }

    public function getRemoveItemFromBasket($extra, $title){
        return '<a href="?' . self::$action . '=' . self::RemoveItemFromBasket . '&' . $extra . '">' . $title . '</a>';
    }

    public function getViewCheckoutLink($title){
        return '<a href="?' . self::$action . '=' . self::ViewCheckout . '">' . $title . '</a>';
    }

    public function getPurchaseProductsLink($title){
        return '<a href="?' . self::$action . '=' . self::PurchaseProducts . '">' . $title . '</a>';
    }

    public function getBackLink(){
        return '<div><a href="?">Go back</a></div>';
    }
    public function ShowInstructions(){
        return '
    <h2>Menu</h2>
    <ol>
        <li><a href="?' . self::$action . '=' . self::AddMember . '">Add Member</a></li>
    </ol>
        ';
    }

}