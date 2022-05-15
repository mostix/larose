<?php

require_once 'AddrNomen.class.php';

/**
 * ResultSite instances are returned as a result of sites speedy web service requests
 */
class ResultSite {

    /**
     * Site ID
     * @access private
     * @var integer Signed 64-bit
     */
    private $_id;

    /**
     * Site type
     * @access private
     * @var string
     */
    private $_type;

    /**
     * Site name
     * @access private
     * @var string
     */
    private $_name;

    /**
     * Site municipality name
     * @access private
     * @var string
     */
    private $_municipality;

    /**
     * Site region name
     * @access private
     * @var string
     */
    private $_region;

    /**
     * Site post code
     * @access private
     * @var string
     */
    private $_postCode;

    /**
     * Site address nomenclature.
     * Specifies if speedy have (or have not) address nomenclature (streets, quarters etc.) for this site
     * @access private
     * @var AddrNomen
     */
    private $_addrNomen;

    /**
     * Constructs new instance of ResultSite
     * @param stdClass $stdClassResultSite
     */
    function __construct($stdClassResultSite) {
        $this->_id            = isset($stdClassResultSite->id)           ? $stdClassResultSite->id                       : null;
        $this->_type          = isset($stdClassResultSite->type)         ? $stdClassResultSite->type                     : null;
        $this->_name          = isset($stdClassResultSite->name)         ? $stdClassResultSite->name                     : null;
        $this->_municipality  = isset($stdClassResultSite->municipality) ? $stdClassResultSite->municipality             : null;
        $this->_region        = isset($stdClassResultSite->region)       ? $stdClassResultSite->region                   : null;
        $this->_postCode      = isset($stdClassResultSite->postCode)     ? $stdClassResultSite->postCode                 : null;
        $this->_addrNomen     = isset($stdClassResultSite->addrNomen)    ? new AddrNomen($stdClassResultSite->addrNomen) : null;
    }

    /**
     * Get site ID
     * @return integer Signed 64-bit
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Get site type
     * @return string Site type
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Get site name
     * @return string Site name
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Get site municipality
     * @return string Site municipality
     */
    public function getMunicipality() {
        return $this->_municipality;
    }

    /**
     * Get site region
     * @return string Site region
     */
    public function getRegion() {
        return $this->_region;
    }

    /**
     * Get site post code
     * @return string Site post code
     */
    public function getPostCode() {
        return $this->_postCode;
    }

    /**
     * Get site address nomenclature
     * @return string Site address nomenclature
     */
    public function getAddrNomen() {
        return $this->_addrNomen;
    }
}
?>