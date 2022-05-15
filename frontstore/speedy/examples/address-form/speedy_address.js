var DEFAULT_STREET_TYPE = '\u0443\u043B./\u0431\u0443\u043B.';   // Default street type - ул./бул.
var DEFAULT_QUARTER_TYPE = '\u043A\u0432./\u0436\u043A';         // Default quarter type - кв./жк

var AUTOCOMPLETE_DELAY                   = 700; // The delay in milliseconds between when a keystroke occurs and when a search is performed
var SITE_NAME_SEARCH_MIN_LENGTH          = 1;   // The minimum number of characters a user must type before a search is performed for site name
var STREET_NAME_SEARCH_MIN_LENGTH        = 1;   // The minimum number of characters a user must type before a search is performed for street name
var QUARTER_NAME_SEARCH_MIN_LENGTH       = 1;   // The minimum number of characters a user must type before a search is performed for quarter name
var COMMON_OBJECT_NAME_SEARCH_MIN_LENGTH = 1;   // The minimum number of characters a user must type before a search is performed for common object name
var BLOCK_SEARCH_MIN_LENGTH              = 1;   // The minimum number of characters a user must type before a search is performed for block
var OFFICE_NAME_SEARCH_MIN_LENGTH        = 0;   // The minimum number of characters a user must type before a search is performed for office name

var site;
var quarter;
var street;
var commonObject;
var office;
var isOffice = false;
var officeEnabled = true;
var streetTypesLoaded = false;

if (typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, ''); 
	}
}

function initAfterFormChange() {
	$("#message").text("");
	$("#varError").text("");
}

//**************************************************************************
// Series of on..Changed functions for tracing in varaibles log div  
//**************************************************************************

function onSiteChanged() {
	$("#varSiteId").text($("#siteId").val());
	$("#varSiteType").text($("#siteType").val());
	$("#varSiteName").text($("#siteName").val());
	$("#varPostCode").text($("#postCode").val());
	$("#varRegion").text($("#region").val());
	$("#varMunicipality").text($("#municipality").val());
	$("#varAddrNomen").text($("#addrNomen").val());
	initAfterFormChange();
}

function onAddressNoteChanged() {
	$("#varAddressNote").text($("#addressNote").val());
	initAfterFormChange();
}

function onQuarterIdChanged() {
	$("#varQuarterId").text($("#quarterId").val());
}

function onQuarterTypeChanged() {
	var quarterTypeValue = $("#quarterType").val();
	$("#varQuarterType").text(quarterTypeValue == DEFAULT_QUARTER_TYPE ? "" : (quarterTypeValue != null ? quarterTypeValue : ""));
}

function onQuarterNameChanged() {
	$("#varQuarterName").text($("#quarterName").val());
}

function onQuarterChanged() {
	onQuarterIdChanged();
	onQuarterTypeChanged();
	onQuarterNameChanged();
	$("#varQuarterActualName").text($("#quarterActualName").val());
}

function onStreetIdChanged() {
	$("#varStreetId").text($("#streetId").val());
}

function onStreetTypeChanged() {
	var streetTypeValue = $("#streetType").val();
	$("#varStreetType").text(streetTypeValue == DEFAULT_STREET_TYPE ? "" : (streetTypeValue != null ? streetTypeValue : "") );
}

function onStreetNameChanged() {
	$("#varStreetName").text($("#streetName").val());
}

function onStreetChanged() {
	onStreetIdChanged();
	onStreetTypeChanged();
	onStreetNameChanged();
	$("#varStreetActualName").text($("#streetActualName").val());
}

function onStreetNoChanged() {
	$("#varStreetNo").text($("#streetNo").val());
}

function onBlockChanged() {
	$("#varBlock").text($("#block").val());
}

function onEntranceNoChanged() {
	$("#varEntranceNo").text($("#entranceNo").val());
}

function onFloorChanged() {
	$("#varFloor").text($("#floor").val());
}

function onApartmentNoChanged() {
	$("#varApartmentNo").text($("#apartmentNo").val());
}

function onOfficeChanged() {
	var workTime
	$("#varOfficeId").text($("#officeId").val());
	$("#varOfficeName").text($("#officeName").val());
	if ($.trim(($("#officeWorkingTimeFrom").val() + $("#officeWorkingTimeTo").val())) == "") {
		workTime = "";
	} else {
		workTime =  $("#officeWorkingTimeFrom").val() + " - " + $("#officeWorkingTimeTo").val();
	}
	$("#varOfficeWorkingTime").text(workTime);
	if ($.trim(($("#officeWorkingTimeHalfFrom").val() + $("#officeWorkingTimeHalfTo").val())) == "") {
		workTime = "";
	} else {
		workTime = $("#officeWorkingTimeHalfFrom").val() + " - " + $("#officeWorkingTimeHalfTo").val();
	}
	$("#varOfficeWorkingTimeHalf").text(workTime);
	$("#varOfficeFullAddressString").text($("#officeFullAddressString").val());
}

function onCommonObjectChanged() {
	$("#varCommonObjectId").text($("#commonObjectId").val());
	$("#varCommonObjectType").text($("#commonObjectType").val());
	$("#varCommonObjectName").text($("#commonObjectName").val());
}

function onGPSCoordsChanged() {
	$("#varCoordX").text($("#coordX").val());
	$("#varCoordY").text($("#coordY").val());
	$("#varCoordTypeId").text($("#coordTypeId").val());
}

//**************************************************************************

function setAddressNote(addressNote, initAfterFormChangeFlag) {
	var addressNoteValue = addressNote != null ? addressNote.trim().toUpperCase() : "";
	$( "#addressNote" ).val(addressNoteValue);
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onAddressNoteChanged();
}


function setSite(item) {
	site = item;
	if (site == null) {
		$( "#siteName" ).val("");
		$( "#siteType" ).val("");
		$( "#siteId" ).val("");
		$( "#postCode" ).val("");
		$( "#region" ).val("");
		$( "#municipality" ).val("");
		$(" #addrNomen ").val("");
	} else {
		$( "#siteName" ).val(site.name != null ? site.name.trim().toUpperCase() : "");
		$( "#siteType" ).val(site.type != null ? site.type.trim() : "");
		$( "#siteId" ).val(site.id != null ? site.id : "");
		$( "#postCode" ).val(site.postCode != null ? site.postCode.trim() : "");
		$( "#region" ).val(site.region != null ? site.region.trim().toUpperCase() : "");
		$( "#municipality" ).val(site.municipality != null ? site.municipality.trim().toUpperCase() : "");
		$( "#addrNomen" ).val(site.addrNomen != null ? site.addrNomen.trim().toUpperCase() : "");
	}
	setQuarter(null, false);
	setStreet(null, false);
	setCommonObject(null, false);
	setAddressNote("", false);
	setOffice(null, false);
	clearQuarterAndStreetDetails();
    
	if (isOffice) {
		$("#quarterName").autocomplete("disable");
		$("#streetName").autocomplete("disable");
		$("#commonObjectName").autocomplete("disable");
		$("#block").autocomplete("disable");
		$("#officeName").autocomplete("enable");
		  
		$("#quarterType").attr("disabled", true).trigger("chosen:updated");
		$("#streetType").attr("disabled", true).trigger("chosen:updated");
		$("#quarterName").attr("disabled", true);
		$("#streetName").attr("disabled", true);
		$("#streetNo").attr("disabled", true);
		$("#block").attr("disabled", true);
		$("#entranceNo").attr("disabled", true);
		$("#floor").attr("disabled", true);
		$("#apartmentNo").attr("disabled", true);
		$("#commonObjectName").attr("disabled", true);
		$("#addressNote").attr("disabled", true);
		  
		$("#officeName").attr("disabled", false);
		  
	} else {
		$("#officeName").attr("disabled", true);
		$("#officeName").autocomplete("disable");
		  
		if (site == null) {
			$("#quarterName").autocomplete("disable");
			$("#streetName").autocomplete("disable");
			$("#commonObjectName").autocomplete("disable");
			$("#block").autocomplete("disable");
			$("#quarterType").attr("disabled", true).trigger("chosen:updated");
			$("#streetType").attr("disabled", true).trigger("chosen:updated");
			$("#quarterName").attr("disabled", true);
			$("#streetName").attr("disabled", true);
			$("#streetNo").attr("disabled", true);
			$("#block").attr("disabled", true);
			$("#entranceNo").attr("disabled", true);
			$("#floor").attr("disabled", true);
			$("#apartmentNo").attr("disabled", true);
			$("#commonObjectName").attr("disabled", true);
			$("#addressNote").attr("disabled", true);
		} else {
			$("#commonObjectName").autocomplete("enable");
			$("#commonObjectName").attr("disabled", false);
			if (site.addrNomen != 'NO') {
				$("#quarterName").autocomplete("enable");
				$("#streetName").autocomplete("enable");
				$("#block").autocomplete("enable");
				if (site.addrNomen == 'FULL') {
					$("#quarterType").attr("disabled", true).trigger("chosen:updated");
					$("#streetType").attr("disabled", true).trigger("chosen:updated");
				} else {
					$("#quarterType").attr("disabled", false).trigger("chosen:updated");
					$("#streetType").attr("disabled", false).trigger("chosen:updated");
				}
				$("#quarterName").attr("disabled", false);
				$("#streetName").attr("disabled", false);
				$("#streetNo").attr("disabled", false);
				$("#block").attr("disabled", false);
				$("#entranceNo").attr("disabled", false);
				$("#floor").attr("disabled", false);
				$("#apartmentNo").attr("disabled", false);
				$("#addressNote").attr("disabled", false);
			} else {
				$("#quarterName").autocomplete("disable");
				$("#streetName").autocomplete("disable");
				$("#block").autocomplete("disable");
				$("#quarterType").attr("disabled", false).trigger("chosen:updated");
				$("#streetType").attr("disabled", false).trigger("chosen:updated");
				$("#quarterName").attr("disabled", false);
				$("#streetName").attr("disabled", false);
				$("#streetNo").attr("disabled", false);
				$("#block").attr("disabled", false);
				$("#entranceNo").attr("disabled", false);
				$("#floor").attr("disabled", false);
				$("#apartmentNo").attr("disabled", false);
				$("#addressNote").attr("disabled", false);
			}
		}
	}
	
	onSiteChanged();
}

function setQuarterId(quarterId, initAfterFormChangeFlag) {
	var quarterIdValue = quarterId != null ? quarterId : "";
	$("#quarterId").val(quarterIdValue);
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onQuarterIdChanged();
}

function setQuarterType(quarterType, initAfterFormChangeFlag) {
	var quarterTypeValue = quarterType != null ? $.trim(quarterType) : DEFAULT_QUARTER_TYPE;
	if (quarterTypeValue == "") {
		quarterTypeValue = DEFAULT_QUARTER_TYPE;
	}
	$("#quarterType").val(quarterTypeValue).trigger("chosen:updated");
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onQuarterTypeChanged();
}

function setQuarterName(quarterName, initAfterFormChangeFlag) {
	$( "#quarterName" ).val(quarterName != null ? $.trim(quarterName).toUpperCase() : "");
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onQuarterNameChanged();
}

function setQuarter(item, initAfterFormChangeFlag) {
	quarter = item;
	if (item == null) {
		setQuarterId(null, false);
		setQuarterType(null, false);
		setQuarterName(null, false);
		$( "#quarterActualName" ).val("");
		//clearQuarterDetails();
	} else {
		setQuarterId(item.id, false);
		setQuarterType(item.type, false);
		setQuarterName(item.name, false);
		$( "#quarterActualName" ).val(item.actualName != null ? $.trim(item.actualName).toUpperCase() : "");
	}
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onQuarterChanged();
}


function setStreetId(streetId, initAfterFormChangeFlag) {
	var streetIdValue = streetId != null ? streetId : "";
	$("#streetId").val(streetIdValue);
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onStreetIdChanged();
}

function setStreetType(streetType, initAfterFormChangeFlag) {
	var streetTypeValue = streetType != null ? streetType.trim() : DEFAULT_STREET_TYPE;
	if (streetTypeValue == "") {
		streetTypeValue = DEFAULT_STREET_TYPE;
	}
	$("#streetType").val(streetTypeValue).trigger("chosen:updated");
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onStreetTypeChanged();
}


function setStreetName(streetName, initAfterFormChangeFlag) {
	$( "#streetName" ).val(streetName != null ? streetName.trim().toUpperCase() : "");
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onStreetNameChanged();
}

function setStreetNo(streetNo, initAfterFormChangeFlag) {
	var streetNoValue = streetNo != null ? streetNo.trim().toUpperCase() : "";
	$("#streetNo").val(streetNoValue);
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onStreetNoChanged();
}

function setBlock(block, initAfterFormChangeFlag) {
	var blockValue = block != null ? block.trim().toUpperCase() : "";
	$("#block").val(blockValue);
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onBlockChanged();
}

function setEntranceNo(entranceNo, initAfterFormChangeFlag) {
	var entranceNoValue = entranceNo != null ? entranceNo.trim().toUpperCase() : "";
	$("#entranceNo").val(entranceNoValue);
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onEntranceNoChanged();
}

function setFloor(floor, initAfterFormChangeFlag) {
	var floorValue = floor != null ? floor.trim().toUpperCase() : "";
	$("#floor").val(floorValue);
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onFloorChanged();
}

function setApartmentNo(apartmentNo, initAfterFormChangeFlag) {
	var apartmentNoValue = apartmentNo != null ? apartmentNo.trim().toUpperCase() : "";
	$("#apartmentNo").val(apartmentNoValue);
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onApartmentNoChanged();
}

function setStreet(item, initAfterFormChangeFlag) {
	street = item;
	if (item == null) {
		setStreetId(null, false);
		setStreetType(null, false);
		setStreetName(null, false);
		$( "#streetActualName" ).val("");
		//clearStreetDetails();
	} else {
		setStreetId(item.id, false);
		setStreetType(item.type, false);
		setStreetName(item.name, false);
		$( "#streetActualName" ).val(item.actualName != null ? item.actualName.trim().toUpperCase() : "");
	}
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onStreetChanged();
}

//function clearStreetDetails() {
//	setStreetNo("", false);
//}

//function clearQuarterDetails() {
//	setBlock(null, false);
//	setEntranceNo(null, false);
//	setFloor(null, false);
//	setApartmentNo(null, false);
//}

function clearQuarterAndStreetDetails() {
	setBlock(null, false);
	setEntranceNo(null, false);
	setFloor(null, false);
	setApartmentNo(null, false);
	setStreetNo(null, false);
}

function setGPSCoords(coordX, coordY, coordTypeId, initAfterFormChangeFlag) {
	$( "#coordX" ).val(coordX != null ? coordX : "");
	$( "#coordY" ).val(coordY != null ? coordY : "");
	$( "#coordTypeId" ).val(coordTypeId != null ? coordTypeId : "");
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onGPSCoordsChanged();
}

function setOffice(item, initAfterFormChangeFlag) {
	office = item;
	if (office == null) {
		$( "#officeId" ).val("");
		$( "#officeName" ).val("");
		$( "#officeWorkingTimeFrom" ).val("");
		$( "#officeWorkingTimeTo" ).val("");
		$( "#officeWorkingTimeHalfFrom" ).val("");
		$( "#officeWorkingTimeHalfTo" ).val("");
		$( "#officeFullAddressString" ).val("");
		setStreet(null, false);
		setQuarter(null, false);
		//clearStreetDetails();
		//clearQuarterDetails();
		clearQuarterAndStreetDetails();
		setCommonObject(null, false);
		setAddressNote(null, false);
		setGPSCoords(null, null, null, false);
	} else {
		$( "#officeId" ).val(office.id != null ? office.id : "");
		$( "#officeName" ).val(office.name != null ? office.name.trim().toUpperCase() : "");
		$( "#officeWorkingTimeFrom" ).val(office.workingTimeFrom != null ? office.workingTimeFrom : "");
		$( "#officeWorkingTimeTo" ).val(office.workingTimeTo != null ? office.workingTimeTo : "");
		$( "#officeWorkingTimeHalfFrom" ).val(office.workingTimeHalfFrom != null ? office.workingTimeHalfFrom : "");
		$( "#officeWorkingTimeHalfTo" ).val(office.workingTimeHalfTo != null ? office.workingTimeHalfTo : "");
		$( "#officeFullAddressString" ).val(office.addressValue != null ? office.addressValue : "");
		setStreet(
			{
				id: (office.address != null ? office.address.streetId : ""),
				type: (office.address != null ? office.address.streetType : ""),
				name: (office.address != null ? office.address.streetName : ""),
				actualName: ""
			}, false
		);
		setQuarter(
			{
				id: (office.address != null ? office.address.quarterId : ""),
				type: (office.address != null ? office.address.quarterType : ""),
				name: (office.address != null ? office.address.quarterName : ""),
				actualName: ""
			});
		setStreetNo(office.address != null ? office.address.streetNo : "", false);
		setBlock(office.address != null ? office.address.blockNo : "");
		setEntranceNo(office.address != null ? office.address.entranceNo : "", false);
		setFloor(office.address != null ? office.address.floorNo : "", false);
		setApartmentNo(office.address != null ? office.address.apartmentNo : "", false);
		setCommonObject(
				office.address != null ? office.address.commonObjectId : "",
				null,
				office.address != null ? office.address.commonObjectName: ""
		);
		setAddressNote(office.address != null ? office.address.addressNote : "", false);
		setGPSCoords(
				office.address != null ? office.address.coordX : "", 
				office.address != null ? office.address.coordY : "",
				office.address != null ? office.address.coordTypeId : "",
				false
		);
	}
	if (officeEnabled) {
		if (isOffice) {
			$("#isOffice").removeClass("disabledCombo").removeClass("uncheckedCombo").addClass("checkedCombo");
		} else {
			$("#isOffice").removeClass("disabledCombo").removeClass("checkedCombo").addClass("uncheckedCombo");
		}
	} else {
		$("#isOffice").removeClass("checkedCombo").removeClass("uncheckedCombo").addClass("disabledCombo");
	}
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onOfficeChanged();
}

function setCommonObject(item, initAfterFormChangeFlag) {
	commonObject = item;
	if (commonObject == null) {
		$( "#commonObjectId" ).val("");
		$( "#commonObjectType" ).val("");
		$( "#commonObjectName" ).val("");
	} else {
		$( "#commonObjectId" ).val(item.id != null ? item.id : "");
		$( "#commonObjectType" ).val(item.type != null ? item.type.trim().toUpperCase() : "");
		$( "#commonObjectName" ).val(item.name != null ? item.name.trim().toUpperCase() : "");
		setAddressNote(item.address);
	}
	if (initAfterFormChangeFlag) {
		initAfterFormChange();
	}
	onCommonObjectChanged();
}

$(function() {
	$( "#siteName" ).autocomplete({
		columns: [
		          {name: "Тип", width: '30px', valueField: 'type'},
		          {name: 'Име', width: '120px', valueField: 'name'},
		          {name: 'Община', width: '110px', valueField: 'municipality'},
		          {name: 'Област', width: '100px', valueField: 'region'},
		          {name: 'Пощ. к.', width: '45px', valueField: 'postCode'}
		          ],
		source: function( request, response ) {
			$.ajax({
				type: "POST",
				url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
				timeout: 5000,
				dataType: "json",
				data: {
					username: $("#username").val(),
					password: $("#password").val(),
					method: "listSites",
					siteType: "",
					siteName: request.term.trim()
				},
				success: function( data ) {
					json = eval(data);
					if (json.status != 0) {
						$("#message").text(json.message);
						return {};
					} else {
						response( $.map( json.result, function( item ) {
							var itemName = item.name != null ? item.name.trim().toUpperCase() : "";
							return {
								value: itemName,
								type: item.type != null ? item.type.trim() : "",
								id: item.id != null ? item.id : "",
								name: itemName,
								municipality: item.municipality != null ? item.municipality.trim().toUpperCase() : "",
								region: item.region != null ? item.region.trim().toUpperCase() : "",
								postCode: item.postCode != null ? item.postCode.trim() : "",
								addrNomen: item.addrNomen != null ? item.addrNomen : ""
							}
						}));
					}
				},
				error: function() {
		    		$("#message").text("Timeout or communication error on retreiving sites");
				}
			});
		},
		minLength: SITE_NAME_SEARCH_MIN_LENGTH,
		delay: AUTOCOMPLETE_DELAY, 
		select: function( event, ui ) {
			if (site == null && ui.item != null || site != null && (ui.item == null || site.id != ui.item.id)) {
				setSite(ui.item); 
			}
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		},
		autoFocus: true
	});
 
	$( "#siteName" ).on("blur", function() {
		var siteNameValue = $( "#siteName" ).val().trim().toUpperCase();
		var siteName = site != null ? site.name : "";
		if (siteName != siteNameValue) {
			setSite(null);
		}
	});

	$( "#siteName" ).data( "uiAutocomplete" )._renderMenu = function(ul, items) {
		var self = this, thead;
		var itemHTML = '';
		table = $('<div class="ui-widget-header" style="width:100%;"></div>');
		$.each(this.options.columns, function(index, item) {
			itemHTML += '<span style="padding:0;float:left;width:' + item.width + ';">' + item.name + '</span>';
		});
		table.append('<div style="width:100%;padding: 2px 0.4em;">' + itemHTML + '<div style="clear: both;"></div></div>');
		ul.append(table);
		$.each(items, function(index, item) {
			self._renderItem(ul, item);
		});
	};
	
	$( "#siteName" ).data( "uiAutocomplete" )._renderItem = function(ul, item) {
		var itemHTML = '';
		$.each(this.options.columns, function(index, column) {
			var value = item[column.valueField ? column.valueField : index];
			if (value == null || value == "") {
				value = '&nbsp;';
			}
			itemHTML += '<span style="padding:0;float:left;width:' + column.width + ';">' + value + '</span>'
	    });
		return $('<li></li>').data('ui-autocomplete-item', item).append('<a>' + itemHTML + '<div style="clear: both;"></div></a>').appendTo(ul);
	};
});

$(function() {
	$( "#streetName" ).autocomplete({
		columns: [
		          {name: "Тип", width: '30px', valueField: 'type'},
		          {name: 'Име', width: '220px', valueField: 'name'},
		          {name: 'Ново име', width: '220px', valueField: 'actualName'}
		          ],
		source: function( request, response ) {
			$.ajax({
				type: "POST",
				url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
				timeout: 5000,
				dataType: "json",
				data: {
					username: $("#username").val(),
					password: $("#password").val(),
					method: "listStreets",
					name: request.term.trim(),
					siteId: site.id
				},
				success: function( data ) {
					json = eval(data);
					if (json.status != 0) {
						$("#message").text(json.message);
						return {};
					} else {
						response( $.map( json.result, function( item ) {
							var itemName = item.name != null ? item.name.trim().toUpperCase() : "";
							return {
								id: item.id != null ? item.id : "",
								type: item.type != null ? item.type.trim() : "",
								name: itemName,
								actualName: item.actualName != null ? item.actualName.trim().toUpperCase() : "",
								value: itemName
							}
						}));
					}
				},
				error: function() {
		    		$("#message").text("Timeout or communication error on retreiving streets");
				}
			});
		},
		minLength: STREET_NAME_SEARCH_MIN_LENGTH,
		delay: AUTOCOMPLETE_DELAY,
		select: function( event, ui ) {
			if (street == null && ui.item != null || street != null && (ui.item == null || street.id != ui.item.id)) {
				setStreet(ui.item, true); 
			}
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		},
		autoFocus: true
	});

	$( "#streetName" ).on("blur", function() {
		var streetNameValue = $( "#streetName" ).val().trim().toUpperCase();
		var streetName = street != null ? street.name : "";
		if (site != null && site.addrNomen == "FULL" && streetName != streetNameValue) {
			setStreet(null, true);
		} else {
			if (site != null && site.addrNomen != "NO" && streetName != streetNameValue) {
				street = null;
				setStreetId(null, false);
			}
			setStreetName(streetNameValue, true);
		}
	});
	
	$( "#streetName" ).data( "uiAutocomplete" )._renderMenu = function(ul, items) {
		var self = this, thead;
		var itemHTML = '';
		table = $('<div class="ui-widget-header" style="width:100%;"></div>');
		$.each(this.options.columns, function(index, item) {
			itemHTML += '<span style="padding:0;float:left;width:' + item.width + ';">' + item.name + '</span>';
		});
		table.append('<div style="width:100%;padding: 2px 0.4em;">' + itemHTML + '<div style="clear: both;"></div></div>');
		ul.append(table);
		$.each(items, function(index, item) {
			self._renderItem(ul, item);
		});
	};
	
	$( "#streetName" ).data( "uiAutocomplete" )._renderItem = function(ul, item) {
		var itemHTML = '';
		$.each(this.options.columns, function(index, column) {
			var value = item[column.valueField ? column.valueField : index];
			if (value == null || value == "") {
				value = '&nbsp;';
			}
			itemHTML += '<span style="padding:0;float:left;width:' + column.width + ';">' + value + '</span>'
	    });
		return $('<li></li>').data('ui-autocomplete-item', item).append('<a>' + itemHTML + '<div style="clear: both;"></div></a>').appendTo(ul);
	};

});


$(function() {
	$( "#quarterName" ).autocomplete({
		columns: [
		          {name: "Тип", width: '35px', valueField: 'type'},
		          {name: 'Име', width: '220px', valueField: 'name'},
		          {name: 'Ново име', width: '200px', valueField: 'actualName'}
		          ],
		source: function( request, response ) {
			$.ajax({
				type: "POST",
				url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
				timeout: 5000,
				dataType: "json",
				data: {
					username: $("#username").val(),
					password: $("#password").val(),
					method: "listQuarters",
					name: request.term.trim(),
					siteId: site.id
				},
				success: function( data ) {
					json = eval(data);
					if (json.status != 0) {
						$("#message").text(json.message);
						return {};
					} else {
						response( $.map( json.result, function( item ) {
							var itemName = item.name != null ? item.name.trim().toUpperCase() : "";
							return {
								id: item.id != null ? item.id : "",
								type: item.type != null ? item.type.trim() : "",
								name: itemName,
								actualName: item.actualName != null ? item.actualName.trim().toUpperCase() : "",
								value: itemName
							}
						}));
					}
				},
				error: function() {
		    		$("#message").text("Timeout or communication error on retreiving quarters");
				}
			});
		},
		minLength: QUARTER_NAME_SEARCH_MIN_LENGTH,
		delay: AUTOCOMPLETE_DELAY,
		select: function( event, ui ) {
			if (quarter == null && ui.item != null || quarter != null && (ui.item == null || quarter.id != ui.item.id)) {
				setQuarter(ui.item, true); 
			}
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		},
		autoFocus: true
	});

	$( "#quarterName" ).on("blur", function() {
		var quarterNameValue = $( "#quarterName" ).val().trim().toUpperCase();
		var quarterName = quarter != null ? quarter.name : "";
		if (site != null && site.addrNomen == "FULL" && quarterName != quarterNameValue) {
			setQuarter(null, true);
		} else { 
			if (site != null && site.addrNomen != "NO" && quarterName != quarterNameValue) {
				quarter = null;
				setQuarterId(null, false);
			}
			setQuarterName(quarterNameValue, true);
		}
	});
	
	$( "#quarterName" ).data( "uiAutocomplete" )._renderMenu = function(ul, items) {
		var self = this, thead;
		var itemHTML = '';
		table = $('<div class="ui-widget-header" style="width:100%;"></div>');
		$.each(this.options.columns, function(index, item) {
			itemHTML += '<span style="padding:0;float:left;width:' + item.width + ';">' + item.name + '</span>';
		});
		table.append('<div style="width:100%;padding: 2px 0.4em;">' + itemHTML + '<div style="clear: both;"></div></div>');
		ul.append(table);
		$.each(items, function(index, item) {
			self._renderItem(ul, item);
		});
	};
	
	$( "#quarterName" ).data( "uiAutocomplete" )._renderItem = function(ul, item) {
		var itemHTML = '';
		$.each(this.options.columns, function(index, column) {
			var value = item[column.valueField ? column.valueField : index];
			if (value == null || value == "") {
				value = '&nbsp;';
			}
			itemHTML += '<span style="padding:0;float:left;width:' + column.width + ';">' + value + '</span>'
	    });
		return $('<li></li>').data('ui-autocomplete-item', item).append('<a>' + itemHTML + '<div style="clear: both;"></div></a>').appendTo(ul);
	};
});


$(function() {
	$( "#commonObjectName" ).autocomplete({
		columns: [
		          {name: "Тип", width: '180px', valueField: 'type'},
		          {name: 'Име', width: '320px', valueField: 'name'},
		          {name: 'Адрес', width: '280px', valueField: 'address'}
		          ],
		source: function( request, response ) {
			$.ajax({
				type: "POST",
				url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
				timeout: 5000,
				dataType: "json",
				data: {
					username: $("#username").val(),
					password: $("#password").val(),
					method: "listCommonObjects",
					name: request.term.trim(),
					siteId: site.id
				},
				success: function( data ) {
					json = eval(data);
					if (json.status != 0) {
						$("#message").text(json.message);
						return {};
					} else {
						response( $.map( json.result, function( item ) {
							var itemName = item.name != null ? item.name.trim().toUpperCase() : "";
							return {
								id: item.id != null ? item.id : "",
								type: item.type != null ? item.type.trim() : "",
								name: itemName,
								address: item.address != null ? item.address.trim().toUpperCase() : "",
								value: itemName
							}
						}));
					}
				},
				error: function() {
		    		$("#message").text("Timeout or communication error on retreiving common objects");
				}
			});
		},
		minLength: COMMON_OBJECT_NAME_SEARCH_MIN_LENGTH,
		delay: AUTOCOMPLETE_DELAY,
		select: function( event, ui ) {
			if (commonObject == null && ui.item != null || commonObject != null && (ui.item == null || commonObject.id != ui.item.id)) {
				setCommonObject(ui.item, true); 
			}
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		},
		autoFocus: true
	});

	$( "#commonObjectName" ).on("blur", function() {
		var commonObjectNameValue = $( "#commonObjectName" ).val().trim().toUpperCase();
		if (site != null) {
			var commonObjectName = commonObject != null ? commonObject.name : "";
			if (commonObjectName != commonObjectNameValue) {
				setCommonObject(null, true);
			}
		}
	});
	
	$( "#commonObjectName" ).data( "uiAutocomplete" )._renderMenu = function(ul, items) {
		var self = this, thead;
		var itemHTML = '';
		table = $('<div class="ui-widget-header" style="width:100%;"></div>');
		$.each(this.options.columns, function(index, item) {
			itemHTML += '<span style="padding:0;float:left;width:' + item.width + ';">' + item.name + '</span>';
		});
		table.append('<div style="width:100%;padding: 2px 0.4em;">' + itemHTML + '<div style="clear: both;"></div></div>');
		ul.append(table);
		$.each(items, function(index, item) {
			self._renderItem(ul, item);
		});
	};
	
	$( "#commonObjectName" ).data( "uiAutocomplete" )._renderItem = function(ul, item) {
		var itemHTML = '';
		$.each(this.options.columns, function(index, column) {
			var value = item[column.valueField ? column.valueField : index];
			if (value == null || value == "") {
				value = '&nbsp;';
			}
			itemHTML += '<span style="padding:0;float:left;width:' + column.width + ';">' + value + '</span>'
	    });
		return $('<li></li>').data('ui-autocomplete-item', item).append('<a>' + itemHTML + '<div style="clear: both;"></div></a>').appendTo(ul);
	};
});

$(function() {
	$( "#block" ).autocomplete({
		columns: [
		          {name: "Име/номер на блок", width: '200px', valueField: 'name'}
		         ],
		source: function( request, response ) {
			$.ajax({
				type: "POST",
				url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
				timeout: 5000,
				dataType: "json",
				data: {
					username: $("#username").val(),
					password: $("#password").val(),
					method: "listBlocks",
					name: request.term.trim(),
					siteId: site.id
				},
				success: function( data ) {
					json = eval(data);
					if (json.status != 0) {
						$("#message").text(json.message);
						return {};
					} else {
						response( $.map( json.result, function( item ) {
							var itemName = item.name != null ? item.name.trim().toUpperCase() : "";
							return {
								name: itemName,
								value: itemName
							}
						}));
					}
				},
				error: function() {
		    		$("#message").text("Timeout or communication error on retreiving blocks");
				}
			});
		},
		minLength: BLOCK_SEARCH_MIN_LENGTH,
		delay: AUTOCOMPLETE_DELAY,
		select: function( event, ui ) {
			setBlock(ui.item.label, true);
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		},
		autoFocus: true
	});
	
	$( "#block" ).on("blur", function() {
		setBlock($( "#block" ).val(), true);
	});
	
	$( "#block" ).data( "uiAutocomplete" )._renderMenu = function(ul, items) {
		var self = this, thead;
		var itemHTML = '';
		table = $('<div class="ui-widget-header" style="width:100%;"></div>');
		$.each(this.options.columns, function(index, item) {
			itemHTML += '<span style="padding:0;float:left;width:' + item.width + ';">' + item.name + '</span>';
		});
		table.append('<div style="width:100%;padding: 2px 0.4em;">' + itemHTML + '<div style="clear: both;"></div></div>');
		ul.append(table);
		$.each(items, function(index, item) {
			self._renderItem(ul, item);
		});
	};
	
	$( "#block" ).data( "uiAutocomplete" )._renderItem = function(ul, item) {
		var itemHTML = '';
		$.each(this.options.columns, function(index, column) {
			var value = item[column.valueField ? column.valueField : index];
			if (value == null || value == "") {
				value = '&nbsp;';
			}
			itemHTML += '<span style="padding:0;float:left;width:' + column.width + ';">' + value + '</span>'
	    });
		return $('<li></li>').data('ui-autocomplete-item', item).append('<a>' + itemHTML + '<div style="clear: both;"></div></a>').appendTo(ul);
	};

});

$(function() {
	$( "#officeName" ).autocomplete({
		columns: [
		          {name: "\u2116", width: '35px', valueField: 'id'},
		          {name: 'Име', width: '210px', valueField: 'name'},
		          {name: 'Адрес', width: '350px', valueField: 'addressValue'}
		          ],
		source: function( request, response ) {
			$.ajax({
				type: "POST",
				url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
				dataType: "json",
				data: {
					username: $("#username").val(),
					password: $("#password").val(),
					method: /*"listOffices"*/"listOfficesEx",
					name: request.term.trim(),
					siteId: (site != null && site.id != 0 ? site.id : null)
				},
				success: function( data ) {
					json = eval(data);
					if (json.status != 0) {
						$("#message").text(json.message);
						return {};
					} else {
						response( $.map( json.result, function( item ) {
							var itemName = item.name != null ? item.name.trim().toUpperCase() : "";
							return {
								value: itemName,
								id: item.id != null ? item.id : "",
								name: itemName,
								workingTimeFrom: item.workingTimeFrom != null ? item.workingTimeFrom : "",
								workingTimeTo: item.workingTimeTo != null ? item.workingTimeTo : "",
								workingTimeHalfFrom: item.workingTimeHalfFrom != null ? item.workingTimeHalfFrom : "",
								workingTimeHalfTo: item.workingTimeHalfTo != null ? item.workingTimeHalfTo : "",
								address: item.address,
								addressValue: item.address.fullAddressString != null ? item.address.fullAddressString.trim().toUpperCase() : ""
								/*addressValue:  
									(item.address.site.type != null ?item.address.site.type + " ": "") +
									(item.address.site.name != null ? item.address.site.name: "") +
									(item.address.quarterType != null ? " " + item.address.quarterType: "") +
									(item.address.quarterName != null ? " " + item.address.quarterName: "") +
									(item.address.streetType != null ? " " + item.address.streetType: "") +
									(item.address.streetName != null ? " " + item.address.streetName: "") +
									(item.address.streetNo != null ? " \u2116" + item.address.streetNo: "") +
									(item.address.blockNo != null ? " \u0431\u043B." + item.address.blockNo: "") +
									(item.address.entranceNo != null ? " \u0432\u0445." + item.address.entranceNo: "") +
									(item.address.floorNo != null ? " \u0435\u0442." + item.address.floorNo: "") +
									(item.address.apartmentNo != null ? " \u0430\u043F." + item.address.apartmentNo: "") +
									(item.address.commonObjectName != null ? " " + item.address.commonObjectName: "") +
									(item.address.addressNote != null ? ", " + item.address.addressNote: "")
									*/
							}
						}));
					}
				},
				error: function() {
		    		$("#message").text("Timeout or communication error on retreiving offices");
				}
			});
		},
		minLength: OFFICE_NAME_SEARCH_MIN_LENGTH,
		delay: AUTOCOMPLETE_DELAY,
		select: function( event, ui ) {
			var officeSite = (ui.item != null ? ui.item.address.site : null);
			if (site == null && officeSite != null || site != null && site.id != officeSite.id) {
				setSite(officeSite);
				setOffice(ui.item, true);
				return;
			}
			if (office == null && ui.item != null || office != null && (ui.item == null || office.id != ui.item.id)) {
				setOffice(ui.item, true); 
			}
		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		},
		autoFocus: true
	});
	
	$( "#officeName" ).on("blur", function() {
		var officeNameValue = $( "#officeName" ).val().trim().toUpperCase();
		var officeName = office != null ? office.name : "";
		if (officeName != officeNameValue) {
			setOffice(null, true);
		}
	});
	
	$( "#officeName" ).data( "uiAutocomplete" )._renderMenu = function(ul, items) {
		ul.css("overflow-y", "scroll");
		var self = this, thead;
		var itemHTML = '';
		table = $('<div class="ui-widget-header" style="width:100%;"></div>');
		$.each(this.options.columns, function(index, item) {
			itemHTML += '<span style="padding:0;float:left;width:' + item.width + ';">' + item.name + '</span>';
		});
		table.append('<div style="width:100%;padding: 2px 0.4em;">' + itemHTML + '<div style="clear: both;"></div></div>');
		ul.append(table);
		$.each(items, function(index, item) {
			self._renderItem(ul, item);
		});
	};
	
	$( "#officeName" ).data( "uiAutocomplete" )._renderItem = function(ul, item) {
		var itemHTML = '';
		$.each(this.options.columns, function(index, column) {
			var value = item[column.valueField ? column.valueField : index];
			if (value == null || value == "") {
				value = '&nbsp;';
			}
			itemHTML += '<span style="padding:0;float:left;width:' + column.width + ';">' + value + '</span>'
	    });
		return $('<li></li>').data('ui-autocomplete-item', item).append('<a>' + itemHTML + '<div style="clear: both;"></div></a>').appendTo(ul);
	};
});
	
$(function() {
	$( "#clearButton" ).button().click(function( event ) {
        event.preventDefault();
        isOffice = false;
        setSite(null);
	});
});

$(function() {
	$( "#validateButton" ).button().click(function( event ) {
        event.preventDefault();
        $("#message").text("");
        $.ajax({
        	type: "POST",
        	async: "false",
			url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
			timeout: 5000,
			dataType: "json",
			data: {
				username: $("#username").val(),
				password: $("#password").val(),
				method: "validateAddress",
				siteId: (site == null ? 0 : site.id) ,
				streetName: $("#streetName").val(),
				streetType: ($("#streetType").val() == DEFAULT_STREET_TYPE ? null : $("#streetType").val()),
				streetId: (street == null? 0 : street.id),
				quarterName: $("#quarterName").val(),
				quarterType: ($("#quarterType").val() == DEFAULT_QUARTER_TYPE ? null : $("#quarterType").val()),
				quarterId: (quarter == null? 0 : quarter.id),
				streetNo: $("#streetNo").val(),
				blockNo: $("#block").val(),
				entranceNo: $("#entranceNo").val(),
				floorNo: $("#floor").val(),
				apartmentNo: $("#apartmentNo").val(),
				addressNote: $("#addressNote").val(),
				commonObjectId: (commonObject == null? 0 : commonObject.id),
				coordX: null,
				coordY: null
			},
			success: function( data ) {
				json = eval(data);
				if (json.status == 0) {
					$("#message").text("Address is OK");
				} else if (json.status == 1) {
					$("#message").text(json.message);
				} else if (json.status == 2) {
					$("#message").text(json.message);
					$("#varError").text(json.exception);
				}
				return true;
			},
			error: function() {
	    		$("#message").text("Timeout or communication error on validation request");
			}
		});
	});
});


$( document ).ready(function() {
	
	jQuery.browser={};(function(){jQuery.browser.msie=false;
	jQuery.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
	jQuery.browser.msie=true;jQuery.browser.version=RegExp.$1;}})();

	jQuery(".chosen").chosen({disable_search_threshold: 20});
	
//	if ($("#username").val().trim() != "") {
//		$.ajax({
//			type: "POST",
//			url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
//          timeout: 5000, 
//			dataType: "json",
//			async: "false",
//			data: {
//				username: $("#username").val(),
//				password: $("#password").val(),
//				method: "listStreetTypes",
//			},
//			success: function( data ) {
//				json = eval(data);
//				if (json.status != 0) {
//					$("#message").text(json.message);
//				} else {
//					$("#streetType").html('');
//					$("#streetType").append('<option value="' + DEFAULT_STREET_TYPE + '">' + DEFAULT_STREET_TYPE + '</option>');
//					$(json.result).each(function(){
//						$("#streetType").append('<option value="' + this.name + '">' + this.name + '</option>');
//					});
//					$("#streetType").trigger("chosen:updated");
//				}
//			},
//			error: function() {
//				$("#message").text("Timeout or communication error on retreiving street types");
//			}
//		});
//		
//		$.ajax({
//			type: "POST",
//			url: "/frontstore/speedy/examples/address-form/speedy_address_soap.php",
//			timeout: 5000,
//			dataType: "json",
//			async: "false",
//			data: {
//				username: $("#username").val(),
//				password: $("#password").val(),
//				method: "listQuarterTypes",
//			},
//			success: function( data ) {
//				json = eval(data);
//				if (json.status != 0) {
//					$("#message").text(json.message);
//				} else {
//					$("#quarterType").html('');
//					$("#quarterType").append('<option value="' + DEFAULT_QUARTER_TYPE + '">' + DEFAULT_QUARTER_TYPE + '</option>');
//					$(json.result).each(function(){
//						$("#quarterType").append('<option value="' + this.name + '">' + this.name + '</option>');
//					});
//					$("#quarterType").trigger("chosen:updated");
//					streetTypesLoaded = true;
//				}
//			},
//			error: function() {
//				$("#message").text("Timeout or communication error on retreiving quarter types");
//			}
//		});
//	}
	
	setSite(null);
	
	$("#siteName").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#quarterName").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#streetName").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#streetNo").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#commonObjectName").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#addressNote").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#block").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#entranceNo").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#floor").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#apartmentNo").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#officeName").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
	});
	
	$("#isOfficeInput").on("keydown", function (e) {
		if(e.which == 13) { // Checks for the enter key
	        return false;
	    }
		if(e.which == 32) { // Checks for the space
			//FF duplicates this event eith click event on isOffice element
			if (!(navigator.userAgent.indexOf("Firefox") > 0)) {
				isOffice = !isOffice;
				setSite(site);
				return false;
			}
		}
	});
	
	$("#isOfficeInput").on("click", function (e) {
		e.preventDefault();
	});
	
	$("#isOffice").on("click", function (e) {
		e.preventDefault();
		if (!officeEnabled) {
			return;
		}
		$("#isOfficeInput").focus();
		isOffice = !isOffice;
		setSite(site);
	});
	
	$("#isOfficeInput").on("focus", function() {
		if (officeEnabled) {
			$(this).parent().addClass("focusedElement");
		}
    });
	$("#isOfficeInput").on('blur', function() {
		if (officeEnabled) {
			$(this).parent().removeClass("focusedElement");
		}
    });
	
	$("#entranceNo").on('blur', function() {
		setEntranceNo($("#entranceNo").val(), true);
    });
	
	$("#floor").on('blur', function() {
		setFloor($("#floor").val(), true);
    });
	
	$("#apartmentNo").on('blur', function() {
		setApartmentNo($("#apartmentNo").val(), true);
    });
	
	$("#streetNo").on('blur', function() {
		setStreetNo($("#streetNo").val(), true);
    });
	
	$("#streetType").on('blur', function() {
		setStreetType($( "#streetType" ).val(), true);
    });
	
	$("#streetType").chosen().change(function() {
		var streetTypeValue = $( "#streetType" ).val().trim();
		var streetType = street != null ? street.type : "";
		if (site != null && site.addrNomen == "PARTIAL" && streetType != streetTypeValue) {
			street = null;
			setStreetId(null, false);
		}
		setStreetType(streetTypeValue, true);
    });
	
	$("#quarterType").on('blur', function() {
		setQuarterType($( "#quarterType" ).val(), true);
    });
	
	$("#quarterType").chosen().change(function() {
		var quarterTypeValue = $( "#quarterType" ).val().trim();
		var quarterType = quarter != null ? quarter.type : "";
		if (site != null && site.addrNomen == "PARTIAL" && quarterType != quarterTypeValue) {
			quarter = null;
			setQuarterId(null, false);
		}
		setQuarterType(quarterTypeValue, true);
    });
	
	$("#addressNote").on('blur', function() {
		setAddressNote($("#addressNote").val());
    });
});

