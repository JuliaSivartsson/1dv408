<?php
namespace model;

class LayoutModel
{
    public $title = '1dv608';
    public $header = 'Assignment 2';
    public $authenticated;
    public $body;

    public function __construct(string $body, bool $authenticated){
        $this->body = $body;
        $this->authenticated = $authenticated;
    }
}