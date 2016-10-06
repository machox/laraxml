<?php

namespace App;

use \App\Obay;

class Employee extends Obay
{
    /**
     * The file associated with the model
     *
     * @var string
     */
    protected $file = 'data.xml';

    /**
     * Root element of the document
     *  
     * @var string
     */
    protected $root = 'data';  

    /**
     * Child elements of the root
     * 
     * @var string 
     */
    protected $child = 'row';         

    /**
     * Child elements of the child
     * 
     * @var string 
     */
    protected $child_end = 'field';     

    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $mapping = true;
}
