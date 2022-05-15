<?php

require_once 'Size.class.php';

/**
 * Instances of this class are used as parameters in web service calls for picking calculation and registration
 */
class ParamParcelInfo {

    /**
     * Parcel's serial number (2, 3, ...)
     * MANDATORY: YES
     * @var integer Signed 32-bit
     */
    private $_seqNo;

    /**
     * Parcel ID
     * MANDATORY: YES
     * @var integer Signed 64-bit
     */
    private $_parcelId;

    /**
     * Packing ID
     * MANDATORY: NO
     * @var integer Signed 64-bit
     */
    private $_packId;
    
    /**
     * Parcel size
     * MANDATORY: if pallet service is specified
     * @since 2.3.0
     */
    private $_size;
    
    /**
     * Parcel weight
     * MANDATORY: if pallet service is specified
     * @since 2.3.0
     */
    private $_weight;

    /**
     * Set parcel's serial number (2, 3, ...)
     * @param integer $seqNo Signed 32-bit
     */
    public function setSeqNo($seqNo) {
        $this->_seqNo = $seqNo;
    }

    /**
     * Get parcel's serial number
     * @return integer Signed 32-bit
     */
    public function getSeqNo() {
        return $this->_seqNo;
    }

    /**
     * Set parcel ID
     * @param integer $parcelId Signed 64-bit
     */
    public function setParcelId($parcelId) {
        $this->_parcelId = $parcelId;
    }

    /**
     * Get parcel ID
     * @return integer Signed 64-bit
     */
    public function getParcelId() {
        return $this->_parcelId;
    }

    /**
     * Set packing ID
     * @param integer $packId Signed 64-bit
     */
    public function setPackId($packId) {
        $this->_packId = $packId;
    }

    /**
     * Get packing ID
     * @return integer Signed 64-bit
     */
    public function getPackId() {
        return $this->_packId;
    }
    
    /**
     * Gets the parcel's size
     * @return Parcel's size
     * @since 2.3.0
     */
    public function getSize() {
    	return $this->_size;
    }
    
    /**
     * Sets parcel's size
     * @param Size $size Parcel's size
     * @since 2.3.0
     */
    public function setSize($size) {
    	 $this->_size = $size;
    }
    
    /**
     * Gets the parcel's weight
     * @return Parcel's weight
     * @since 2.3.0
     */
    public function getWeight() {
    	return $this->_weight;
    }
    
    /**
     * Sets parcel's weight
     * @param double $weight Parcel's weight
     * @since 2.3.0
     */
    public function setWeight($weight) {
    	$this->_weight = $weight;
    }

    /**
     * Return standard class from this class
     * @return stdClass
     */
    public function toStdClass() {
        $stdClass = new stdClass();
        $stdClass->seqNo    = $this->_seqNo;
        $stdClass->parcelId = $this->_parcelId;
        $stdClass->packId   = $this->_packId;
        $stdClass->size     = $this->_size;
        if (isset($this->_size)) {
        	$stdClass->size = $this->_size->toStdClass();
        }
        $stdClass->weight   = $this->_weight;
        return $stdClass;
    }
}
?>