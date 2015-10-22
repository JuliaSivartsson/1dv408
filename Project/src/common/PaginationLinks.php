<?php

namespace common;

class PaginationLinks{

    private $_limit;
    private $_page;
    private $_total;
    /**
     * Source: http://code.tutsplus.com/tutorials/how-to-paginate-data-with-php--net-2928
     * I used this guys code to help me render the pagination links, the only thing I used as help
     * was the function createLinks in his Paginator class.
     */
    public function createLinks($page, $limit, $totalRows, $links, $list_class ) {
        $this->_page = $page;
        $this->_limit = $limit;
        $this->_total = $totalRows;


        if ( $this->_limit == 'all' ) {
            return '';
        }

        $last       = ceil( $this->_total / $this->_limit );

        $start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
        $end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

        $html       = '<ul class="' . $list_class . '">';

        if($this->_page == 1){
            $html .= '<li></li>';
        }
        else{
            $class = "";
            $html .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page - 1 ) . '">&laquo;</a></li>';
        }
        if ( $start > 1 ) {
            $html .= '<li><a href="?limit=' . $this->_limit . '&page=1">1</a></li>';
            $html .= '<li class="disabled"><span>...</span></li>';
        }

        for ( $i = $start ; $i <= $end; $i++ ) {
            $class = ( $this->_page == $i ) ? "active" : "";
            $html .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
        }

        if ( $end < $last ) {
            $html .= '<li class="disabled"><span>...</span></li>';
            $html .= '<li></li>';
        }

        if($this->_page == $last){
            $html .= '<li></li>';
        }
        else{
            $class = "";
            $html .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page + 1 ) . '">&raquo;</a></li>';
        }

        $html .= '</ul>';

        return $html;
    }

}