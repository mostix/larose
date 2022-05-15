<?php

/**
 * Instances of this class are used as parameters to specify picking options before payment
 * @since 2.3.0
 */
class ParamOptionsBeforePayment {

   /**
	 * Open before payment option
	 * MANDATORY: NO
	 * @var boolean Flag
	 */
    private $_open;


    /**
     * Set open option before payment flag
     * @param boolean $open Open option before payment flag
     */
    public function setOpen($open) {
        $this->_open = $open;
    }

    /**
     * Get open option before payment flag
     * @return boolean Open option before payment flag
     */
    public function isOpen() {
        return $this->_open;
    }

    /**
     * Return standard class from this class
     * @return stdClass
     */
    public function toStdClass() {
        $stdClass = new stdClass();
        $stdClass->open = $this->_open;
        return $stdClass;
    }
}
?>