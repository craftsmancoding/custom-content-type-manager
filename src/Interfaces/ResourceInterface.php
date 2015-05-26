<?php namespace CCTM\Interfaces;
/**
 * Interface ResourceInterface
 *
 * A resource is a generic term for our file-based JSON data model.  Instead
 * of a resource class corresponding loosely to a database table, with our
 * file-based model, a resource class corresponds to a folder.  Otherwise the
 * pattern is similar: a resource assumes a defined set of attributes.
 * In the CCTM, there are resource classes for fields, post-types, filters,
 * and validators.
 *
 * @package CCTM\Interfaces
 */
interface ResourceInterface {

    public function getOne($id);
    public function getCollection(array $filters=array());
    public function delete();
    public function duplicate($new_id);
    public function rename($new_id);
    public function save();

}
/*EOF*/