<?php

namespace view;


class NavigationView
{
    const ViewAllProducts = "ViewAllProducts";
    //const ViewAll = "ViewAll";
    const ViewProduct = "ViewProduct";
    const LoginUser = "LoginUser";
    const AddProductToBasket = "AddProductToBasket";
    //const DeleteMember = "DeleteMember";
    //const EditBoat = "EditBoat";
    //const AddBoat = "AddBoat";
    //const DeleteBoat = "DeleteBoat";
    private static $action = "action";
    public function GetAction(){
        if(isset($_GET[self::$action])){
            return $_GET[self::$action];
        }
        return null;
    }
    /*public function GetDeleteMemberLink($extra, $title){
        return '<a href="?' . self::$action . '=' . self::DeleteMember . '&' . $extra . '">' . $title . '</a>';
    }*/
    public function GetLoginUserLink($title){
        return '<a href="?' . self::$action . '=' . self::LoginUser . '">' . $title . '</a>';
    }
    public function getViewProductLink($extra, $title){
        return '<a href="?' . self::$action . '=' . self::ViewProduct . '&' . $extra . '">' . $title . '</a>';
    }

    public function getAddToBasketLink($extra, $title){
        return '<a href="?' . self::$action . '=' . self::AddProductToBasket . '&' . $extra . '">' . $title . '</a>';
    }
/*
    public function GetDeleteBoatLink($extra, $title){
        return '<a href="?' . self::$action . '=' . self::DeleteBoat . '&' . $extra . '">' . $title . '</a>';
    }*/
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