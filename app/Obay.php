<?php

namespace App;

use \Config;
use \ArrayIterator;
use \IteratorAggregate;
use \SimpleXMLElement;
use \Exception;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Salvaon 1.0.5 "Senhor Todo-Poderoso"
 *
 * Base functions for XML models
 */
abstract class Obay implements IteratorAggregate {

    /**
     * SimpleXMLElement object from loaded XML file.
     *
     * @var object|array|null
     */
    public $xml = null;

    /**
     * The file associated with the model.
     *
     * @var string
     */
    protected $file;

    /**
     * Root element of the document.
     *
     * @var string
     */
    protected $root;

    /**
     * Child elements of the root.
     *
     * @var string
     */
    protected $child;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $mapping = true;

    /**
     * Attributes to add new child.
     *
     * @var array
     */
    protected $new = array();

    /**
     * SimpleXMLElement xpath expression in parts.
     *
     * @var array
     */
    protected $query = array(
        'where'    => '',
        'contains' => '',
        'order'    => false,
        'limit'    => false
    );

    /**
     * Full path to XML file.
     *
     * @var string
     */
    protected static $path;

    /**
     * The array of booted models.
     *
     * @var array
     */
    protected static $booted = array();

    /**
     * SimpleXMLElement object from loaded XML file.
     *
     * @var object
     */
    protected static $xmlObject = null;

    /**
     * Create a new Salvaon model instance.
     *
     * @return Salvaon
     */
    public function __construct() {
        $this->bootIfNotBooted();
    }

    /**
     * Get an iterator for the XML object.
     *
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->xml);
    }

    public static function array2XML($obj, $array)
    {
        foreach ($array as $key => $value)
        {
            if(is_numeric($key))
                $key = 'row';

            if (is_array($value))
            {
                $node = $obj->addChild($key);
                self::array2XML($node, $value);
            }
            else
            {
                $obj->addChild($key, htmlspecialchars($value));
            }
        }
    }

    public static function array2XMLDB($obj, $array)
    {
        foreach ($array as $key => $value)
        {
            if(is_numeric($key))
                $key = 'row';

            if (is_array($value))
            {
                $node = $obj->addChild($key);
                self::array2XMLDB($node, $value);
            }
            else
            {
                $node = $obj->addChild('field', htmlspecialchars($value));
                $node->addAttribute('name', $key);
            }
        }
    }

    /**
     * Check if the model needs to be booted and if so, do it.
     *
     * @return void
     */
    protected function bootIfNotBooted() {
        libxml_use_internal_errors(true); 

        $class = get_class($this);

        if (!isset(static::$booted[$class])) {
            static::$path = Config::get('obay.path', storage_path() . '/xml') . '/' . $this->file;
            try{ 
                static::$xmlObject = new SimpleXMLElement(static::$path, 0, true);
            } catch (Exception $e){ 
                echo 'This '.$this->file.' xml is not valid'; 
                exit(); 
            }

            if($this->mapping) {
                $newData = [];
                $i = 0;
                foreach (static::$xmlObject->row as $row) {
                    foreach ($row->field as $field)  {
                        $key = "$field[name]";
                        $val = "$field";
                        $newData[$i][$key] = $val;
                    }
                    $i++;
                }

                $xml = new SimpleXMLElement('<data/>');
                self::array2XML($xml, $newData);
                try{ 
                    static::$xmlObject = new SimpleXMLElement($xml->asXML(), 0, false);
                } catch (Exception $e){ 
                    echo 'This '.$this->file.' xml is not valid'; 
                    exit(); 
                }
            }

            static::$booted[$class] = array(
                'path' => static::$path,
                'object' => static::$xmlObject
            );
        } else {
            static::$path = static::$booted[$class]['path'];
            static::$xmlObject = static::$booted[$class]['object'];
        }
    }

    /**
     * SimpleXML xpath method.
     *
     * @param string $query
     * @return array
     */
    protected function xpath($query) {
        return static::$xmlObject->xpath($query);
    }

    /**
     * Find a model by its primary key or fail if not exists.
     *
     * @param mixed $id
     * @return array|static
     */
    public static function findOrFail($id) {
        $model = self::find($id);

        if (is_null($model)) {
            throw (new ModelNotFoundException)->setModel(get_called_class());
        }

        return $model;
    }

    /**
     * Find a model by its primary key.
     *
     * @param mixed $id
     * @return array|static
     */
    public static function find($id) {
        $instance = new static;

        $query = "/{$instance->root}/{$instance->child}[{$instance->primaryKey}=\"{$id}\"]";

        $result = $instance->xpath($query);

        if (empty($result)) {
            return null;
        }

        $instance->xml = $result[0];

        return $instance;
    }

    /**
     * Get all items from model.
     *
     * @return array|static
     */
    public static function all() {
        $instance = new static;

        $query = "/{$instance->root}/{$instance->child}";

        $instance->xml = $instance->xpath($query);

        return $instance;
    }

    public static function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? self::xml2array ( $node ) : $node;

        return $out;
    }

    /**
     * Save changes from XML object.
     *
     * @param array $attributes Attributes for child that you are added.
     * @return Salvaon
     * @throws Exception
     */
    public function save($attributes = array()) {
        if (!empty($this->new)) {
            $this->addChildWithAttributes($attributes);
        }

        if($this->mapping) {
            $newData = [];
            foreach (static::$xmlObject->row as $row) {
                $newData[] = $row;
            }
            $newArr = self::xml2array($newData);
            $xml = new SimpleXMLElement('<data/>');
            self::array2XMLDB($xml, $newArr);
            static::$xmlObject = new SimpleXMLElement($xml->asXML(), 0, false);
        }

        $result = static::$xmlObject->asXML(static::$path);

        if ($result === false) {
            throw new Exception('Cannot save values into "' . static::$path . '".');
        } else {
            if($this->mapping) {
                $newData = [];
                $i = 0;
                foreach (static::$xmlObject->row as $row) {
                    foreach ($row->field as $field)  {
                        $key = "$field[name]";
                        $val = "$field";
                        $newData[$i][$key] = $val;
                    }
                    $i++;
                }

                $xml = new SimpleXMLElement('<data/>');
                self::array2XML($xml, $newData);
                static::$xmlObject = new SimpleXMLElement($xml->asXML(), 0, false);
            }
        }

        return $this;
    }

    /**
     * Delete XML node from object.
     *
     * @return Salvaon
     */
    public function delete() {
        unset($this->xml[0]);

        return $this->save();
    }

    /**
     * Count XML nodes from object.
     *
     * @return integer
     */
    public function count() {
        return count($this->xml);
    }

    /**
     * Select something from XML file with xpath syntax.
     *
     * @param string $query
     * @return array|static
     */
    public static function raw($query) {
        $instance = new static;

        $instance->xml = $instance->xpath($query);

        return $instance;
    }

    /**
     * Initialize query builder static instance.
     *
     * @return static
     */
    public static function select() {
        $instance = new static;

        return $instance;
    }

    /**
     * where()
     *
     * @param string $attribute
     * @param string $operator
     * @param mixed $value
     * @param string $where [default: 'and']
     * @return Salvaon
     */
    public function where($attribute, $operator, $value, $where = 'and') {
        if (!empty($this->query['where'])) {
            $this->query['where'] .= " {$where} ";
        }

        $this->query['where'] .= "{$attribute}{$operator}\"{$value}\"";

        return $this;
    }

    /**
     * orWhere()
     *
     * @param string $attribute
     * @param string $operator
     * @param mixed $value
     * @return Salvaon
     */
    public function orWhere($attribute, $operator, $value) {
        return $this->where($attribute, $operator, $value, 'or');
    }

    /**
     * contains()
     *
     * @param string $attribute
     * @param mixed $value
     * @param string $contains [default: 'and']
     * @return Salvaon
     */
    public function contains($attribute, $value, $contains = 'and') {
        if (!empty($this->query['contains'])) {
            $this->query['contains'] .= " {$contains} ";
        }

        $this->query['contains'] .= "contains({$attribute}, \"{$value}\")";

        return $this;
    }

    /**
     * orContains()
     *
     * @param string $attribute
     * @param mixed $value
     * @return Salvaon
     */
    public function orContains($attribute, $value) {
        return $this->contains($attribute, $value, 'or');
    }

    /**
     * skip()
     *
     * @param integer $skip
     * @return Salvaon
     */
    public function skip($skip) {
        $this->query['limit']['skip'] = $skip;

        return $this;
    }

    /**
     * take()
     *
     * @param integer $take
     * @return Salvaon
     */
    public function take($take) {
        $this->query['limit']['take'] = $take;

        return $this;
    }

    /**
     * orderBy()
     *
     * @param string $attribute
     * @param string $type
     * @return Salvaon
     */
    public function orderBy($attribute, $type = "asc") {
        $this->query['order']['attribute'] = $attribute;
        $this->query['order']['type'] = $type;

        return $this;
    }

    /**
     * get()
     *
     * @return array
     */
    public function get() {
        $query = "/{$this->root}/{$this->child}";

        if (!empty($this->query['where'])) {
            $query.= "[{$this->query['where']}]";
        }

        if (!empty($this->query['contains'])) {
            $query.= "[{$this->query['contains']}]";
        }

        $result = $this->xpath($query);

        if (is_array($this->query['order'])) {
            $result = $this->queryOrderBy($result, $this->query['order']['type']);
        }

        if (is_array($this->query['limit'])) {
            $skip = isset($this->query['limit']['skip']) ? $this->query['limit']['skip'] : 0;
            $result = $this->queryLimit($result, $skip, $this->query['limit']['take']);
        }

        $this->xml = $result;

        return $this;
    }

    /**
     * get()
     *
     * @return array
     */
    public function first() {
        $query = "/{$this->root}/{$this->child}";

        if (!empty($this->query['where'])) {
            $query.= "[{$this->query['where']}]";
        }

        if (!empty($this->query['contains'])) {
            $query.= "[{$this->query['contains']}]";
        }

        $result = $this->xpath($query);

        if (is_array($this->query['order'])) {
            $result = $this->queryOrderBy($result, $this->query['order']['type']);
        }

        if($result) $this->xml = $result[0];

        return $this;
    }

    /**
     * queryLimit()
     *
     * @param array $array
     * @param integer $skip
     * @param integer $take
     * @return array
     */
    protected function queryLimit($array, $skip, $take) {
        return array_slice($array, $skip, $take);
    }

    /**
     * queryOrderBy()
     *
     * @param array $array
     * @param string $type
     * @return array
     */
    protected function queryOrderBy($array, $type) {
        usort($array, __class__ . '::queryCompare');

        return ($type == 'desc') ? array_reverse($array) : $array;
    }

    /**
     * queryCompare()
     *
     * @param string $a
     * @param string $b
     * @return integer
     */
    protected function queryCompare($a, $b) {
        return strnatcmp($a->{$this->query['order']['attribute']}, $b->{$this->query['order']['attribute']});
    }

    /**
     * Add child with attributes to $xmlObject.
     *
     * @param array $attributes
     * @return void
     */
    protected function addChildWithAttributes($attributes = array()) {
        $child = static::$xmlObject->addChild($this->child);

        foreach ($attributes as $key => $value) {
            $child->addAttribute($key, $value);
        }

        foreach ($this->new as $key => $value) {
            $child->addChild($key, $value);
        }
    }
        
    /**
     * Get array of SimpleXMLElement objects
     * 
     * @return array|null
     */
    public function toArray() {
        if (!is_array($this->xml) && !is_null($this->xml)) {
            return array($this->xml);
        }
        
        return $this->xml;
    }
            
    /**
     * Check if array of SimpleXMLElement objects is empty
     * 
     * @return boolean
     */
    public function isEmpty() {
        if (empty($this->xml)) {
            return true;
        }
        
        return false;
    }

   /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function __get($key) {
        if (isset($this->xml->{$key})) {
            return $this->xml->{$key};
        }

        throw new Exception('Attribute "' . $key . '" not found in "' . get_class(new static) . '".');
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value) {
        if (is_null($this->xml)) {
            $this->new[$key] = $value;
        } else {
            $this->xml->$key = $value;
        }
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key) {
        return isset($this->xml->$key);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param string $key
     * @return void
     */
    public function __unset($key) {
        unset($this->xml->$key);
    }

    /**
     * Convert the class to its string representation.
     *
     * @return string
     */
    public function __toString() {
        return $this->xml;
    }

}

