<?php
/**
 * Persistable interface
 */
interface IPersistable {
    public function load($id);
    public function save();
    public function destroy();
}
?>