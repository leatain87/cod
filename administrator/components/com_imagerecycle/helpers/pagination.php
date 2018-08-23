<?php

/**
 * Description of pagination
 *
 * @author nguyen
 */
class IRPagination extends JPagination
{
    //override 
    function getListFooter()
    {

        return $this->getPaginationLinks();
    }
}