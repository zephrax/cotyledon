<?php

namespace DB;

/**
 * Model base class. Your model objects should extend
 * this class. A minimal subclass would look like:
 *
 * class Widget extends Model {
 * }
 *
 */
class Model {

    // Default ID column for all models. Can be overridden by adding
    // a public static _id_column property to your model classes.
    const DEFAULT_ID_COLUMN = 'id';

    // Default foreign key suffix used by relationship methods
    const DEFAULT_FOREIGN_KEY_SUFFIX = '_id';

    /**
     * The ORM instance used by this model 
     * instance to communicate with the database.
     */
    public $orm;

    /**
     * Retrieve the value of a static property on a class. If the
     * class or the property does not exist, returns the default
     * value supplied as the third argument (which defaults to null).
     */
    protected static function _get_static_property($class_name, $property, $default=null) {
        if (!class_exists($class_name) || !property_exists($class_name, $property)) {
            return $default;
        }
        $properties = get_class_vars($class_name);
        return $properties[$property];
    }

    /**
     * Static method to get a table name given a class name.
     * If the supplied class has a public static property
     * named $_table, the value of this property will be
     * returned. If not, the class name will be converted using
     * the _class_name_to_table_name method method.
     */
    protected static function _get_table_name($class_name) {
        $specified_table_name = self::_get_static_property($class_name, '_table');
        if (is_null($specified_table_name)) {
            return self::_class_name_to_table_name($class_name);
        }
        return $specified_table_name;
    }

    /**
     * Static method to convert a class name in CapWords
     * to a table name in lowercase_with_underscores.
     * For example, CarTyre would be converted to car_tyre.
     */
    protected static function _class_name_to_table_name($class_name) {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $class_name));
    }

    /**
     * Return the ID column name to use for this class. If it is
     * not set on the class, returns null.
     */
    protected static function _get_id_column_name($class_name) {
        return self::_get_static_property($class_name, '_id_column', self::DEFAULT_ID_COLUMN);
    }

    /**
     * Build a foreign key based on a table name. If the first argument
     * (the specified foreign key column name) is null, returns the second
     * argument (the name of the table) with the default foreign key column
     * suffix appended.
     */
    protected static function _build_foreign_key_name($specified_foreign_key_name, $table_name) {
        if (!is_null($specified_foreign_key_name)) {
            return $specified_foreign_key_name;
        }
        return $table_name . self::DEFAULT_FOREIGN_KEY_SUFFIX;
    }

    /**
     * Factory method used to acquire instances of the given class.
     * The class name should be supplied as a string, and the class
     * should already have been loaded by PHP (or a suitable autoloader
     * should exist). This method actually returns a wrapped ORM object
     * which allows a database query to be built. The wrapped ORM object is
     * responsible for returning instances of the correct class when
     * its find_one or find_many methods are called.
     */
    public static function factory($class_name) {
        $table_name = self::_get_table_name($class_name);
        $wrapper = ORMWrapper::for_table($table_name);
        $wrapper->set_class_name($class_name);
        $wrapper->use_id_column(self::_get_id_column_name($class_name));
        return $wrapper;
    }

    /**
     * Internal method to construct the queries for both the has_one and
     * has_many methods. These two types of association are identical; the
     * only difference is whether find_one or find_many is used to complete
     * the method chain.
     */
    protected function _has_one_or_many($associated_class_name, $foreign_key_name=null) {
        $base_table_name = self::_get_table_name(get_class($this));
        $foreign_key_name = self::_build_foreign_key_name($foreign_key_name, $base_table_name);
        return self::factory($associated_class_name)->where($foreign_key_name, $this->id());
    }

    /**
     * Helper method to manage one-to-one relations where the foreign
     * key is on the associated table.
     */
    protected function has_one($associated_class_name, $foreign_key_name=null) {
        return $this->_has_one_or_many($associated_class_name, $foreign_key_name);
    }

    /**
     * Helper method to manage one-to-many relations where the foreign
     * key is on the associated table.
     */
    protected function has_many($associated_class_name, $foreign_key_name=null) {
        return $this->_has_one_or_many($associated_class_name, $foreign_key_name);
    }

    /**
     * Helper method to manage one-to-one and one-to-many relations where
     * the foreign key is on the base table.
     */
    protected function belongs_to($associated_class_name, $foreign_key_name=null) {
        $associated_table_name = self::_get_table_name($associated_class_name);
        $foreign_key_name = self::_build_foreign_key_name($foreign_key_name, $associated_table_name);
        $associated_object_id = $this->$foreign_key_name;
        return self::factory($associated_class_name)->where_id_is($associated_object_id);
    }

    /**
     * Helper method to manage many-to-many relationships via an intermediate model. See
     * README for a full explanation of the parameters.
     */
    protected function has_many_through($associated_class_name, $join_class_name=null, $key_to_base_table=null, $key_to_associated_table=null) {
        $base_class_name = get_class($this);

        // The class name of the join model, if not supplied, is
        // formed by concatenating the names of the base class
        // and the associated class, in alphabetical order.
        if (is_null($join_class_name)) {
            $class_names = array($base_class_name, $associated_class_name);
            sort($class_names, SORT_STRING);
            $join_class_name = join("", $class_names);
        }

        // Get table names for each class
        $base_table_name = self::_get_table_name($base_class_name);
        $associated_table_name = self::_get_table_name($associated_class_name);
        $join_table_name = self::_get_table_name($join_class_name);

        // Get ID column names
        $base_table_id_column = self::_get_id_column_name($base_class_name);
        $associated_table_id_column = self::_get_id_column_name($associated_class_name);

        // Get the column names for each side of the join table
        $key_to_base_table = self::_build_foreign_key_name($key_to_base_table, $base_table_name);
        $key_to_associated_table = self::_build_foreign_key_name($key_to_associated_table, $associated_table_name);

        return self::factory($associated_class_name)
            ->select("{$associated_table_name}.*")
            ->join($join_table_name, array("{$associated_table_name}.{$associated_table_id_column}", '=', "{$join_table_name}.{$key_to_associated_table}"))
            ->where("{$join_table_name}.{$key_to_base_table}", $this->id());
    }

    /**
     * Set the wrapped ORM instance associated with this Model instance.
     */
    public function set_orm($orm) {
        $this->orm = $orm;
    }

    /**
     * Magic getter method, allows $model->property access to data.
     */
    public function __get($property) {
        return $this->orm->get($property);
    }

    /**
     * Magic setter method, allows $model->property = 'value' access to data.
     */
    public function __set($property, $value) {
        $this->orm->set($property, $value);
    }

    /**
     * Magic isset method, allows isset($model->property) to work correctly.
     */
    public function __isset($property) {
        return $this->orm->__isset($property);
    }

    /**
     * Getter method, allows $model->get('property') access to data
     */
    public function get($property) {
        return $this->orm->get($property);
    }

    /**
     * Setter method, allows $model->set('property', 'value') access to data.
     */
    public function set($property, $value) {
        $this->orm->set($property, $value);
    }

    /**
     * Check whether the given field has changed since the object was created or saved
     */
    public function is_dirty($property) {
        return $this->orm->is_dirty($property);
    }

    /**
     * Wrapper for Idiorm's as_array method.
     */
    public function as_array() {
        $args = func_get_args();
        return call_user_func_array(array($this->orm, 'as_array'), $args);
    }

    /**
     * Save the data associated with this model instance to the database.
     */
    public function save() {
        return $this->orm->save();
    }

    /**
     * Delete the database row associated with this model instance.
     */
    public function delete() {
        return $this->orm->delete();
    }

    /**
     * Get the database ID of this model instance.
     */
    public function id() {
        return $this->orm->id();
    }

    /**
     * Hydrate this model instance with an associative array of data.
     * WARNING: The keys in the array MUST match with columns in the
     * corresponding database table. If any keys are supplied which
     * do not match up with columns, the database will throw an error.
     */
    public function hydrate($data) {
        $this->orm->hydrate($data)->force_all_dirty();
    }
}
