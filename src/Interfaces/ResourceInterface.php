<?php namespace CCTM\Interfaces;
interface ResourceInterface {
    public function getOne($id);
    public function getCollection(array $filters=array());
    public function delete();
    public function duplicate($new_id);
    public function rename($new_id);
    public function save();

}
/*EOF*/