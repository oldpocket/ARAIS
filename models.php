<?php

/**
 * This file extend a couple of RedBean models to provide default/automatic
 * behaviour, like save timestamp every time a bean is created or updated.
 */

// Save a timestamp in the DB everytime a Data table item is created or updated
class Model_Data extends RedBean_SimpleModel {
    public function update() {
        $this->bean->modified = date('Y-m-d G:i:s');
    }
    public function dispense() {
        $this->bean->created = date('Y-m-d G:i:s');
    }
}

// Save a timestamp in the DB everytime a Sensor table item is created or updated
class Model_Sensors extends RedBean_SimpleModel {
    public function update() {
        $this->bean->modified = date('Y-m-d G:i:s');
    }
    public function dispense() {
        $this->bean->created = date('Y-m-d G:i:s');
    }
}

// Save a timestamp in the DB everytime a Device table item is created or updated
class Model_Devices extends RedBean_SimpleModel {
    public function update() {
        $this->bean->modified = date('Y-m-d G:i:s');
    }
    public function dispense() {
        $this->bean->created = date('Y-m-d G:i:s');
    }
}
