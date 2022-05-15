
( function( $ ) {
	
	var DEFAULT_STREET_TYPE = '\u0443\u043B./\u0431\u0443\u043B.';   // Default street type - ул./бул.
	var DEFAULT_QUARTER_TYPE = '\u043A\u0432./\u0436\u043A';         // Default quarter type - кв./жк

	var address = null;
	var isOffice = false;
	var officeEnabled = true;
	
	$.widget( "ui.speedyAddress", {
		
		// These options will be used as defaults
        options: { 
        	siteIdInputFieldClass: "speedyAddressSiteId",
        	siteTypeInputFieldClass: "speedyAddressSiteType",
        	siteNameInputFieldClass: "speedyAddressSiteName",
        	postCodeInputFieldClass: "speedyAddressPostCode",
        	regionInputFieldClass: "speedyAddressRegion",
        	municipalityInputFieldClass: "speedyAddressMunicipality",
        	addrNomenInputFieldClass: "speedyAddressAddrNomen",
        	
        	streetIdInputFieldClass: "speedyAddressStreetId",
        	streetTypeInputFieldClass: "speedyAddressStreetType",
        	streetNameInputFieldClass: "speedyAddressStreetName",
        	streetActualNameInputFieldClass: "speedyAddressStreetActualName",
        	
        	quarterIdInputFieldClass: "speedyAddressQuarterId",
        	quarterTypeInputFieldClass: "speedyAddressQuarterType",
        	quarterNameInputFieldClass: "speedyAddressQuarterName",
        	quarterActualNameInputFieldClass: "speedyAddressQuarterActualName",
        	
        	streetNoInputFieldClass: "speedyAddressStreetNo",
        	blockNoInputFieldClass: "speedyAddressBlockNo",
        	entranceNoInputFieldClass: "speedyAddressEntranceNo",
        	floorNoInputFieldClass: "speedyAddressFloorNo",
        	apartmentNoInputFieldClass: "speedyAddressApartmentNo",
        	commonObjectIdInputFieldClass: "speedyAddressCommonObjectId",
        	commonObjectTypeInputFieldClass: "speedyAddressCommonObjectType",
        	commonObjectNameInputFieldClass: "speedyAddressCommonObjectName",
        	addressNoteInputFieldClass: "speedyAddressAddressNote",

        	toOfficeEnabled : true,
        	isOfficeInputFieldClass: "speedyAddressIsOffice",
        	officeIdInputFieldClass: "speedyAddressOfficeId",
        	officeNameInputFieldClass: "speedyAddressOfficeName",
        	officeWorkingTimeFromInputFieldClass: "speedyAddressOfficeWorkingTimeFrom",
        	officeWorkingTimeToInputFieldClass: "speedyAddressOfficeWorkingTimeTo",
        	officeWorkingTimeHalfFromInputFieldClass: "speedyAddressOfficeWorkingTimeHalfFrom",
        	officeWorkingTimeHalfToInputFieldClass: "speedyAddressOfficeWorkingTimeHalfTo",
        	officeFullAddressStringInputFieldClass: "speedyAddressOfficeFullAddressString",
        	
        	coordXInputFieldClass: "speedyAddressCoordX",
        	coordYInputFieldClass: "speedyAddressCoordY",
        	coordTypeIdInputFieldClass: "speedyAddressCoordTypeId",
        },
        
        widgetEventPrefix: 'eventSpeedyAddress',
        
        _create: function() {
        	address = {
        			site: {
        				id: null,
        				type: null,
        				name: null,
        				postCode: null,
        				region: null,
        				municipality: null,
        				addrNomen: null
        			},
        			quarter: {
        				id: null,
        				type: null,
        				name: null,
        				actualName: null
        			},
        			street: {
        				id: null,
        				type: null,
        				name: null,
        				actualName: null
        			},
        			streetNo: null,
        			blockNo: null,
        			entranceNo: null,
        			floorNo: null,
        			apartmentNo: null,
        			commonObject: {
        				id: null,
        				type: null,
        				name: null
        			},
        			addressNote: null,
        			coordX,
        			coordY,
        			coordTypeId
        	};
        },
        
        _init: function() {
        	setSite(null);
        },

        _destroy: function() {

            // Use the destroy method to reverse everything your plugin has applied
            return this._super();

        },
        
        _setSite: function(site) {
        		var el = this.element;
        		address.site = site;
        		if (site == null) {
        			el.find("." + this.siteIdInputFieldClass).val("");
        			el.find("." + this.siteNameInputFieldClass).val("");
        			el.find("." + this.siteTypeInputFieldClass).val("");
        			el.find("." + this.postCodeInputFieldClass).val("");
        			el.find("." + this.regionInputFieldClass).val("");
        			el.find("." + this.municipalityInputFieldClass).val("");
        			el.find("." + this.addrNomenInputFieldClass).val("");
        		} else {
        			el.find("." + this.siteIdInputFieldClass).val(site.id != null ? site.id : "");
        			el.find("." + this.siteNameInputFieldClass).val(site.name != null ? site.name.trim().toUpperCase() : "");
        			el.find("." + this.siteTypeInputFieldClass).val(site.type != null ? site.type.trim() : "");
        			el.find("." + this.postCodeInputFieldClass).val(site.postCode != null ? site.postCode.trim() : "");
        			el.find("." + this.regionInputFieldClass).val(site.region != null ? site.region.trim().toUpperCase() : "");
        			el.find("." + this.municipalityInputFieldClass).val(site.municipality != null ? site.municipality.trim().toUpperCase() : "");
        			el.find("." + this.addrNomenInputFieldClass).val(site.addrNomen != null ? site.addrNomen.trim().toUpperCase() : "");
        		}
        		_setQuarter(null);
        		_setStreet(null);
        		_setCommonObject(null);
        		_setAddressNote(null);
        		_setOffice(null);
        		_clearQuarterAndStreetDetails();
        	    
        		if (isOffice) {
        			el.find("." + this.quarterNameInputFieldClass).autocomplete("disable");
        			el.find("." + this.streetNameInputFieldClass).autocomplete("disable");
        			el.find("." + this.commonObjectNameInputFieldClass).autocomplete("disable");
        			el.find("." + this.blockNoInputFieldClass).autocomplete("disable");
        			el.find("." + this.officeNameInputFieldClass).autocomplete("enable");
        			  
        			el.find("." + this.quarterTypeInputFieldClass).attr("disabled", true).trigger("chosen:updated");
        			el.find("." + this.streetTypeInputFieldClass).attr("disabled", true).trigger("chosen:updated");
        			el.find("." + this.quarterNameInputFieldClass).attr("disabled", true);
        			el.find("." + this.streetNameInputFieldClass).attr("disabled", true);
        			el.find("." + this.streetNoInputFieldClass).attr("disabled", true);
        			el.find("." + this.blockNoInputFieldClass).attr("disabled", true);
        			el.find("." + this.entranceNoInputFieldClass).attr("disabled", true);
        			el.find("." + this.floorNoInputFieldClass).attr("disabled", true);
        			el.find("." + this.apartmentNoInputFieldClass).attr("disabled", true);
        			el.find("." + this.commonObjectNameInputFieldClass).attr("disabled", true);
        			el.find("." + this.addressNoteInputFieldClass).attr("disabled", true);
        			  
        			el.find("." + this.officeNameInputFieldClass).attr("disabled", false);
        			  
        		} else {
        			el.find("." + this.officeNameInputFieldClass).attr("disabled", true);
        			el.find("." + this.officeNameInputFieldClass).autocomplete("disable");

        			if (address.site == null) {
        				
        				el.find("." + this.quarterNameInputFieldClass).autocomplete("disable");
            			el.find("." + this.streetNameInputFieldClass).autocomplete("disable");
            			el.find("." + this.commonObjectNameInputFieldClass).autocomplete("disable");
            			el.find("." + this.blockNoInputFieldClass).autocomplete("disable");
        				
            			el.find("." + this.quarterTypeInputFieldClass).attr("disabled", true).trigger("chosen:updated");
            			el.find("." + this.streetTypeInputFieldClass).attr("disabled", true).trigger("chosen:updated");
            			el.find("." + this.quarterNameInputFieldClass).attr("disabled", true);
            			el.find("." + this.streetNameInputFieldClass).attr("disabled", true);
            			el.find("." + this.streetNoInputFieldClass).attr("disabled", true);
            			el.find("." + this.blockNoInputFieldClass).attr("disabled", true);
            			el.find("." + this.entranceNoInputFieldClass).attr("disabled", true);
            			el.find("." + this.floorNoInputFieldClass).attr("disabled", true);
            			el.find("." + this.apartmentNoInputFieldClass).attr("disabled", true);
            			el.find("." + this.commonObjectNameInputFieldClass).attr("disabled", true);
            			el.find("." + this.addressNoteInputFieldClass).attr("disabled", true);
        			} else {
        				el.find("." + this.commonObjectNameInputFieldClass).autocomplete("enable");
        				el.find("." + this.commonObjectNameInputFieldClass).attr("disabled", false);
        				if (site.addrNomen != 'NO') {
        					el.find("." + this.quarterNameInputFieldClass).autocomplete("enable");
        					el.find("." + this.streetNameInputFieldClass).autocomplete("enable");
        					el.find("." + this.blockNoInputFieldClass).autocomplete("enable");
        					if (site.addrNomen == 'FULL') {
        						el.find("." + this.quarterTypeInputFieldClass).attr("disabled", true).trigger("chosen:updated");
        						el.find("." + this.streetTypeInputFieldClass).attr("disabled", true).trigger("chosen:updated");
        					} else {
        						el.find("." + this.quarterTypeInputFieldClass).attr("disabled", false).trigger("chosen:updated");
        						el.find("." + this.streetTypeInputFieldClass).attr("disabled", false).trigger("chosen:updated");
        					}
        					el.find("." + this.quarterNameInputFieldClass).attr("disabled", false);
        					el.find("." + this.streetNameInputFieldClass).attr("disabled", false);
        					el.find("." + this.streetNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.blockNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.entranceNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.floorNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.apartmentNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.addressNoteInputFieldClass).attr("disabled", false);
        				} else {
        					el.find("." + this.quarterNameInputFieldClass).autocomplete("disable");
        					el.find("." + this.streetNameInputFieldClass).autocomplete("disable");
        					el.find("." + this.blockNoInputFieldClass).autocomplete("disable");
        					el.find("." + this.quarterTypeInputFieldClass).attr("disabled", false).trigger("chosen:updated");
        					el.find("." + this.streetTypeInputFieldClass).attr("disabled", false).trigger("chosen:updated");
        					el.find("." + this.quarterNameInputFieldClass).attr("disabled", false);
        					el.find("." + this.streetNameInputFieldClass).attr("disabled", false);
        					el.find("." + this.streetNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.blockNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.entranceNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.floorNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.apartmentNoInputFieldClass).attr("disabled", false);
        					el.find("." + this.addressNoteInputFieldClass).attr("disabled", false);
        				}
        			}
        		}
        	},
        	_setQuarterId: _function(quarterId) {
        		var quarterIdValue = quarterId != null ? quarterId : "";
        		this.element.find("." + this.quarterIdInputFieldClass).val(quarterIdValue);
        	},
        	_setQuarterType: function(quarterType) {
        		var quarterTypeValue = quarterType != null ? quarterType.trim() : DEFAULT_QUARTER_TYPE;
        		if (quarterTypeValue == "") {
        			quarterTypeValue = DEFAULT_QUARTER_TYPE;
        		}
        		this.element.find("." + this.quarterTypeInputFieldClass).val(quarterTypeValue).trigger("chosen:updated");
        	},
        	_setQuarterName: function(quarterName) {
        		this.element.find("." + this.quarterNameInputFieldClass).val(quarterName != null ? quarterName.trim().toUpperCase() : "")
        	},
        	_setQuarter: function(quarter) {
        		address.quarter = quarter;
        		if (quarter == null) {
        			_setQuarterId(null);
					_setQuarterType(null);
					_setQuarterName(null);
					this.element.find("." + this.quarterActualNameInputFieldClass).val("");
				} else {
					_setQuarterId(quarter.id);
					_setQuarterType(quarter.type);
					_setQuarterName(quarter.name);
					this.element.find("." + this.quarterActualNameInputFieldClass).val(quarter.actualName != null ? quarter.actualName.trim().toUpperCase() : "");
				}
        	},
        	_setStreetId: function(streetId) {
        		var streetIdValue = streetId != null ? streetId : "";
        		this.element.find("." + this.streetIdInputFieldClass).val(streetIdValue);
        	},
        	_setStreetType: function(streetType) {
        		var streetTypeValue = streetType != null ? streetType.trim() : DEFAULT_STREET_TYPE;
        		if (streetTypeValue == "") {
        			streetTypeValue = DEFAULT_STREET_TYPE;
        		}
        		this.element.find("." + this.streetTypeInputFieldClass).val(streetTypeValue).trigger("chosen:updated");
        	},
        	_setStreetName: function(streetName) {
        		this.element.find("." + this.streetNameInputFieldClass).val(streetName != null ? streetName.trim().toUpperCase() : "");
        	},
        	_setStreet: function(street) {
        		address.street = street;
        		if (street == null) {
        			_setStreetId(null);
        			_setStreetType(null);
        			_setStreetName(null);
        			this.element.find("." + this.streetActualNameInputFieldClass).val("");
        		} else {
        			_setStreetId(street.id);
        			_setStreetType(street.type);
        			_setStreetName(street.name);
        			this.element.find("." + this.streetActualNameInputFieldClass).val(street.actualName != null ? street.actualName.trim().toUpperCase() : "");
        		}

        	},
        	_setCommonObject: function(commonObject) {
        		address.commonObject = commonObject;
        		if (commonObject == null) {
        			this.element.find("." + this.commonObjectIdInputFieldClass).val("");
        			this.element.find("." + this.commonObjectTypeInputFieldClass).val("");
        			this.element.find("." + this.commonObjectNameInputFieldClass).val("");
        		} else {
        			this.element.find("." + this.commonObjectIdInputFieldClass).val(commonObject.id != null ? commonObject.id : "");
        			this.element.find("." + this.commonObjectTypeInputFieldClass).val(commonObject.type != null ? commonObject.type.trim().toUpperCase() : "");
        			this.element.find("." + this.commonObjectNameInputFieldClass).val(commonObject.name != null ? commonObject.name.trim().toUpperCase() : "");
        			_setAddressNote(commonObject.address);
        		}
        	},
        	_setAddressNote: function(addressNote) {
        		var addressNoteValue = addressNote != null ? addressNote.trim().toUpperCase() : "";
        		this.element.find("." + this.addressNoteInputFieldClass).val(addressNoteValue);
        	},
        	_setOffice: function(office) {
        		address.office = office;
        		if (office == null) {
        			this.element.find("." + this.officeIdInputFieldClass).val("");
        			this.element.find("." + this.officeNameInputFieldClass).val("");
        			this.element.find("." + this.officeWorkingTimeFromInputFieldClass).val("");
        			this.element.find("." + this.officeWorkingTimeToInputFieldClass).val("");
        			this.element.find("." + this.officeWorkingTimeHalfFromInputFieldClass).val("");
        			this.element.find("." + this.officeWorkingTimeHalfToInputFieldClass).val("");
        			this.element.find("." + this.officeFullAddressStringInputFieldClass).val("");
        			_setStreet(null);
        			_setQuarter(null);
        			_clearQuarterAndStreetDetails();
        			_setCommonObject(null);
        			_setAddressNote(null);
        			_setGPSCoords(null, null, null);
        		} else {
        			this.element.find("." + this.officeIdInputFieldClass).val(office.id != null ? office.id : "");
        			this.element.find("." + this.officeNameInputFieldClass).val(office.name != null ? office.name.trim().toUpperCase() : "");
        			this.element.find("." + this.officeWorkingTimeFromInputFieldClass).val(office.workingTimeFrom != null ? office.workingTimeFrom : "");
        			this.element.find("." + this.officeWorkingTimeToInputFieldClass).val(office.workingTimeTo != null ? office.workingTimeTo : "");
        			this.element.find("." + this.officeWorkingTimeHalfFromInputFieldClass).val(office.workingTimeHalfFrom != null ? office.workingTimeHalfFrom : "");
        			this.element.find("." + this.officeWorkingTimeHalfToInputFieldClass).val(office.workingTimeHalfTo != null ? office.workingTimeHalfTo : "");
        			this.element.find("." + this.officeFullAddressStringInputFieldClass).val(office.addressValue != null ? office.addressValue : "");
        			_setStreet(
        				{
        					id: (office.address != null ? office.address.streetId : ""),
        					type: (office.address != null ? office.address.streetType : ""),
        					name: (office.address != null ? office.address.streetName : ""),
        					actualName: ""
        				}
        			);
        			_setQuarter(
        				{
        					id: (office.address != null ? office.address.quarterId : ""),
        					type: (office.address != null ? office.address.quarterType : ""),
        					name: (office.address != null ? office.address.quarterName : ""),
        					actualName: ""
        				}
        			);
        			_setStreetNo(office.address != null ? office.address.streetNo : "");
        			_setBlock(office.address != null ? office.address.blockNo : "");
        			_setEntranceNo(office.address != null ? office.address.entranceNo : "");
        			_setFloor(office.address != null ? office.address.floorNo : "");
        			_setApartmentNo(office.address != null ? office.address.apartmentNo : "");
        			_setCommonObject(
        					office.address != null ? office.address.commonObjectId : "",
        					null,
        					office.address != null ? office.address.commonObjectName: ""
        			);
        			_setAddressNote(office.address != null ? office.address.addressNote : "");
        			_setGPSCoords(
        					office.address != null ? office.address.coordX : "", 
        					office.address != null ? office.address.coordY : "",
        					office.address != null ? office.address.coordTypeId : ""
        			);
        		}
        		if (officeEnabled) {
        			if (isOffice) {
        				this.element.find("." + this.isOfficeInputFieldClass).removeClass("disabledCombo").removeClass("uncheckedCombo").addClass("checkedCombo");
        			} else {
        				this.element.find("." + this.isOfficeInputFieldClass).removeClass("disabledCombo").removeClass("checkedCombo").addClass("uncheckedCombo");
        			}
        		} else {
        			this.element.find("." + this.isOfficeInputFieldClass).removeClass("checkedCombo").removeClass("uncheckedCombo").addClass("disabledCombo");
        		}
        	},
        	_setGPSCoords: function (coordX, coordY, coordTypeId) {
        		this.element.find("." + this.coordXInputFieldClass).val(coordX != null ? coordX : "");
        		this.element.find("." + this.coordYInputFieldClass).val(coordY != null ? coordY : "");
        		this.element.find("." + this.coordTypeIdInputFieldClass).val(coordTypeId != null ? coordTypeId : "");
        	},
        	_setStreetNo: function(streetNo) {
        		var streetNoValue = streetNo != null ? streetNo.trim().toUpperCase() : "";
        		this.element.find("." + this.streetNoInputFieldClass).val(streetNoValue);
        	},
        	_setBlockNo: function(blockNo) {
        		var blockNoValue = blockNo != null ? blockNo.trim().toUpperCase() : "";
        		this.element.find("." + this.blockNoInputFieldClass).val(blockNoValue);
        	},
        	_setEntranceNo: function(entranceNo) {
        		var entranceNoValue = entranceNo != null ? entranceNo.trim().toUpperCase() : "";
        		this.element.find("." + this.entranceNoInputFieldClass).val(entranceNoValue);
        	},
        	_setFloorNo: function(floorNo) {
        		var floorNoValue = floorNo != null ? floorNo.trim().toUpperCase() : "";
        		this.element.find("." + this.floorNoInputFieldClass).val(floorNoValue);
        	},
        	_setApartmentNo: function(apartmentNo) {
        		var apartmentNoValue = apartmentNo != null ? apartmentNo.trim().toUpperCase() : "";
        		this.element.find("." + this.apartmentNoInputFieldClass).val(apartmentNoValue);
        	},
        	_clearQuarterAndStreetDetails: function() {
        		_setBlock(null);
        		_setEntranceNo(null);
        		_setFloor(null);
        		_setApartmentNo(null);
        		_setStreetNo(null);
        	}
        }
	});
})( jQuery );

