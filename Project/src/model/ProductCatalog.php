<?php

namespace model;

/**
 * Class ProductCatalogModel
 * Used to communicate with product repository
 * @package model
 */
class ProductCatalog
{
    private $productRepository;

    public function __construct(){
        $this->productRepository = new dal\ProductRepository();
    }

    public function getProductById($id){
        return $this->productRepository->getProductById($id);
    }

    public function getProductByName($name){
        return $this->productRepository->getProductByName($name);
    }

    public function getProductsPagination($page = 1, $limit = 4){
        return $this->productRepository->getProductsPagination($page, $limit);
    }

    public function getAllProducts(){
        return $this->productRepository->getAllProducts();
    }

    public function reduceQuantity($id, $newQuantity){
        $this->productRepository->reduceQuantity($id, $newQuantity);
    }

}